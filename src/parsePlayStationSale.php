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

$saleId = isset($argv[1]) ? $argv[1] : Properties::getProperty("default.containerid");
$platforms = explode(" ", isset($argv[2]) ? $argv[2] : Properties::getProperty("default.platforms"));

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
    "PSN_GAME",
    "BUNDLE"
);
$gameFilter->allowedPlayablePlatforms = $platforms;

Debugger::info("Starting with sale: ", $saleId);

Debugger::beginTimer("Load games");
$rootContainer = new PlayStationContainer($apiUrl, $gameFilter);
$gameList = PlayStationGameRepository::getInstance()->getAllGames();
Debugger::endTimer("Load games");

Debugger::info("Got all games: " . count($gameList));

Debugger::beginTimer("Prefetch Metacritic");
$i = 0;
$maxGames = count($gameList);
$iPrintEvery = max(1, intval($maxGames * .1));
foreach ($gameList as $game) {
    if (($i ++) % $iPrintEvery == 0) {
        Debugger::info("Fetched " . (intval($i/$maxGames*100)) . "% (" . $i . " of " . $maxGames . ") games.");
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
    return strcmp($a->getDisplayName(), $b->getDisplayName());
});
Debugger::endTimer("Sorting games");

$outHtmlFilename = $saleId . "-" . date("YmdHi") . ".html";

$arrColumns = explode(" ", Properties::getProperty("parse.store." . $saleId . ".columns", Properties::getProperty("parse.store.columns")));
Debugger::debug("Columns to include, ", $arrColumns);
if (HtmlGenerator::getInstance()->write($outHtmlFilename, $saleIdMapping[$saleId], $gameList, $arrColumns)) {
    RssGenerator::write("playStationSale.rss.xml", $hostBaseUrl . "/" . $outHtmlFilename, $saleIdMapping[$saleId]);
}

Debugger::info("Done!");

?>

