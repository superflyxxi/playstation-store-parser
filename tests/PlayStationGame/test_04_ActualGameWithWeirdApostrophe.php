<?php

include_once "../src/Debugger.php";
include_once "../src/PlayStationGame.php";
include_once "helpers/PlayStationGameHelper.php";
include_once "helpers/Assert.php";

Debugger::info(basename(__FILE__));

$gameId = "UP0001-CUSA00010_00-AC4GAMEPS4000001";
$json = file_get_contents("https://store.playstation.com/store/api/chihiro/00_09_000/container/US/en/999/".$gameId);
print("JSON's Name: ");
print_r(json_decode($json)->name);
print("\n");

$psGame = new PlayStationGame(json_decode($json));
assertEquals("ID", "UP0001-CUSA00010_00-AC4GAMEPS4000001", $psGame->getID());
assertEquals("ShortName", "Assassin's Creed IV Black Flag", $psGame->getShortName());
?>

