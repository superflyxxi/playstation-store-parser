<?php
include_once "Debugger.php";
include_once "playstation/PlayStationGame.php";

use PHPUnit\Framework\TestCase;

final class MetaCriticMatchTest extends TestCase
{

    private function getGameJson($gameId)
    {
        $json = file_get_contents("https://store.playstation.com/store/api/chihiro/00_09_000/container/US/en/999/" . $gameId);
        Debugger::verbose($gameId, " JSON: ", $json);
        return $json;
    }

    public function testMatchMovie()
    {
        // This test is expecting "wrong" results. Even after best matching, this game just doesn't exist in Metacritic.
        $json = $this->getGameJson("UP0891-CUSA14441_00-RATALAIKAGIANDME");
        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals("UP0891-CUSA14441_00-RATALAIKAGIANDME", $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("I and Me", $psGame->getDisplayName(), "Display Name");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertTrue($psGame->getOriginalPrice() > 0, "OriginalPrice exits");
        $this->assertTrue($psGame->getSalePrice() > 0, "SalePrice exists");
        $this->assertTrue(NULL != $psGame->getMetaCriticURL(), "MetaCriticURL found");
        $this->assertTrue(0 < $psGame->getMetaCriticScore(), "MetaCriticScore doesn't exist");
    }

    public function testMatch_PS4_over_iOS()
    {
        $json = $this->getGameJson("UP0001-CUSA01445_00-SCRABBLEGAMPS401");
        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals("UP0001-CUSA01445_00-SCRABBLEGAMPS401", $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("Scrabble", $psGame->getDisplayName(), "Display Name");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertTrue($psGame->getOriginalPrice() > 0, "OriginalPrice exists");
        $this->assertTrue($psGame->getSalePrice() > 0, "SalePrice exists");
        $this->assertEquals("https://www.metacritic.com/game/ios/scrabble-for-ipad", $psGame->getMetaCriticURL(), "MetaCriticURL");
        $this->assertEquals(94, $psGame->getMetaCriticScore(), "MetaCriticScore");
    }

    public function testIncorrectMatch()
    {
        $json = $this->getGameJson($gameId = "UP0082-CUSA00252_00-B000000000000261");
        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals($gameId, $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("Thief", $psGame->getDisplayName(), "Display Name");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertTrue($psGame->getOriginalPrice() > 0, "OriginalPrice exists");
        $this->assertTrue($psGame->getSalePrice() > 0, "SalePrice exists");
        $this->assertEquals("https://www.metacritic.com/game/playstation-4/thief", $psGame->getMetaCriticURL(), "MetaCriticURL");
        $this->assertEquals(67, $psGame->getMetaCriticScore(), "MetaCriticScore");
    }

    public function testCaseInsensitive()
    {
        $json = $this->getGameJson($gameId = "UP0102-CUSA09193_00-BH2R000000000001");
        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals($gameId, $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("RESIDENT EVIL 2", $psGame->getDisplayName(), "Display Name");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertTrue($psGame->getOriginalPrice() > 0, "OriginalPrice exists");
        $this->assertTrue($psGame->getSalePrice() > 0, "SalePrice exists");
        $this->assertEquals("https://www.metacritic.com/game/playstation-4/resident-evil-2", $psGame->getMetaCriticURL(), "MetaCriticURL");
        $this->assertEquals(91, $psGame->getMetaCriticScore(), "MetaCriticScore");
    }

    public function testPreferScoreOverPlatform()
    {
        $json = $this->getGameJson($gameId = "UP0912-CUSA09349_00-HYPERSENTINEL000");
        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals($gameId, $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("Hyper Sentinel", $psGame->getDisplayName(), "Display Name");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertTrue($psGame->getOriginalPrice() > 0, "OriginalPrice exists");
        $this->assertTrue($psGame->getSalePrice() > 0, "SalePrice exists");
        $this->assertEquals("https://www.metacritic.com/game/xbox-one/hyper-sentinel", $psGame->getMetaCriticURL(), "MetaCriticURL");
        $this->assertEquals(73, $psGame->getMetaCriticScore(), "MetaCriticScore");
    }

    public function testStandardEdition()
    {
        $json = $this->getGameJson($gameId = "UP0006-CUSA04027_00-TITANFALL2RSPWN1");
        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals($gameId, $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("Titanfall 2 Standard Edition", $psGame->getDisplayName(), "Display Name");
        $this->assertEquals("Titanfall 2", $psGame->getGameName(), "Game Name");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertEquals("https://www.metacritic.com/game/playstation-4/titanfall-2", $psGame->getMetaCriticURL(), "MetaCriticURL");
        $this->assertEquals(89, $psGame->getMetaCriticScore(), "MetaCriticScore");
    }

    public function testMatchBest()
    {
        $json = $this->getGameJson($gameId = "UP1003-CUSA02218_00-DISHONOREDGAMENA");
        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals($gameId, $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("Dishonored Definitive Edition", $psGame->getDisplayName(), "Display Name");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertEquals("https://www.metacritic.com/game/playstation-4/dishonored-definitive-edition", $psGame->getMetaCriticURL(), "MetaCriticURL");
    }

    public function testMatchRemastered()
    {
        $json = $this->getGameJson($gameId = "UP9000-CUSA07321_00-UCES011770000001");
        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals($gameId, $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("Patapon 2 Remastered", $psGame->getDisplayName(), "Display Name");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertEquals("https://www.metacritic.com/game/playstation-4/patapon-2-remastered", $psGame->getMetaCriticURL(), "MetaCriticURL");
    }
}

?>

