<?php
include_once "../src/Debugger.php";
include_once "../src/PlayStationGame.php";
include_once "helpers/PlayStationGameHelper.php";
include_once "helpers/Assert.php";

Debugger::info(basename(__FILE__));

$gameId = "UP0006-CUSA04027_00-TITANFALL2RSPWN1";
$json = file_get_contents("https://store.playstation.com/store/api/chihiro/00_09_000/container/US/en/999/" . $gameId);

$psGame = new PlayStationGame(json_decode($json));
assertEquals("ID", $gameId, $psGame->getID());
assertEquals("URL", "", $psGame->getURL());
assertEquals("ShortName", "Titanfall 2 Standard Edition", $psGame->getShortName());
assertEquals("Platform[0]", "PS4", $psGame->getPlatforms()[0]);
assertEquals("MetaCriticURL", "https://www.metacritic.com/game/playstation-4/titanfall-2", $psGame->getMetaCriticURL());
assertEquals("MetaCriticScore", 89, $psGame->getMetaCriticScore());

?>
