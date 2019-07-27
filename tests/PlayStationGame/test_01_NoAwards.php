<?php
include_once "../src/Debugger.php";
include_once "../src/PlayStationGame.php";
include_once "helpers/PlayStationGameHelper.php";
include_once "helpers/Assert.php";

Debugger::info(basename(__FILE__));

$game = new GameJSON();
$game->url = "https://store/product/123";
$game->id = "123";
$game->name = "Game";
$game->playable_platform[] = "PS4";
$game->default_sku = new SKUJSON();
$game->default_sku->price = 1000;

$json = json_encode($game);
print("Input\n");
print_r($json);
print("\n");
$psGame = new PlayStationGame(json_decode($json));
assertEquals("ID", "123", $psGame->getID());
assertEquals("URL", "https://store/product/123", $psGame->getURL());
assertEquals("ShortName", "Game", $psGame->getShortName());
assertEquals("Platform[0]", "PS4", $psGame->getPlatforms()[0]);
assertEquals("OriginalPrice", 10, $psGame->getOriginalPrice());
assertEquals("SalePrice", 10, $psGame->getSalePrice());
?>

