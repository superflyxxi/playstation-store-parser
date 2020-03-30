<?php
include_once "cache/CacheObject.php";
include_once "Debugger.php";

abstract class Cache
{

    abstract protected function newObject($decodedJson): CacheObject;

    protected function __construct($filename)
    {
        $this->cacheFileName = $filename;
    }

    private $cache = array();

    private $cacheFileName = "";

    private function getCacheFileName()
    {
        return $this->cacheFileName;
    }

    public function load()
    {
        Debugger::debug("Loading cache from ", $this->getCacheFileName());
        $this->reset();
        if (file_exists($this->getCacheFileName())) {
            $local = json_decode(file_get_contents($this->getCacheFileName()));
            foreach ($local as $json) {
                $obj = $this->newObject(json_decode($json));
                $this->add($obj);
            }
        }
        Debugger::info("Loaded ", count($this->cache), " previous objects into cache from ", $this->getCacheFileName(), ".");
    }

    public function save()
    {
        Debugger::debug("Saving cache ", $this->getCacheFileName());
        file_put_contents($this->getCacheFileName(), json_encode($this->cache));
    }

    public function get(string $key)
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        } else {
            return NULL;
        }
    }

    public function add(CacheObject $obj)
    {
        $this->cache[$obj->getCacheKey()] = $obj;
    }

    public function remove(CacheObject $obj)
    {
        unset($this->cache[$obj->getCacheKey()]);
    }

    protected function reset()
    {
        $this->cache = array();
    }

    protected function addAll(array $arr)
    {
        foreach ($arr as $obj) {
            $this->add($obj);
        }
    }

    protected function getAll()
    {
        return $this->cache;
    }

    public function size()
    {
        return count($this->cache);
    }
}

?>
