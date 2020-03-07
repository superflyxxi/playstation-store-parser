<?php
include_once "../src/Debugger.php";
include_once "../src/PlayStationGame.php";
include_once "../src/cache/PlayStationGameCache.php";
include_once "helpers/PlayStationGameHelper.php";

use PHPUnit\Framework\TestCase;

final class PlayStationGameCacheTest extends TestCase
{

    public function test_01_NoCache()
    {
        PlayStationGameCache::load();
        $this->assertEquals(0, count(PlayStationGameCache::$cache), "Size of cache");
    }

    public function test_02_BasicEndToEnd()
    {
        $arr = array();
        
        $game = new GameJSON();
        $game->url = "https://store/product/123";
        $game->id = "123";
        $game->name = "Game";
        $game->playable_platform[] = "PS4";
        $game->default_sku = new SKUJSON();
        $game->default_sku->price = 1000;
        $arr[$game->id] = new PlayStationGame(json_decode(json_encode($game)));
        
        $game = new GameJSON();
        $game->url = "https://store/product/234";
        $game->id = "234";
        $game->name = "Game2";
        $game->playable_platform[] = "PS4";
        $game->default_sku = new SKUJSON();
        $game->default_sku->price = 2000;
        $arr[$game->id] = new PlayStationGame(json_decode(json_encode($game)));
        
        Debugger::verbose("Array before replacement", $arr);
        
        PlayStationGameCache::replace($arr);
        $this->assertEquals($arr, PlayStationGameCache::$cache, "Cach updated in memory");
        
        PlayStationGameCache::save();
        $this->assertTrue(file_exists("/usr/local/apache2/htdocs/entries/psnow_cache.json"), "File saved");
        Debugger::verbose("File contents: ", file_get_contents("/usr/local/apache2/htdocs/entries/psnow_cache.json"));
        
        PlayStationGameCache::replace(array());
        $this->assertEquals(0, count(PlayStationGameCache::$cache), "Reset");
        
        PlayStationGameCache::load();
        $this->assertEquals(2, count(PlayStationGameCache::$cache), "Size of new cache");
        
        $test = PlayStationGameCache::$cache["123"];
        Debugger::verbose("First entry in cache: ", $test);
        $this->assertEquals("123", $test->getID(), "ID");
        $this->assertEquals("https://store/product/123", $test->getURL(), "URL");
        $this->assertEquals("Game", $test->getShortName(), "ShortName");
        $this->assertEquals("PS4", $test->getPlatforms()[0], "Platform[0]");
        $this->assertEquals(10, $test->getOriginalPrice(), "OriginalPrice");
        $this->assertEquals(10, $test->getSalePrice(), "SalePrice");
        
        $test = PlayStationGameCache::$cache["234"];
        Debugger::verbose("Second entry in cache: ", $test);
        $this->assertEquals("234", $test->getID(), "ID");
        $this->assertEquals("https://store/product/234", $test->getURL(), "URL");
        $this->assertEquals("Game2", $test->getShortName(), "ShortName");
        $this->assertEquals("PS4", $test->getPlatforms()[0], "Platform[0]");
        $this->assertEquals(20, $test->getOriginalPrice(), "OriginalPrice");
        $this->assertEquals(20, $test->getSalePrice(), "SalePrice");
    }

    public function test_03_Comparison()
    {
        $arr = array();
        
        $game = new GameJSON();
        $game->url = "https://store/product/123";
        $game->id = "123";
        $game->name = "Game";
        $game->playable_platform[] = "PS4";
        $game->default_sku = new SKUJSON();
        $game->default_sku->price = 1000;
        $arr[$game->id] = new PlayStationGame(json_decode(json_encode($game)));
        
        $game2 = new GameJSON();
        $game2->url = "https://store/product/234";
        $game2->id = "234";
        $game2->name = "Game2";
        $game2->playable_platform[] = "PS4";
        $game2->default_sku = new SKUJSON();
        $game2->default_sku->price = 2000;
        $arr[$game2->id] = new PlayStationGame(json_decode(json_encode($game2)));
        
        PlayStationGameCache::replace($arr);
        $this->assertEquals($arr, PlayStationGameCache::$cache, "Cach updated in memory");
        
        $arr = array();
        $arr[$game2->id] = new PlayStationGame(json_decode(json_encode($game2)));
        
        $game3 = new GameJSON();
        $game3->url = "https://store/product/345";
        $game3->id = "345";
        $game3->name = "Game3";
        $game3->playable_platform[] = "PS4";
        $game3->playable_platform[] = "PS3";
        $game3->default_sku = new SKUJSON();
        $game3->default_sku->price = 3000;
        $arr[$game3->id] = new PlayStationGame(json_decode(json_encode($game3)));
        
        $newGameList = PlayStationGameCache::getGamesNotInCache($arr);
        Debugger::verbose("New Game List: ", $newGameList);
        
        $this->assertEquals(1, count($newGameList), "Count of new games");
        
        $test = $newGameList["345"];
        $this->assertEquals("345", $test->getID(), "ID");
        $this->assertEquals("https://store/product/345", $test->getURL(), "URL");
        $this->assertEquals("Game3", $test->getShortName(), "ShortName");
        $this->assertEquals("PS4", $test->getPlatforms()[0], "Platform[0]");
        $this->assertEquals("PS3", $test->getPlatforms()[1], "Platform[1]");
        $this->assertEquals(30, $test->getOriginalPrice(), "OriginalPrice");
        $this->assertEquals(30, $test->getSalePrice(), "SalePrice");
    }
}
?>

