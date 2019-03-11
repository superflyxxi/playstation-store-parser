<?php

include_once "../src/Debugger.php";
include_once "../src/PlayStationGame.php";
include_once "helpers/PlayStationGameHelper.php";
include_once "helpers/Assert.php";

Debugger::info(basename(__FILE__));

// This test is expecting "wrong" results. Even after best matching, this game just doesn't exist in Metacritic.

$gameId = "UP0891-CUSA14441_00-RATALAIKAGIANDME";
$json = file_get_contents("https://store.playstation.com/store/api/chihiro/00_09_000/container/US/en/999/".$gameId);

$psGame = new PlayStationGame(json_decode($json));
assertEquals("ID", "UP0891-CUSA14441_00-RATALAIKAGIANDME", $psGame->getID());
assertEquals("URL", "", $psGame->getURL());
assertEquals("ShortName", "I and Me", $psGame->getShortName());
assertEquals("Platform[0]", "PS4", $psGame->getPlatforms()[0]);
assertEquals("OriginalPrice", true, $psGame->getOriginalPrice() > 0);
assertEquals("SalePrice", true, $psGame->getSalePrice() > 0);
assertEquals("MetaCriticURL", "https://www.metacritic.com/game/playstation-4/metal-gear-solid-v-the-phantom-pain", $psGame->getMetaCriticURL());
assertEquals("MetaCriticScore", 93, $psGame->getMetaCriticScore());

?>

