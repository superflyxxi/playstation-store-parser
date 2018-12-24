#!/usr/bin/php
<?php

include_once "Debugger.php";
include_once "PlayStationContainer.php";
include_once "PlayStationGame.php";
include_once "PlayStationGameRepository.php";
include_once "Properties.php";

#print_r($argv);
$saleId = Properties::getProperty("default.containerid");
if (isset($argv[1])) {
	$saleId = $argv[1];
}

$fileDir = Properties::getProperty("html.dir");
$webUrl = Properties::getProperty("web.url");
$apiUrl = Properties::getProperty("api.url").$saleId."";//?platform=ps4";

Debugger::info("Starting with sale: ", $saleId);
$start = time();
$rootContainer = new PlayStationContainer($apiUrl);
Debugger::info("Fetched containers - ", time() - $start);

$start = time();
$gameList = PlayStationGameRepository::getInstance()->getAllGames();
Debugger::info("Got all games (".count($gameList).") - ", time() - $start);
$start = time();
foreach ($gameList as $game) {
	$game->getMetaCriticScore();
}
Debugger::info("Prefetched MetaCritic Scores (".count($gameList).") - ", time() - $start);

$start = time();
usort($gameList, function($a, $b) {
	$res = $b->getMetaCriticScore() - $a->getMetaCriticScore();
	if ($res != 0) {
		return $res;
	}
	return strcmp($a->getShortName(), $b->getShortName());
});
Debugger::info("Sorted Games (".count($gameList).") - ", time() - $start);

$start = time();
$outputHtml = $fileDir."/".date("YmdHi")."-".$saleId.".html";
// Top 5
$topFive = "The top 5 games are ";
$iMax = min(5, count($gameList));
for ($i = 0; $i<$iMax; $i++ ) {
	if ($i > 0) {
		$topFive .= ", ";
	}
	if ($i == $iMax-1) {
		$topFive .= "and ";
	}
	$topFive .= $gameList[$i]->getShortName();
}
$topFive .= ".<br /><!--more-->\n";
file_put_contents($outputHtml, $topFive, FILE_APPEND);
file_put_contents($outputHtml, "<table border=\"1\">\n"
	."<tr><th>Game</th><th>Original Price</th><th>Sale Price</th><th>Metacritic Score</th></tr>\n", FILE_APPEND);
foreach ($gameList as $game) {
	$html = "<tr>";
	$score = $game->getMetaCriticScore();
	if ($score >= 75) {
		$color = "green";
	} else if ($score >= 60) {
		$color = "orange";
	} else {
		$color = "red";
	}
	$html .= "<td><a href='".$webUrl.$game->getID()."'>".$game->getShortName()."</a></td>";
	$html .= "<td>$".$game->getOriginalPrice()."</td>";
	$html .= "<td>$".$game->getSalePrice()."</td>";
	$html .= "<td bgcolor=\"".$color."\">";
	if ($score == 0) {
		$html .= "&nbsp;";
	} else {
		$html .= "<a href='".$game->getMetaCriticURL()."'>".$score."</a>";
	}
	$html .= "</td>";
	$html .= "</tr>\n";
	file_put_contents($outputHtml, $html, FILE_APPEND);
}
file_put_contents($outputHtml, "</table>\n", FILE_APPEND);
file_put_contents($outputHtml, "Generated " . date("F jS, Y g:ia T"), FILE_APPEND);
Debugger::info("Generated HTML - ", time() - $start);
Debugger::info("Done!");

?>

