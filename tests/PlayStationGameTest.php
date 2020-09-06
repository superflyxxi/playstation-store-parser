<?php
include_once "Debugger.php";
include_once "playstation/PlayStationGame.php";
include_once "helpers/PlayStationGameHelper.php";

use PHPUnit\Framework\TestCase;

final class PlayStationGameTest extends TestCase
{

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
        $this->assertEquals("Game", $psGame->getDisplayName(), "Display Name");
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
        $this->assertEquals("Battlefield V", $psGame->getDisplayName(), "Display Name");
    }

}
?>

