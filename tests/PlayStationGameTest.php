<?php
include_once "../src/Debugger.php";
include_once "../src/PlayStationGame.php";
include_once "helpers/PlayStationGameHelper.php";

use PHPUnit\Framework\TestCase;

final class PlayStationGameTest extends TestCase
{

    private function getGameJson($gameId)
    {
        $json = file_get_contents("https://store.playstation.com/store/api/chihiro/00_09_000/container/US/en/999/" . $gameId);
        Debugger::verbose($gameId, " JSON: ", $json);
        return $json;
    }

    public function testNoAwards()
    {
        $game = new GameJSON();
        $game->url = "https://store/product/123";
        $game->id = "123";
        $game->name = "Game";
        $game->playable_platform[] = "PS4";
        $game->default_sku = new SKUJSON();
        $game->default_sku->price = 1000;

        $json = json_encode($game);
        Debugger::verbose("Input ", $json);
        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals("123", $psGame->getID(), "ID");
        $this->assertEquals("https://store/product/123", $psGame->getURL(), "URL");
        $this->assertEquals("Game", $psGame->getShortName(), "ShortName");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platform[0]");
        $this->assertEquals(10, $psGame->getOriginalPrice(), "OriginalPrice");
        $this->assertEquals(10, $psGame->getSalePrice(), "SalePrice");
    }

    public function testSpecialCharacters()
    {
        $game = new GameJSON();
        $game->url = "https://store/product/123";
        $game->id = "123";
        $game->name = file_get_contents("data/01_special_character_game.txt");
        $game->playable_platform[] = "PS4";
        $game->default_sku = new SKUJSON();
        $game->default_sku->price = 1000;

        $json = json_encode($game);
        Debugger::verbose("Input ", $json);
        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals("Battlefield V", $psGame->getShortName(), "ShortName");
    }

    public function testActualGame()
    {
        $json = $this->getGameJson("UP9000-CUSA00552_00-THELASTOFUS00000");

        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals("UP9000-CUSA00552_00-THELASTOFUS00000", $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("The Last Of Us Remastered", $psGame->getShortName(), "ShortName");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertTrue($psGame->getOriginalPrice() > 0, "OriginalPrice exists");
        $this->assertTrue($psGame->getSalePrice() <= $psGame->getOriginalPrice(), "SalePrice <= OriginalPrice");
        $this->assertTrue($psGame->getMetaCriticScore() > 0, "Metacritic Score Exists");
        $this->assertEquals("https://www.metacritic.com/game/playstation-4/the-last-of-us-remastered", $psGame->getMetaCriticURL(), "MetaCritic URL");
    }

    public function testActualGameWithWeirdApostrophe()
    {
        $json = $this->getGameJson("UP0001-CUSA00010_00-AC4GAMEPS4000001");

        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals("UP0001-CUSA00010_00-AC4GAMEPS4000001", $psGame->getID(), "ID");
        $this->assertEquals("Assassin's Creed IV Black Flag", $psGame->getShortName(), "ShortName");
    }

    public function testActualGameWithScore()
    {
        $json = $this->getGameJson("UP9000-CUSA00552_00-THELASTOFUS00000");

        $psGame = new PlayStationGame(json_decode($json));

        $this->assertEquals("UP9000-CUSA00552_00-THELASTOFUS00000", $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("The Last Of Us Remastered", $psGame->getShortName(), "ShortName");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertTrue($psGame->getOriginalPrice() > 0, "Has Original Price");
        $this->assertTrue($psGame->getSalePrice() <= $psGame->getOriginalPrice(), "Sale Price <= Original Price");
        $this->assertEquals(95, $psGame->getMetaCriticScore(), "MetaCriticScore");
        $this->assertEquals("https://www.metacritic.com/game/playstation-4/the-last-of-us-remastered", $psGame->getMetaCriticURL(), "MetaCriticURL");
    }

    public function test_EA_Access_Game()
    {
        $json = $this->getGameJson("UP0006-CUSA02429_00-BATTLEFIELD01000");
        $psGame = new PlayStationGame(json_decode($json));

        $this->assertEquals("UP0006-CUSA02429_00-BATTLEFIELD01000", $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("Battlefield 1", $psGame->getShortName(), "ShortName");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertTrue($psGame->getOriginalPrice() > 0, "Has Original Price");
        $this->assertTrue($psGame->getSalePrice() <= $psGame->getOriginalPrice(), "Sale Price <= Original Price");
        $this->assertNotEquals(0, $psGame->getSalePrice(), "Not equal to 0 for sale price");
        $this->assertTrue($psGame->isEAAccess(), "EA Access Game");
    }
}
?>

