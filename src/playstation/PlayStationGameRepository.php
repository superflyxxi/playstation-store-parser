<?php
include_once "playstation/PlayStationGame.php";

class PlayStationGameRepository
{

    private $mapGames = array();

    private static $instance = NULL;

    private function __construct()
    {}

    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new PlayStationGameRepository();
        }
        return self::$instance;
    }

    public function addGame(PlayStationGame $game)
    {
        if (! array_key_exists($game->getID(), $this->mapGames)) {
            $this->mapGames[$game->getID()] = $game;
            return $game;
        }
        return $this->mapGames[$game->getID()];
    }

    public function getGame($id)
    {
        if (array_key_exists($id, $this->mapGames)) {
            return $this->mapGames[$id];
        }
        return NULL;
    }

    public function getAllGames()
    {
        return $this->mapGames;
    }
}

?>

