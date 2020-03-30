<?php
include_once "Properties.php";
include_once "Debugger.php";
include_once "cache/Cache.php";

class PlayStationGameCache extends Cache
{

    private static $instance = NULL;

    public static function setInstance(PlayStationGameCache $cache)
    {
        self::$instance = $cache;
    }

    public static function getInstance(): PlayStationGameCache
    {
        return self::$instance;
    }

    public function __construct()
    {
        parent::__construct(Properties::getProperty("psnow.cache.file", "psnow_cache.json"));
    }

    public function replace(array $new)
    {
        Debugger::debug("Replacing cache");
        $this->reset();
        $this->addAll($new);
    }

    public function getGamesNotInCache($otherArray)
    {
        Debugger::debug("Getting difference");
        return array_udiff($otherArray, $this->getAll(), 'comparePlayStationGame');
    }

    protected function newObject($decodedJson): CacheObject
    {
        return new PlayStationGame($decodedJson);
    }
}

function comparePlayStationGame(PlayStationGame $alpha, PlayStationGame $beta)
{
    return strcmp($alpha->getID(), $beta->getID());
}

PlayStationGameCache::setInstance(new PlayStationGameCache());

?>

