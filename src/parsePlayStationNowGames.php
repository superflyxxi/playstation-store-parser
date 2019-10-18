#!/usr/bin/php
<?php
include_once "Debugger.php";
include_once "PlayStationContainer.php";
include_once "PlayStationGame.php";
include_once "PlayStationGameRepository.php";
include_once "Properties.php";
include_once "RssGenerator.php";
include_once "HtmlGenerator.php";
include_once "PlayStationGameFilter.php";

// print_r($argv);
$saleId = Properties::getProperty("default.containerid");
if (isset($argv[1])) {
    $saleId = $argv[1];
}

$fileDir = Properties::getProperty("html.dir");
$apiUrl = Properties::getProperty("playstation.api.url") . "STORE-MSF77008-ALLGAMES";
$hostBaseUrl = Properties::getProperty("host.base.url");

$gameFilter = new PlayStationGameFilter();
$gameFilter->allowedGameContentType = array(
    "cloud",
    "ps4_cloud"
);

Debugger::info("Starting with all games");
$start = time();
$rootContainer = new PlayStationContainer($apiUrl, $gameFilter);
Debugger::info("Fetched all games - ", time() - $start);

$start = time();
$gameList = PlayStationGameRepository::getInstance()->getAllGames();
Debugger::info("Got all games (" . count($gameList) . ") - ", time() - $start);
$start = time();
$i = 0;
$iPrintEvery = intval(count($gameList) * .1);
Debugger::info("Prefetching Metacritic Scores " . $iPrintEvery);
foreach ($gameList as $game) {
    if (($i ++) % $iPrintEvery == 0) {
        Debugger::info("Fetched " . $i . " of " . count($gameList) . " games.");
    }
    $game->getMetaCriticScore();
}
Debugger::info("Prefetched MetaCritic Scores (" . count($gameList) . ") - ", time() - $start);

$start = time();
usort($gameList, function ($a, $b) {
    $res = $b->getMetaCriticScore() - $a->getMetaCriticScore();
    if ($res != 0) {
        return $res;
    }
    return strcmp($a->getShortName(), $b->getShortName());
});
Debugger::info("Sorted Games (" . count($gameList) . ") - ", time() - $start);

$outHtmlFilename = date("YmdHi") . "-PSNow.html";

HtmlGenerator::write($fileDir . "/" . $outHtmlFilename, $gameList, array());
RssGenerator::write($fileDir . "/playStationNow.rss.xml", $hostBaseUrl . "/" . $outHtmlFilename, "PSNow");

Debugger::info("Done!");

?>

