<?php
include_once "Debugger.php";
include_once "playstation/PlayStationGame.php";
include_once "helpers/PlayStationGameHelper.php";

use PHPUnit\Framework\TestCase;

final class ActualPlayStationGamesTest extends TestCase
{
    public function testWeirdApostrophe()
    {
        $json = getGameJson("UP0001-CUSA00010_00-AC4GAMEPS4000001");

        $psGame = new PlayStationGame(json_decode($json));
        $this->assertEquals("UP0001-CUSA00010_00-AC4GAMEPS4000001", $psGame->getID(), "ID");
        $this->assertEquals("Assassin's Creed IV Black Flag", $psGame->getDisplayName(), "Display Name");
        $this->assertEquals("ASSASSIN'S CREED IV BLACK FLAG", $psGame->getGameName(), "Game Name");
        $this->assertFalse($psGame->isEAAccess(), "EA Access Game");
    }

    public function testScore()
    {
        $json = getGameJson("UP9000-CUSA00552_00-THELASTOFUS00000");

        $psGame = new PlayStationGame(json_decode($json));

        $this->assertEquals("UP9000-CUSA00552_00-THELASTOFUS00000", $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("The Last Of Us Remastered", $psGame->getDisplayName(), "Display Name");
        $this->assertEquals("The Last Of Us Remastered", $psGame->getGameName(), "Game Name");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertTrue($psGame->getOriginalPrice() > 0, "Has Original Price");
        $this->assertTrue($psGame->getSalePrice() <= $psGame->getOriginalPrice(), "Sale Price <= Original Price");
        $this->assertEquals(95, $psGame->getMetaCriticScore(), "MetaCriticScore");
        $this->assertEquals("https://www.metacritic.com/game/playstation-4/the-last-of-us-remastered", $psGame->getMetaCriticURL(), "MetaCriticURL");
        $this->assertFalse($psGame->isEAAccess(), "EA Access Game");
    }

    public function test_EA_Access_Game()
    {
        $json = getGameJson("UP0006-CUSA02429_00-BATTLEFIELD01000");
        $psGame = new PlayStationGame(json_decode($json));

        $this->assertEquals("UP0006-CUSA02429_00-BATTLEFIELD01000", $psGame->getID(), "ID");
        $this->assertEquals("", $psGame->getURL(), "URL");
        $this->assertEquals("Battlefield 1", $psGame->getDisplayName(), "Display Name");
        $this->assertEquals("PS4", $psGame->getPlatforms()[0], "Platforms[0]");
        $this->assertTrue($psGame->getOriginalPrice() > 0, "Has Original Price");
        $this->assertTrue($psGame->getSalePrice() <= $psGame->getOriginalPrice(), "Sale Price <= Original Price");
        $this->assertNotEquals(0, $psGame->getSalePrice(), "Not equal to 0 for sale price");
        $this->assertTrue($psGame->isEAAccess(), "EA Access Game");
    }

}
?>

