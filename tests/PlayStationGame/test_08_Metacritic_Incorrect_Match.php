<?php

include_once "../src/Debugger.php";
include_once "../src/PlayStationGame.php";
include_once "helpers/PlayStationGameHelper.php";
include_once "helpers/Assert.php";

Debugger::info(basename(__FILE__));

$gameId = "UP0082-CUSA00252_00-B000000000000261";
$json = file_get_contents("https://store.playstation.com/store/api/chihiro/00_09_000/container/US/en/999/".$gameId);

$psGame = new PlayStationGame(json_decode($json));
assertEquals("ID", $gameId, $psGame->getID());
assertEquals("URL", "", $psGame->getURL());
assertEquals("ShortName", "Thief", $psGame->getShortName());
assertEquals("Platform[0]", "PS4", $psGame->getPlatforms()[0]);
assertEquals("OriginalPrice", true, $psGame->getOriginalPrice() > 0);
assertEquals("SalePrice", true, $psGame->getSalePrice() > 0);
assertEquals("MetaCriticURL", "https://www.metacritic.com/game/playstation-4/thief", $psGame->getMetaCriticURL());
assertEquals("MetaCriticScore", 67, $psGame->getMetaCriticScore());

?>

