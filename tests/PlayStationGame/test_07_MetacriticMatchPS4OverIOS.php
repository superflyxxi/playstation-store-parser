<?php
include_once "../src/Debugger.php";
include_once "../src/PlayStationGame.php";
include_once "helpers/PlayStationGameHelper.php";
include_once "helpers/Assert.php";

Debugger::info(basename(__FILE__));

$gameId = "UP0001-CUSA01445_00-SCRABBLEGAMPS401";
$json = file_get_contents("https://store.playstation.com/store/api/chihiro/00_09_000/container/US/en/999/" . $gameId);

$psGame = new PlayStationGame(json_decode($json));
assertEquals("ID", "UP0001-CUSA01445_00-SCRABBLEGAMPS401", $psGame->getID());
assertEquals("URL", "", $psGame->getURL());
assertEquals("ShortName", "Scrabble", $psGame->getShortName());
assertEquals("Platform[0]", "PS4", $psGame->getPlatforms()[0]);
assertEquals("OriginalPrice", true, $psGame->getOriginalPrice() > 0);
assertEquals("SalePrice", true, $psGame->getSalePrice() > 0);
assertEquals("MetaCriticURL", "https://www.metacritic.com/game/ios/scrabble-for-ipad", $psGame->getMetaCriticURL());
assertEquals("MetaCriticScore", 94, $psGame->getMetaCriticScore());

?>

