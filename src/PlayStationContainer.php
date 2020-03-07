<?php
include_once "Debugger.php";
include_once "PlayStationGame.php";
include_once "PlayStationGameRepository.php";
include_once "PlayStationContainerRepository.php";
include_once "PlayStationGameFilter.php";

class PlayStationContainer
{

    private $id = "";

    private $url = "";

    private $arrContainers = NULL;

    private $arrGames = NULL;

    private $gameFilter = NULL;

    function __construct($url, $gameFilter = NULL)
    {
        $this->url = $url;
        $this->gameFilter = $gameFilter;
        $this->loadData();
    }

    public function getID()
    {
        return $this->id;
    }

    public function getGamesIDs()
    {
        if (NULL == $this->arrGames) {
            $this->loadData();
        }
        return $this->arrGames;
    }

    function getContainerIDs()
    {
        if (NULL == $this->arrContainers) {
            $this->loadData();
        }
        return $this->arrContainers;
    }

    function loadData()
    {
        $this->arrContainers = array();
        $this->arrGames = array();
        if (strpos($this->url, '?') !== false) {
            $url = $this->url . "&";
        } else {
            $url = $this->url . "?";
        }
        $url = $url . "size=50";
        $gameList = array();
        $json = file_get_contents($url);
        $sale = json_decode($json);
        if ($sale == NULL) {
            Debugger::info("Invalid URL ", $url);
            return;
        }
        $this->id = $sale->id;
        if (NULL != PlayStationContainerRepository::getInstance()->getContainer($this->id)) {
            // i have already been loaded, don't load.
            Debugger::info($sale->id, " already loaded.");
            return;
        }
        
        $total = $sale->total_results;
        $current = 1;
        
        do {
            
            foreach ($sale->links as $entry) {
                
                if (isset($entry->container_type))
                    switch ($entry->container_type) {
                        case "container":
                            $container = new PlayStationContainer($entry->url, $this->gameFilter);
                            $container = PlayStationContainerRepository::getInstance()->addContainer($container);
                            $this->arrContainers[] = $container->getID();
                            break;
                        
                        case "product":
                            $game = new PlayStationGame($entry);
                            if (NULL == $this->gameFilter || $this->gameFilter->meetsCriteria($game)) {
                                Debugger::verbose($game->getShortName(), " considered for container");
                                $game = PlayStationGameRepository::getInstance()->addGame($game);
                                $this->arrGames[] = $game->getID();
                            }
                            break;
                        
                        default:
                            // container_type
                            break;
                    }
                Debugger::debug($current, " out of ", $total, " completed.");
                $current ++;
            }
            $json = file_get_contents($url . "&start=" . $current);
            $sale = json_decode($json);
        } while ($current < $total);
        Debugger::debug("Sale ID: ", $sale->id);
        Debugger::debug("Total: ", $total);
        Debugger::debug("Total of " . count($this->arrGames) . " games and " . count($this->arrContainers) . " collections found.");
    }
}

