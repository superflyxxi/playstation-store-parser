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

// print_r($argv);
$saleId = Properties::getProperty("default.containerid");
if (isset($argv[1])) {
    $saleId = $argv[1];
}

$saleIdMapping = array(
    "STORE-MSF77008-WEEKLYDEALS" => "Weekly Deals for " . date("F jS, Y"),
    "STORE-MSF77008-PSPLUSFREEGAMES" => "PlayStation Plus Free Games for " . date("F Y"),
    "STORE-MSF77008-NEWTHISWEEK" => "New Games for the Week of " . date("F jS, Y")
);

$apiUrl = Properties::getProperty("playstation.api.url") . $saleId . ""; // ?platform=ps4";
$hostBaseUrl = Properties::getProperty("host.base.url");

$gameFilter = new PlayStationGameFilter();
$gameFilter->allowedGameContentType = array(
    "FULL_GAME",
    "PSN_GAME"
);
$gameFilter->allowedPlayablePlatforms = array(
    "PS4"
);

Debugger::info("Starting with sale: ", $saleId);

Debugger::beginTimer("Load games");
$rootContainer = new PlayStationContainer($apiUrl, $gameFilter);
$gameList = PlayStationGameRepository::getInstance()->getAllGames();
Debugger::endTimer("Load games");

Debugger::info("Got all games: " . count($gameList));

Debugger::beginTimer("Prefetch Metacritic");
$i = 0;
$iPrintEvery = max(1, intval(count($gameList) * .1));
foreach ($gameList as $game) {
    if (($i ++) % $iPrintEvery == 0) {
        Debugger::info("Fetched " . $i . " of " . count($gameList) . " games.");
    }
    $game->getMetaCriticScore();
}
Debugger::endTimer("Prefetch Metacritic");

Debugger::beginTimer("Sorting games");
usort($gameList, function ($a, $b) {
    $res = $b->getMetaCriticScore() - $a->getMetaCriticScore();
    if ($res != 0) {
        return $res;
    }
    return strcmp($a->getShortName(), $b->getShortName());
});
Debugger::endTimer("Sorting games");

$outHtmlFilename = date("YmdHi") . "-" . $saleId . ".html";

if (HtmlGenerator::getInstance()->write($outHtmlFilename, $saleIdMapping[$saleId], $gameList)) {
    RssGenerator::write("playstationStore.rss.xml", $hostBaseUrl . "/" . $outHtmlFilename, $saleIdMapping[$saleId]);
}

Debugger::info("Done!");

?>

