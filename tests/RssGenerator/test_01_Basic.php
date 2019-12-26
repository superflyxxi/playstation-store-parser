<?php
include_once "../src/Debugger.php";
include_once "../src/RssGenerator.php";
include_once "helpers/Assert.php";

Debugger::info(basename(__FILE__));


RssGenerator::write("./test_01.rss.xml", "NewLink", "Test 01");

$rss = simplexml_load_file("/tmp/test_01.rss.xml");
assertEquals("Title", "Test 01", $rss->channel->item[0]->title);
assertEquals("Link", "NewLink", $rss->channel->item[0]->link);
assertEquals("Description", "Not Available", $rss->channel->item[0]->description);
assertDatesEquals("lastBuildDate", date(DATE_RSS), $rss->channel->lastBuildDate, 60);
assertDatesEquals("pubDate", date(DATE_RSS), $rss->channel->pubDate, 60);


RssGenerator::write("./test_01.rss.xml", "Linkb", "Test 01b");

$rss = simplexml_load_file("/tmp/test_01.rss.xml");
assertEquals("Title", "Test 01b", $rss->channel->item[0]->title);
assertEquals("Link", "Linkb", $rss->channel->item[0]->link);
assertEquals("Description", "Not Available", $rss->channel->item[0]->description);
assertEquals("Title", "Test 01", $rss->channel->item[1]->title);
?>


