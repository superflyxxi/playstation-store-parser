<?php
include_once "../src/Debugger.php";
include_once "../src/PlayStationGame.php";
include_once "helpers/PlayStationGameHelper.php";
include_once "helpers/Assert.php";

Debugger::info(basename(__FILE__));

$gameId = "UP9000-CUSA00552_00-THELASTOFUS00000";
$json = file_get_contents("https://store.playstation.com/store/api/chihiro/00_09_000/container/US/en/999/" . $gameId);

$psGame = new PlayStationGame(json_decode($json));
assertEquals("ID", "UP9000-CUSA00552_00-THELASTOFUS00000", $psGame->getID());
assertEquals("URL", "", $psGame->getURL());
assertEquals("ShortName", "The Last Of Us Remastered", $psGame->getShortName());
assertEquals("Platform[0]", "PS4", $psGame->getPlatforms()[0]);
assertEquals("OriginalPrice", true, $psGame->getOriginalPrice() > 0);
assertEquals("SalePrice", true, $psGame->getSalePrice() <= $psGame->getOriginalPrice());
assertEquals("MetaCriticScore", 95, $psGame->getMetaCriticScore());
assertEquals("MetaCriticURL", "https://www.metacritic.com/game/playstation-4/the-last-of-us-remastered", $psGame->getMetaCriticURL());
?>

