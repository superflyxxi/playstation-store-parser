<?php
include_once "Debugger.php";
include_once "playstation/PlayStationGame.php";
include_once "cache/PlayStationGameCache.php";
include_once "helpers/PlayStationGameHelper.php";

use PHPUnit\Framework\TestCase;

final class PlayStationGameCacheTest extends TestCase
{

    private function assertCache(array $expected)
    {
        $this->assertEquals(count($expected), PlayStationGameCache::getInstance()->size(), "Size of cache");
        foreach ($expected as $exKey => $exObj) {
            $this->assertEquals($exObj, PlayStationGameCache::getInstance()->get($exKey), "Games equal for $exKey");
        }
    }

    public function test_01_No_Cache()
    {
        PlayStationGameCache::getInstance()->load();
        $this->assertEquals(0, PlayStationGameCache::getInstance()->size(), "Size of cache");
    }

    public function test_02_Basic_End_To_End()
    {
        $arr = array();

        $game = new GameJSON();
        $game->id = "US123";
        $game->url = "https://store/product/" . $game->id;
        $game->name = "Game";
        $game->playable_platform[] = "PS4";
        $game->default_sku = new SKUJSON();
        $game->default_sku->price = 1000;
        $game->gameContentTypesList[] = new ContentTypeCloud();
        $game->gameContentTypesList[] = new ContentTypePS4Cloud();
        $arr[$game->id] = new PlayStationGame(json_decode(json_encode($game)));

        $game = new GameJSON();
        $game->id = "US234";
        $game->url = "https://store/product/" . $game->id;
        $game->name = "Game2";
        $game->playable_platform[] = "PS4";
        $game->playable_platform[] = "PS3";
        $game->default_sku = new SKUJSON();
        $game->default_sku->price = 2000;
        $arr[$game->id] = new PlayStationGame(json_decode(json_encode($game)));

        Debugger::verbose("Array before replacement", $arr);

        PlayStationGameCache::getInstance()->replace($arr);

        $this->assertCache($arr);

        PlayStationGameCache::getInstance()->save();
        $this->assertTrue(file_exists("./psnow_cache.json"), "File saved");
        Debugger::verbose("File contents: ", file_get_contents("./psnow_cache.json"));

        PlayStationGameCache::getInstance()->replace(array());
        $this->assertCache(array());

        PlayStationGameCache::getInstance()->load();
        $this->assertEquals(2, PlayStationGameCache::getInstance()->size(), "Size of new cache");

        $test = PlayStationGameCache::getInstance()->get("US123");
        Debugger::verbose("First entry in cache: ", $test);
        $this->assertEquals("US123", $test->getID(), "ID");
        $this->assertEquals("https://store/product/US123", $test->getURL(), "URL");
        $this->assertEquals("Game", $test->getDisplayName(), "Display Name");
        $this->assertEquals("PS4", $test->getPlatforms()[0], "Platform[0]");
        $this->assertEquals(10, $test->getOriginalPrice(), "OriginalPrice");
        $this->assertEquals(10, $test->getSalePrice(), "SalePrice");

        $test = PlayStationGameCache::getInstance()->get("US234");
        Debugger::verbose("Second entry in cache: ", $test);
        $this->assertEquals("US234", $test->getID(), "ID");
        $this->assertEquals("https://store/product/US234", $test->getURL(), "URL");
        $this->assertEquals("Game2", $test->getDisplayName(), "Display Name");
        $this->assertEquals("PS4", $test->getPlatforms()[0], "Platform[0]");
        $this->assertEquals("PS3", $test->getPlatforms()[1], "Platform[1]");
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

        PlayStationGameCache::getInstance()->replace($arr);
        $this->assertCache($arr);

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

        $newGameList = PlayStationGameCache::getInstance()->getGamesNotInCache($arr);
        Debugger::verbose("New Game List: ", $newGameList);

        $this->assertEquals(1, count($newGameList), "Count of new games");

        $test = $newGameList["345"];
        $this->assertEquals("345", $test->getID(), "ID");
        $this->assertEquals("https://store/product/345", $test->getURL(), "URL");
        $this->assertEquals("Game3", $test->getDisplayName(), "Display Name");
        $this->assertEquals("PS4", $test->getPlatforms()[0], "Platform[0]");
        $this->assertEquals("PS3", $test->getPlatforms()[1], "Platform[1]");
        $this->assertEquals(30, $test->getOriginalPrice(), "OriginalPrice");
        $this->assertEquals(30, $test->getSalePrice(), "SalePrice");
    }
}
?>

