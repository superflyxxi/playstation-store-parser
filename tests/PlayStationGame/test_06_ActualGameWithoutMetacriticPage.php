<?php

include_once "../src/Debugger.php";
include_once "../src/PlayStationGame.php";
include_once "helpers/PlayStationGameHelper.php";
include_once "helpers/Assert.php";

Debugger::info(basename(__FILE__));

$gameId = "UP1024-CUSA06978_00-TOKYOXANADUPS4SP";
$json = file_get_contents("https://store.playstation.com/store/api/chihiro/00_09_000/container/US/en/999/".$gameId);

$psGame = new PlayStationGame(json_decode($json));
assertEquals("ID", "UP1024-CUSA06978_00-TOKYOXANADUPS4SP", $psGame->getID());
assertEquals("URL", "", $psGame->getURL());
assertEquals("ShortName", "Tokyo Xanadu eX+", $psGame->getShortName());
assertEquals("Platform[0]", "PS4", $psGame->getPlatforms()[0]);
assertEquals("OriginalPrice", true, $psGame->getOriginalPrice() > 0);
assertEquals("SalePrice", true, $psGame->getSalePrice() > 0);
assertEquals("MetaCriticScore", -1, $psGame->getMetaCriticScore());
assertEquals("MetaCriticURL", "", $psGame->getMetaCriticURL());
?>

