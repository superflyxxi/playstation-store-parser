#!/usr/bin/php
<?php
include_once "Debugger.php";
include_once "playstation/PlayStationContainer.php";
include_once "playstation/PlayStationGame.php";
include_once "playstation/PlayStationGameRepository.php";
include_once "Properties.php";
include_once "RssGenerator.php";
include_once "html/HtmlGenerator.php";
include_once "playstation/PlayStationGameFilter.php";
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
PlayStationGameCache::getInstance()->load();

Debugger::beginTimer("Fetching all games");
$rootContainer = new PlayStationContainer($apiUrl, $gameFilter);
$fullGameList = PlayStationGameRepository::getInstance()->getAllGames();
Debugger::endTimer("Fetching all games");
Debugger::info("Total games: ", count($fullGameList));

Debugger::info("Determining differences from cache and full list");
$newGameList = PlayStationGameCache::getInstance()->getGamesNotInCache($fullGameList);

Debugger::beginTimer("Sorting new games");
usort($newGameList, function ($a, $b) {
    $res = $b->getMetaCriticScore() - $a->getMetaCriticScore();
    if ($res != 0) {
        return $res;
    }
    return strcmp($a->getDisplayName(), $b->getDisplayName());
});
Debugger::endTimer("Sorting new games");

$outHtmlFilename = date("YmdHi") . "-PSNow.html";

$arrColumns = explode(" ", Properties::getProperty("parse.psnow.columns"));
Debugger::debug("Columns to include, ", $arrColumns);
if (HtmlGenerator::getInstance()->write($outHtmlFilename, "New PlayStation Now Games", $newGameList, $arrColumns)) {
    RssGenerator::write("playStationNow.rss.xml", $hostBaseUrl . "/" . $outHtmlFilename, "New PlayStation Now Games for the Month of " . date("F Y"));
}
Debugger::info("Saving the full list");
HtmlGenerator::getInstance()->write("All-PSNow.html", "All PlayStation Now Games", $fullGameList, $arrColumns);

PlayStationGameCache::getInstance()->replace($fullGameList);
PlayStationGameCache::getInstance()->save();

Debugger::info("Done!");

?>

