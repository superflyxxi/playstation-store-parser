<?php
include_once "cache/Cache.php";

class MetaCriticCache extends Cache
{

    private static $instance = NULL;

    public static function setInstance(MetaCriticCache $cache)
    {
        self::$instance = $cache;
    }

    public static function getInstance(): MetaCriticCache
    {
        return self::$instance;
    }

    public function __construct()
    {
        parent::__construct(Properties::getProperty("metacritic.cache.file", "metacritic_cache.json"));
    }

    protected function newObject($decodedJson): CacheObject
    {
        return new MetaCriticObject($decodedJson);
    }
}

MetaCriticCache::setInstance(new MetaCriticCache());
MetaCriticCache::getInstance()->load();

?>
