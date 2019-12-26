<?php
include_once "../src/Debugger.php";
include_once "../src/RssGenerator.php";

use PHPUnit\Framework\TestCase;

final class RssGeneratorTest extends TestCase
{

    public function testWritingNewRssEntry()
    {
        RssGenerator::write("common.rss.xml", "Link-A", "Test New Entry");
        $this->assertFileExists("/tmp/common.rss.xml");
        $rss = simplexml_load_file("/tmp/common.rss.xml");
        $this->assertEquals("Test New Entry", $rss->channel->item[0]->title, "Title");
        $this->assertEquals("Link-A", $rss->channel->item[0]->link, "Link");
        $this->assertEquals("Not Available", $rss->channel->item[0]->description, "Description");
        $this->assertEqualsWithDelta(time(), strtotime($rss->channel->lastBuildDate), 60, "lastBuildDate");
        $this->assertEqualsWithDelta(time(), strtotime($rss->channel->pubDate), 60, "pubDate");
    }

    /**
     *
     * @depends testWritingNewRssEntry
     */
    public function testAppendNewEntry()
    {
        RssGenerator::write("common.rss.xml", "Link-B", "Test New Entry to Existing");
        $this->assertFileExists("/tmp/common.rss.xml");
        $rss = simplexml_load_file("/tmp/common.rss.xml");
        $this->assertEquals("Test New Entry to Existing", $rss->channel->item[0]->title, "Title");
        $this->assertEquals("Link-B", $rss->channel->item[0]->link, "Link");
        $this->assertEquals("Not Available", $rss->channel->item[0]->description, "Description");
    }
}

?>

