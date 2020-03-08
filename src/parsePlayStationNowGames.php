#!/usr/bin/php
<?php
include_once "Debugger.php";
include_once "PlayStationContainer.php";
include_once "PlayStationGame.php";
include_once "PlayStationGameRepository.php";
include_once "Properties.php";
include_once "RssGenerator.php";
include_once "html/HtmlGenerator.php";
include_once "PlayStationGameFilter.php";
include_once "cache/PlayStationGameCache.php";

$saleId = Properties::getProperty("default.containerid");
if (isset($argv[1])) {
    $saleId = $argv[1];
}

$apiUrl = Properties::getProperty("playstation.api.url") . "STORE-MSF77008-ALLGAMES";
$hostBaseUrl = Properties::getProperty("host.base.url");

$gameFilter = new PlayStationGameFilter();
$gameFilter->allowedGameContentType = array(
    "cloud",
    "ps4_cloud"
);
PlayStationGameCache::load();

Debugger::beginTimer("Fetching all games");
$rootContainer = new PlayStationContainer($apiUrl, $gameFilter);
$fullGameList = PlayStationGameRepository::getInstance()->getAllGames();
Debugger::endTimer("Fetching all games");
Debugger::info("Total games: ", count($fullGameList));

Debugger::info("Determining differences from cache and full list");
$newGameList = PlayStationGameCache::getGamesNotInCache($fullGameList);

Debugger::beginTimer("Sorting new games");
usort($newGameList, function ($a, $b) {
    $res = $b->getMetaCriticScore() - $a->getMetaCriticScore();
    if ($res != 0) {
        return $res;
    }
    return strcmp($a->getShortName(), $b->getShortName());
});
Debugger::endTimer("Sorting new games");

$outHtmlFilename = date("YmdHi") . "-PSNow.html";

if (HtmlGenerator::getInstance()->write($outHtmlFilename, "New PlayStation Now Games", $newGameList, array())) {
    RssGenerator::write("playStationNow.rss.xml", $hostBaseUrl . "/" . $outHtmlFilename, "New PlayStation Now Games for the Month of " . date("F Y"));
}

PlayStationGameCache::replace($fullGameList);
PlayStationGameCache::save();

Debugger::info("Done!");

?>

