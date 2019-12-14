<?php
include_once "Debugger.php";

class RssGenerator
{

    public static function write($rssFile, $newLink, $saleTitle)
    {
        $rssFile = Properties::getProperty("rss.dir") . "/" . $rssFile;

        Debugger::info("Writing RSS to ", $rssFile);
        if (! file_exists($rssFile)) {
            copy("../resources/init.rss.xml", $rssFile);
        }
        $rss = simplexml_load_file($rssFile);
        $rss->channel->lastBuildDate = date(DATE_RSS);
        $rss->channel->pubDate = date(DATE_RSS);
        $item = $rss->channel->addChild("item");
        $item->title = $saleTitle;
        $item->link = $newLink;
        $item->description = self::getDescription($newLink);
        $item->pubDate = date(DATE_RSS);
        $rss->asXML($rssFile);
    }

    private static function getDescription($link)
    {
        $desc = file_get_contents($link);
        $start = strpos($desc, "<body>") + 6;
        $end = strpos($desc, "<!--more-->");
        if ($end <= 0) {
            $end = strpos($desc, "</body>");
        }
        return substr($desc, $start, $end - $start);
    }
}
?>

