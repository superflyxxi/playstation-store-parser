<?php
include_once "Debugger.php";
include_once "html/HtmlGenerator.php";

class RowHtmlGenerator extends HtmlGenerator
{

    public function write($outputHtml, $title, $gameList, $columnList = array("psNow", "originalPrice", "salePrice"))
    {
        $start = time();
        $outputHtml = Properties::getProperty("html.dir") . "/" . $outputHtml;
        Debugger::info("Writing HTML to ", $outputHtml);
        $hostBaseUrl = Properties::getProperty("host.base.url");

        file_put_contents($outputHtml, "<html>\n", FILE_APPEND);
        file_put_contents($outputHtml, "<head>\n", FILE_APPEND);
        file_put_contents($outputHtml, "<title>", FILE_APPEND);
        file_put_contents($outputHtml, $title, FILE_APPEND);
        file_put_contents($outputHtml, "</title>\n", FILE_APPEND);
        file_put_contents($outputHtml, "<link media='screen' type='text/css' rel='stylesheet' href='../", FILE_APPEND);
        file_put_contents($outputHtml, Properties::getProperty("style", "styles/style.css"), FILE_APPEND);
        file_put_contents($outputHtml, "' />\n", FILE_APPEND);
        file_put_contents($outputHtml, "<script src='../js/common.js'></script>\n", FILE_APPEND);
        file_put_contents($outputHtml, "</head>\n", FILE_APPEND);
        file_put_contents($outputHtml, "<body>\n", FILE_APPEND);

        $webUrl = Properties::getProperty("playstation.web.url");
        // Top 5
        $topFive = "The top 5 games are ";
        $iMax = min(5, count($gameList));
        for ($i = 0; $i < $iMax; $i ++) {
            if ($i > 0) {
                $topFive .= ", ";
            }
            if ($i == $iMax - 1) {
                $topFive .= "and ";
            }
            $topFive .= "<a href='" . $webUrl . $gameList[$i]->getID() . "'>" . $gameList[$i]->getShortName() . "</a>";
        }
        $topFive .= ".<!--more--><br />\n";
        file_put_contents($outputHtml, $topFive, FILE_APPEND);
        file_put_contents($outputHtml, "<table border=\"1\">\n", FILE_APPEND);
        file_put_contents($outputHtml, "<tr><th id='gameTitle'>Game</th>\n", FILE_APPEND);

        foreach ($columnList as $column) {
            switch ($column) {
                case "psNow":
                    file_put_contents($outputHtml, "<th id='psNow'>On PS Now<br/>", FILE_APPEND);
                    file_put_contents($outputHtml, "(<button class='filter' onclick='hideAllClasses(\".onPsNow\");showAllClasses(\".offPsNow\")'>hide</button>", FILE_APPEND);
                    file_put_contents($outputHtml, "|<button class='filter' onclick='showAllClasses(\".onPsNow\");hideAllClasses(\".offPsNow\")'>only</button>", FILE_APPEND);
                    file_put_contents($outputHtml, "|<button class='filter' onclick='showAllClasses(\".onPsNow\");showAllClasses(\".offPsNow\")'>all</button>)</th>", FILE_APPEND);
                    break;

                case "originalPrice":
                    file_put_contents($outputHtml, "<th id='originalPrice'>Original Price</th>", FILE_APPEND);
                    break;

                case "salePrice":
                    file_put_contents($outputHtml, "<th id='salePrice'>Sale Price</th>", FILE_APPEND);
                    break;
            }
        }
        file_put_contents($outputHtml, "<th id='metaCritic'>Metacritic Score<br/>\n(", FILE_APPEND);
        file_put_contents($outputHtml, "<button class='filter' onclick='showAllClasses(\".metaGood\");hideAllClasses(\".metaOkay\");hideAllClasses(\".metaBad\")'>good</button>|", FILE_APPEND);
        file_put_contents($outputHtml, "<button class='filter' onclick='hideAllClasses(\".metaGood\");showAllClasses(\".metaOkay\");hideAllClasses(\".metaBad\")'>okay</button>|", FILE_APPEND);
        file_put_contents($outputHtml, "<button class='filter' onclick='hideAllClasses(\".metaGood\");hideAllClasses(\".metaOkay\");showAllClasses(\".metaBad\")'>bad</button>|", FILE_APPEND);
        file_put_contents($outputHtml, "<button class='filter' onclick='showAllClasses(\".metaGood\");showAllClasses(\".metaOkay\");showAllClasses(\".metaBad\")'>all</button>)</th></tr>", FILE_APPEND);
        foreach ($gameList as $game) {
            $score = $game->getMetaCriticScore();
            $class = "";
            if ($score >= 75) {
                $class .= " metaGood";
            } else if ($score >= 60) {
                $class .= " metaOkay";
            } else if ($score > 0) {
                $class .= " metaBad";
            } else {
                $score = "TBD";
            }
            $html = "<td ><a href='" . $webUrl . $game->getID() . "'>" . $game->getShortName() . "</a></td>";
            foreach ($columnList as $column) {
                switch ($column) {
                    case "psNow":
                        $html .= "<td>" . ($game->isPSNow() ? "Yes" : "No") . "</td>";
                        $class .= $game->isPSNow() ? " onPsNow" : " offPsNow";
                        break;

                    case "originalPrice":
                        $html .= "<td>$" . $game->getOriginalPrice() . "</td>";
                        break;

                    case "salePrice":
                        $html .= "<td>$" . $game->getSalePrice() . "</td>";
                        break;
                }
            }
            $html .= "<td >";
            if ($game->getMetaCriticURL() == "") {
                $html .= "Not Found";
            } else {
                $html .= "<a href='" . $game->getMetaCriticURL() . "'>" . $score . "</a>";
            }
            $html .= "</td>";
            $html .= "</tr>\n";
            $html = "<tr class='" . $class . "' />" . $html;
            file_put_contents($outputHtml, $html, FILE_APPEND);
        }
        file_put_contents($outputHtml, "</table>\n", FILE_APPEND);
        file_put_contents($outputHtml, "Generated " . date("F jS, Y g:ia T"), FILE_APPEND);
        file_put_contents($outputHtml, "</body>\n</html>\n", FILE_APPEND);
        Debugger::info("Generated HTML - ", time() - $start);
    }
}
?>

