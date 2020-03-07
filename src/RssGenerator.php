<?php
include_once "Debugger.php";

class RssGenerator
{

    public static function write($rssFile, $newLink, $saleTitle)
    {
        $rssFile = Properties::getProperty("rss.dir") . "/" . $rssFile;
        
        Debugger::verbose("Writing RSS to ", $rssFile);
        if (! file_exists($rssFile)) {
            copy("../resources/init.rss.xml", $rssFile);
        }
        $rss = DOMDocument::load($rssFile);
        $channel = $rss->documentElement->getElementsByTagName("channel")->item(0);
        $elemDate = $channel->getElementsByTagName("lastBuildDate")[0];
        $elemNew = $rss->createElement("lastBuildDate");
        $elemNew->appendChild($rss->createTextNode(date(DATE_RSS)));
        $channel->replaceChild($elemNew, $elemDate);
        $elemDate = $channel->getElementsByTagName("pubDate")[0];
        $elemNew = $rss->createElement("pubDate");
        $elemNew->appendChild($rss->createTextNode(date(DATE_RSS)));
        $channel->replaceChild($elemNew, $elemDate);
        $item = $rss->createElement("item");
        $firstItem = $channel->getElementsByTagName("item")->item(0);
        if (NULL == $firstItem) {
            $channel->appendChild($item);
        } else {
            $channel->insertBefore($item, $firstItem);
        }
        
        $title = $item->appendChild($rss->createElement("title"));
        $title->appendChild($rss->createTextNode($saleTitle));
        
        $link = $item->appendChild($rss->createElement("link"));
        $link->appendChild($rss->createTextNode($newLink));
        
        $desc = $item->appendChild($rss->createElement("description"));
        $desc->appendChild($rss->createTextNode(self::getDescription($newLink)));
        
        $pubDate = $item->appendChild($rss->createElement("pubDate"));
        $pubDate->appendChild($rss->createTextNode(date(DATE_RSS)));
        
        $rss->save($rssFile);
    }

    private static function getDescription($link)
    {
        $desc = @file_get_contents($link);
        if ($desc !== FALSE) {
            $start = strpos($desc, "<body>") + 6;
            $end = strpos($desc, "<!--more-->");
            if ($end <= 0) {
                $end = strpos($desc, "</body>");
            }
            return substr($desc, $start, $end - $start);
        }
        return "Not Available";
    }
}
?>

