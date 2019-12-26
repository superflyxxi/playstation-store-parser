<?php
include_once "../src/Debugger.php";
include_once "../src/html/RowHtmlGenerator.php";
include_once "helpers/Assert.php";

Debugger::info(basename(__FILE__));

RowHtmlGenerator::write("test_01.html", "Testing Rows", array());

$html = simplexml_load_file("/tmp/html/test_01.html");
assertEquals("Title", "Testing Rows", $html->head->title);

?>

