<?php
include_once "Debugger.php";

class RssGenerator
{

    public static function write($rssFile, $newLink, $saleId)
    {
        Debugger::info("Writing RSS to ", $rssFile);
        if (! file_exists($rssFile)) {
            copy("../resources/init.rss.xml", $rssFile);
        }
        $rss = simplexml_load_file($rssFile);
        $rss->channel->lastBuildDate = date(DATE_RSS);
        $rss->channel->pubDate = date(DATE_RSS);
        $item = $rss->channel->addChild("item");
        $item->title = date(DATE_RSS) . " for " . $saleId;
        $item->description = "New results for " . $saleId;
        $item->link = $newLink;
        $item->pubDate = date('r');
        $rss->asXML($rssFile);
    }
}
?>

