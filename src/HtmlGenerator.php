<?php
include_once "Debugger.php";

class HtmlGenerator
{

    public static function write($outputHtml, $gameList, $columnList = array("psNow", "originalPrice", "salePrice"))
    {
        $start = time();
        Debugger::info("Writing HTML to ", $outputHtml);
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
            $topFive .= $gameList[$i]->getShortName();
        }
        $topFive .= ".<br /><!--more-->\n";
        file_put_contents($outputHtml, $topFive, FILE_APPEND);
        file_put_contents($outputHtml, "<table border=\"1\">\n", FILE_APPEND);
        file_put_contents($outputHtml, "<tr><th>Game</th>\n", FILE_APPEND);

        foreach ($columnList as $column) {
            switch ($column) {
                case "psNow":
                    file_put_contents($outputHtml, "<th>On PS Now</th>", FILE_APPEND);
                    break;

                case "originalPrice":
                    file_put_contents($outputHtml, "<th>Original Price</th>", FILE_APPEND);
                    break;

                case "salePrice":
                    file_put_contents($outputHtml, "<th>Sale Price</th>", FILE_APPEND);
                    break;
            }
        }
        file_put_contents($outputHtml, "<th>Metacritic Score</th></tr>\n", FILE_APPEND);
        foreach ($gameList as $game) {
            $html = "<tr>";
            $score = $game->getMetaCriticScore();
            if ($score >= 75) {
                $color = "green";
            } else if ($score >= 60) {
                $color = "orange";
            } else {
                $color = "red";
            }
            $html .= "<td><a href='" . $webUrl . $game->getID() . "'>" . $game->getShortName() . "</a></td>";
            foreach ($columnList as $column) {
                switch ($column) {
                    case "psNow":
                        $html .= "<td>" . ($game->isPSNow() ? "Yes" : "No") . "</td>";
                        break;

                    case "originalPrice":
                        $html .= "<td>$" . $game->getOriginalPrice() . "</td>";
                        break;

                    case "salePrice":
                        $html .= "<td>$" . $game->getSalePrice() . "</td>";
                        break;
                }
            }
            $html .= "<td bgcolor=\"" . $color . "\">";
            if ($score <= 0) {
                $html .= "&nbsp;";
            } else {
                $html .= "<a href='" . $game->getMetaCriticURL() . "'>" . $score . "</a>";
            }
            $html .= "</td>";
            $html .= "</tr>\n";
            file_put_contents($outputHtml, $html, FILE_APPEND);
        }
        file_put_contents($outputHtml, "</table>\n", FILE_APPEND);
        file_put_contents($outputHtml, "Generated " . date("F jS, Y g:ia T"), FILE_APPEND);
        Debugger::info("Generated HTML - ", time() - $start);
    }
}
?>

