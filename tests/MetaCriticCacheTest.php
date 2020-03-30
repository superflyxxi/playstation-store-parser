<?php
include_once "../src/Debugger.php";
include_once "../src/cache/MetaCriticCache.php";
include_once "../src/metacritic/MetaCriticObject.php";

use PHPUnit\Framework\TestCase;

final class MetaCriticCacheTest extends TestCase
{

    public function test_01_No_Cache()
    {
        $this->assertEquals(0, MetaCriticCache::getInstance()->size(), "Size of cache");
    }

    public function test_02_Adding_And_Reloading()
    {
        $obj = array();
        $obj["refTypeId"] = 30;
        $obj["metaScore"] = 90;
        $obj["url"] = "https://metacritic.com/playstation-4/someGame";
        $obj["requestedGame"] = "Req Some Game";
        $obj["name"] = "Some Game";

        MetaCriticCache::getInstance()->add(new MetaCriticObject(json_decode(json_encode($obj))));

        $this->assertEquals(1, MetaCriticCache::getInstance()->size(), "Size of cache after adding one");

        $obj = MetaCriticCache::getInstance()->get("Req Some Game");
        $this->assert02TestObject($obj);

        MetaCriticCache::getInstance()->save();

        $obj = array();
        $obj["refTypeId"] = 30;
        $obj["metaScore"] = 100;
        $obj["url"] = "https://metacritic.com/playstation-4/someOtherGame";
        $obj["requestedGame"] = "Req Some Other Game";
        $obj["name"] = "Some Other Game";
        MetaCriticCache::getInstance()->add(new MetaCriticObject(json_decode(json_encode($obj))));

        $this->assertEquals(2, MetaCriticCache::getInstance()->size(), "Size of cache after adding second");

        $this->assertTrue(file_exists("/usr/local/apache2/htdocs/caches/metacritic_cache.json"), "File not saved");
        Debugger::verbose("File contents: ", file_get_contents("/usr/local/apache2/htdocs/caches/metacritic_cache.json"));

        MetaCriticCache::getInstance()->load();
        $this->assertEquals(1, MetaCriticCache::getInstance()->size(), "Size of cache after loading");

        $obj = MetaCriticCache::getInstance()->get("Req Some Game");
        $this->assert02TestObject($obj);
    }

    private function assert02TestObject($obj)
    {
        $this->assertFalse($obj == NULL, "Object not found");
        $this->assertEquals("Some Game", $obj->getName(), "Name");
        $this->assertEquals("Req Some Game", $obj->getCacheKey(), "Cache Key");
        $this->assertEquals("https://metacritic.com/playstation-4/someGame", $obj->getUrl(), "URL");
        $this->assertEquals(90, $obj->getScore(), "Meta Score");
        $this->assertTrue($obj->isGame(), "Is a Game");
    }
}
?>

