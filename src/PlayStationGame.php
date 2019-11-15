<?php
include_once "Metacritic.php";
include_once "Debugger.php";

class PlayStationGame
{

    private $actualName = "";

    private $shortName = "";

    private $originalPrice = 0.00;

    private $salePrice = 0.00;

    private $storeUrl = "";

    private $arrPlatform = array();

    private $metaCriticLoaded = false;

    private $metaCriticScore = - 1;

    private $metaCriticUrl = "";

    private $url = "";

    private $id = "";

    private $gameContentTypes = array();

    function __construct($json)
    {
        if (array_key_exists("url", $json)) {
            $this->url = $json->url;
        }
        $this->originalPrice = 0.0;
        $this->id = $json->id;
        $this->actualName = $json->name;
        $arr = array();
        $gameName = $json->name;
        Debugger::verbose("PlayStation Game Name: ", $gameName);
        $gameName = str_replace("’", "'", $gameName); // replace weird apostrophe with normal '
        preg_match_all("/[A-Za-z0-9\-'&+!:.]+/", $gameName, $arr);
        $this->shortName = implode($arr[0], " ");
        $this->shortName = str_replace(" :", ":", $this->shortName); // remove space before :
        Debugger::verbose("Converted Game Name: ", $this->shortName);
        if (isset($json->playable_platform)) {
	    foreach ($json->playable_platform as $platform) {
            	$arr = array();
            	preg_match_all("/[A-Za-z0-9-\':\.]+/", $platform, $arr);
            	$this->arrPlatform[] = implode($arr[0], " ");
	    }
        }
        if (isset($json->default_sku)) {
            $this->originalPrice = $json->default_sku->price / 100;
            $this->salePrice = $this->originalPrice;
            foreach ($json->default_sku->rewards as $singleReward) {
                $this->salePrice = min($this->salePrice, $singleReward->price / 100);
                if (isset($singleReward->bonus_price)) {
                    $this->salePrice = min($this->salePrice, $singleReward->bonus_price / 100);
                }
            }
        }
        /*
         * "gameContentTypesList": [
         * {
         * "name": "Full Game",
         * "key": "FULL_GAME"
         * },
         * {
         * "name": "PS Now",
         * "key": "ps4_cloud"
         * },
         * {
         * "name": "PS Now",
         * "key": "cloud"
         * },
         * ]
         */
        if (isset($json->gameContentTypesList)) {
            foreach ($json->gameContentTypesList as $content) {
                $this->gameContentTypes[] = $content->key;
            }
        }
    }

    public function getID()
    {
        return $this->id;
    }

    public function getShortName()
    {
        return $this->shortName;
    }

    public function getPlatforms()
    {
        return $this->arrPlatform;
    }

    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    public function getSalePrice()
    {
        return $this->salePrice;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function isPSNow()
    {
        return in_array("cloud", $this->gameContentTypes) || in_array("ps4_cloud", $this->gameContentTypes);
    }

    public function getGameContentTypes()
    {
        return $this->gameContentTypes;
    }

    private function loadMetaCriticDataIfNecessary()
    {
        if (! $this->metaCriticLoaded) {
            $arrSystems = array(
                "playstation-4",
                "pc",
                "playstation-2"
            );
            $testName = $this->shortName;
            $testName = preg_replace("/\.\.\./", " ", $testName);
            $testName = rtrim(trim($testName), ":");
            Debugger::debug("Testing Metacritic for ", $testName);
            $mcApi = new Metacritic($testName);
            try {
                $mcResult = $mcApi->find();
                if (isset($mcResult["url"])) {
                    $this->metaCriticScore = $mcResult["metaScore"];
                    $this->metaCriticUrl = $mcResult["url"];
                }
                $this->metaCriticLoaded = true;
                Debugger::debug("Loaded metacritic score for \"", $this->shortName, "\" = ", $this->metaCriticScore);
            } catch (Exception $e) {
                Debugger::error("Got an error (", $e->getMessage(), ") while getting score for ", $testName);
                Debugger::debug("Skipping this for now.");
            }
        }
    }

    public function getMetaCriticScore()
    {
        $this->loadMetaCriticDataIfNecessary();
        return $this->metaCriticScore;
    }

    public function getMetaCriticURL()
    {
        $this->loadMetaCriticDataIfNecessary();
        return $this->metaCriticUrl;
    }
}

?>

