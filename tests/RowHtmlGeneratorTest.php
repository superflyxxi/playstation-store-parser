<?php
include_once "../src/Debugger.php";
include_once "../src/html/RowHtmlGenerator.php";
include_once "helpers/PlayStationGameHelper.php";
include_once "PlayStationGame.php";

use PHPUnit\Framework\TestCase;

final class RowHtmlGeneratorTest extends TestCase
{

    public function testEmptyGames()
    {
        RowHtmlGenerator::write("test_basic.html", "Testing Rows", array());
        $date = date("F jS, Y g:ia T");
        $this->assertFileExists("/tmp/html/test_basic.html");
        $html = simplexml_load_file("/tmp/html/test_basic.html");
        $this->assertEquals("Testing Rows", $html->head->title, "Title");
        $this->assertEquals("\nThe top 5 games are .\n\nGenerated " . $date, $html->body->__toString(), "Body");
        $this->assertEquals(1, $html->body->table[0]->count(), "Rows");
        $this->assertEquals(5, $html->body->table[0]->tr[0]->count(), "Columns");
        foreach ($html->body->table[0]->tr[0]->children() as $col) {
            $arrActualCol[] = $col->__toString();
        }
        $this->assertEquals(array(
            "Game",
            "On PS Now(||)",
            "Original Price",
            "Sale Price",
            "Metacritic Score\n(|||)"
        ), $arrActualCol, "Column Names");
    }

    public function testAllCombinationsOfGames()
    {
        $game = new GameJSON();
        $game->id = rand(0, 100);
        $game->url = "https://store/product/" . $game->id;
        $game->name = "The Last of Us Remastered";
        $game->playable_platform[] = "PS4";
        $game->default_sku = new SKUJSON(999);
        $game->gameContentTypesList[] = new ContentTypeGame();
        $list[] = new PlayStationGame(json_decode(json_encode($game)));

        $game = new GameJSON();
        $game->id = rand(0, 100);
        $game->url = "https://store/product/" . $game->id;
        $game->name = "Uncharted 4: A Thief's End";
        $game->playable_platform[] = "PS4";
        $game->default_sku = new SKUJSON(1999);
        $game->default_sku->rewards[] = new SKUJSON(1499);
        $game->gameContentTypesList[] = new ContentTypeGame();
        $game->gameContentTypesList[] = new ContentTypePS4Cloud();
        $list[] = new PlayStationGame(json_decode(json_encode($game)));

        $game = new GameJSON();
        $game->id = rand(0, 100);
        $game->url = "https://store/product/" . $game->id;
        $game->name = "Vampyr";
        $game->playable_platform[] = "PS4";
        $game->default_sku = new SKUJSON(5999);
        $game->default_sku->rewards[] = new SKUJSON(1999);
        $game->gameContentTypesList[] = new ContentTypeGame();
        $game->gameContentTypesList[] = new ContentTypeCloud();
        $list[] = new PlayStationGame(json_decode(json_encode($game)));

        $game = new GameJSON();
        $game->id = rand(0, 100);
        $game->url = "https://store/product/" . $game->id;
        $game->name = "Valkyria Revolution";
        $game->playable_platform[] = "PS4";
        $game->default_sku = new SKUJSON(4999);
        $game->default_sku->rewards[] = new SKUJSON(0);
        $game->gameContentTypesList[] = new ContentTypeGame();
        $list[] = new PlayStationGame(json_decode(json_encode($game)));

        $game = new GameJSON();
        $game->id = rand(0, 100);
        $game->url = "https://store/product/" . $game->id;
        $game->name = "Bloody Zombies";
        $game->playable_platform[] = "PS4";
        $game->default_sku = new SKUJSON(999);
        $game->gameContentTypesList[] = new ContentTypeGame();
        $list[] = new PlayStationGame(json_decode(json_encode($game)));

        $game = new GameJSON();
        $game->id = rand(0, 100);
        $game->url = "https://store/product/" . $game->id;
        $game->name = "Hacky Zack";
        $game->playable_platform[] = "PS4";
        $game->default_sku = new SKUJSON(999);
        $game->gameContentTypesList[] = new ContentTypeGame();
        $list[] = new PlayStationGame(json_decode(json_encode($game)));

        RowHtmlGenerator::write("test_complex.html", "Testing Complex Scenario", $list);
        $date = date("F jS, Y g:ia T");
        $this->assertFileExists("/tmp/html/test_complex.html");
        // $html = new DOMDocument();
        // $html->loadHtmlFile("/tmp/html/test_complex.html");
        // $html = simplexml_import_dom($html);
        $html = simplexml_load_file("/tmp/html/test_complex.html");
        $this->assertEquals("Testing Complex Scenario", $html->head->title, "Title");
        $this->assertEquals("\nThe top 5 games are , , , , and .\n\nGenerated " . $date, $html->body->__toString(), "Body");
        $this->assertEquals(5, $html->body->table[0]->tr[0]->count(), "Columns");
        $this->assertEquals(7, $html->body->table[0]->count(), "Rows");

        // The Last of Us
        $row = $html->body->table[0]->tr[1];
        $this->assertEquals("The Last of Us Remastered", $row->td[0]->a->__toString(), "1) Game Title");
        $this->assertEquals("No", $row->td[1]->__toString(), "1) On PS Now");
        $this->assertEquals("$9.99", $row->td[2]->__toString(), "1) Original Price");
        $this->assertEquals("$9.99", $row->td[3]->__toString(), "1) Sale Price");
        $this->assertEquals(" metaGood offPsNow", $row['class'], "3) Class");
        $this->assertEquals(95, $row->td[4]->a->__toString(), "1) Score");

        // Uncharted 4
        $row = $html->body->table[0]->tr[2];
        $this->assertEquals("Uncharted 4: A Thief's End", $row->td[0]->a->__toString(), "2) Game Title");
        $this->assertEquals("Yes", $row->td[1]->__toString(), "2) On PS Now");
        $this->assertEquals("$19.99", $row->td[2]->__toString(), "2) Original Price");
        $this->assertEquals("$14.99", $row->td[3]->__toString(), "2) Sale Price");
        $this->assertEquals(" metaGood onPsNow", $row['class'], "3) Class");
        $this->assertEquals(93, $row->td[4]->a->__toString(), "2) Score");

        // Vampyr
        $row = $html->body->table[0]->tr[3];
        $this->assertEquals("Vampyr", $row->td[0]->a->__toString(), "3) Game Title");
        $this->assertEquals("Yes", $row->td[1]->__toString(), "3) On PS Now");
        $this->assertEquals("$59.99", $row->td[2]->__toString(), "3) Original Price");
        $this->assertEquals("$19.99", $row->td[3]->__toString(), "3) Sale Price");
        $this->assertEquals(" metaOkay onPsNow", $row['class'], "3) Class");
        $this->assertEquals(70, $row->td[4]->a->__toString(), "3) Score");

        // Valkyria Revolution
        $row = $html->body->table[0]->tr[4];
        $this->assertEquals("Valkyria Revolution", $row->td[0]->a->__toString(), "4) Game Title");
        $this->assertEquals("No", $row->td[1]->__toString(), "4) On PS Now");
        $this->assertEquals("$49.99", $row->td[2]->__toString(), "4) Original Price");
        $this->assertEquals("$0", $row->td[3]->__toString(), "4) Sale Price");
        $this->assertEquals(" metaBad offPsNow", $row['class'], "4) Class");
        $this->assertEquals(54, $row->td[4]->a->__toString(), "4) Score");

        // Bloody Zombies
        $row = $html->body->table[0]->tr[5];
        $this->assertEquals("Bloody Zombies", $row->td[0]->a->__toString(), "5) Game Title");
        $this->assertEquals("No", $row->td[1]->__toString(), "5) On PS Now");
        $this->assertEquals("$9.99", $row->td[2]->__toString(), "5) Original Price");
        $this->assertEquals("$9.99", $row->td[3]->__toString(), "5) Sale Price");
        $this->assertEquals(" offPsNow", $row['class'], "5) Class");
        $this->assertEquals("TBD", $row->td[4]->a->__toString(), "5) Score");

        // Hacky Zack
        $row = $html->body->table[0]->tr[6];
        $this->assertEquals("Hacky Zack", $row->td[0]->a->__toString(), "6) Game Title");
        $this->assertEquals("No", $row->td[1]->__toString(), "6) On PS Now");
        $this->assertEquals("$9.99", $row->td[2]->__toString(), "6) Original Price");
        $this->assertEquals("$9.99", $row->td[3]->__toString(), "6) Sale Price");
        $this->assertEquals(" offPsNow", $row['class'], "6) Class");
        $this->assertEquals("Not Found", $row->td[4]->__toString(), "6) Score");
    }
}

?>

