<?php
include_once "Debugger.php";
include_once "Properties.php";
include_once "html/RowHtmlGenerator.php";

class HtmlGenerator
{

    private static $instance = NULL;

    public static function getInstance()
    {
        if (NULL === $instance) {
            switch (Properties::getProperty("html.generator")) {
                case "row":
                default:
                    self::$instance = new RowHtmlGenerator();
                    break;
            }
        }
        return self::$instance;
    }

    public function write($outputHtml, $title, $gameList, $columnList = array("psNow", "originalPrice", "salePrice"))
    {
        throw new Exception("Missing html.generator implementation.");
    }
}

?>

