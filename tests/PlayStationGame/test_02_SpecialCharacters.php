<?php

include_once "../src/PlayStationGame.php";
include_once "helpers/PlayStationGameHelper.php";
include_once "helpers/Assert.php";

$game = new GameJSON();
$game->url = "https://store/product/123";
$game->id = "123";
$game->name = file_get_contents("PlayStationGame/input/01_special_character_game.txt");
$game->playable_platform[] = "PS4";
$game->default_sku = new SKUJSON();
$game->default_sku->price = 1000;

$json = json_encode($game);
print("Input\n");
print_r($json);
print("\n");
$psGame = new PlayStationGame(json_decode($json));
assertEquals("ShortName", "Battlefield V", $psGame->getShortName());
?>

