#!/usr/bin/php
<?php

include_once "Debugger.php";
include_once "PlayStationContainer.php";
include_once "PlayStationGame.php";
include_once "PlayStationGameRepository.php";

#print_r($argv);
$saleId = "STORE-MSF77008-WEEKLYDEALS";
if (isset($argv[1])) {
	$saleId = $argv[1];
}

$fileDir = "/home/sigrejas/public_html/";

#$url = "https://store.playstation.com/store/api/chihiro/00_09_000/container/US/en/999/".$saleId."?size=50&gameContentType=games%2Ccontainer&platform=ps4";
$url = "https://store.playstation.com/store/api/chihiro/00_09_000/container/US/en/999/".$saleId."";

Debugger::info("Starting with sale: ", $saleId);
$start = time();
$rootContainer = new PlayStationContainer($url);
Debugger::info("Fetched containers - ", time() - $start);

$gameList = PlayStationGameRepository::getInstance()->getAllGames();
$start = time();
$iScores = 0;
foreach ($gameList as $game) {
	if ($game->getMetaCriticScore() > 0) {
		$iScores++;
	};
}
Debugger::info("Prefetched MetaCritic Scores (", $iScores, "/", count($gameList), ") - ", time() - $start);

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
file_put_contents($outputHtml, "<table border=\"1\">\n"
	."<tr><th>Game</th><th>Original Price</th><th>Sale Price</th><th>Metacritic Score</th></tr>\n");
foreach ($gameList as $game) {
	$html = "<tr><td><a href=\"".$game->getURL()."\">".$game->getShortName()."</a></td><td>$".$game->getOriginalPrice()."</td><td>$".$game->getSalePrice()."</td><td>";
	$score = $game->getMetaCriticScore();
	if ($score == 0) {
		$html .= "&nbsp;";
	} else {
		$html .= $score;
	}
	$html .= "</td></tr>\n";
	file_put_contents($outputHtml, $html, FILE_APPEND);
}
file_put_contents($outputHtml, "</table>\n", FILE_APPEND);
Debugger::info("Generated HTML - ", time() - $start);
Debugger::info("Done!");

?>

