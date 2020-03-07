<?php
include_once "Properties.php";
include_once "Debugger.php";

class PlayStationGameCache
{

    public static $cache = array();

    const version = 1;

    public static function load()
    {
        Debugger::debug("Loading cache");
        self::$cache = array();
        if (file_exists(self::getCacheFileName())) {
            $local = json_decode(file_get_contents(self::getCacheFileName()));
            foreach ($local as $json) {
                $game = new PlayStationGame(json_decode($json));
                self::$cache[$game->getID()] = $game;
            }
        }
        Debugger::info("Fetched ", count(self::$cache), " previous games.");
    }

    public static function replace($new)
    {
        Debugger::debug("Replacing cache");
        self::$cache = $new;
    }

    public static function save()
    {
        Debugger::debug("Saving cache");
        file_put_contents(self::getCacheFileName(), json_encode(self::$cache));
    }

    public static function getGamesNotInCache($otherArray)
    {
        Debugger::debug("Getting difference");
        return array_udiff($otherArray, self::$cache, 'comparePlayStationGame');
    }

    private static function getCacheFileName()
    {
        return Properties::getProperty("psnow.cache.file", "psnow_cache.json");
    }
}

function comparePlayStationGame(PlayStationGame $alpha, PlayStationGame $beta)
{
    return strcmp($alpha->getID(), $beta->getID());
}

?>
