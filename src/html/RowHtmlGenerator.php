<?php
include_once "Debugger.php";
include_once "html/HtmlGenerator.php";

class RowHtmlGenerator extends HtmlGenerator
{

    public function write($outputHtml, $title, $gameList, $columnList = array(
        "psNow",
        "price",
        "metaCriticScore"
    ))
    {
        if (count($gameList) == 0) {
            Debugger::warn("No games to write.");
            return FALSE;
        }
        Debugger::beginTimer("generateHtml");
        $outputHtml = Properties::getProperty("html.dir") . "/" . $outputHtml;
        Debugger::info("Writing HTML to ", $outputHtml);

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
        $iMax = min(5, count($gameList));
        $topFive = "The top " . $iMax . " game(s) are ";
        $i = 0;
        foreach ($gameList as $game) {
            if ($i >= $iMax) {
                break;
            }
            if ($i > 0) {
                $topFive .= ", ";
            }
            if ($i > 0 && $i == $iMax - 1) {
                $topFive .= "and ";
            }
            $topFive .= "<a href='" . $webUrl . $game->getID() . "'>" . $game->getDisplayName() . "</a>";
            $i ++;
        }
        $topFive .= ".<!--more--><br />\n";
        file_put_contents($outputHtml, $topFive, FILE_APPEND);
        file_put_contents($outputHtml, "<table border=\"1\">\n", FILE_APPEND);
        $mapFilterableValues = array();
        $rowHtml = "";
        foreach ($gameList as $game) {
            $rowHtml .= $this->generateRowHTML($columnList, $mapFilterableValues, $webUrl, $game);
        }
        $this->writeTableHeader($outputHtml, $columnList, $mapFilterableValues);
        file_put_contents($outputHtml, $rowHtml, FILE_APPEND);
        file_put_contents($outputHtml, "</table>\n", FILE_APPEND);
        file_put_contents($outputHtml, "Generated " . date("F jS, Y g:ia T"), FILE_APPEND);
        file_put_contents($outputHtml, "</body>\n</html>\n", FILE_APPEND);
        Debugger::endTimer("generateHtml");
        return TRUE;
    }
    
    
    /**
     * @param outputHtml
     * @param columnList
     */
     private function writeTableHeader($outputHtml, $columnList, $mapFilterableValues) 
    {
        file_put_contents($outputHtml, "\t<tr>\n\t\t<th id='gameTitle'>Game</th>\n", FILE_APPEND);

        foreach ($columnList as $column) {
            file_put_contents($outputHtml, "\t\t<th id='" . $column . "'>", FILE_APPEND);
            switch ($column) {
                case "platforms":
                    file_put_contents($outputHtml, "Platforms", FILE_APPEND);
                    file_put_contents($outputHtml, $this->generateFilterButtonsHtml($column, $mapFilterableValues), FILE_APPEND);
                    break;

                case "eaAccess":
                    file_put_contents($outputHtml, "On EA Access", FILE_APPEND);
                    file_put_contents($outputHtml, $this->generateFilterButtonsHtml($column, $mapFilterableValues), FILE_APPEND);
                    break;

                case "psNow":
                    file_put_contents($outputHtml, "On PS Now", FILE_APPEND);
                    file_put_contents($outputHtml, $this->generateFilterButtonsHtml($column, $mapFilterableValues), FILE_APPEND);
                    break;

                case "psVr":
                    file_put_contents($outputHtml, "Has PSVR", FILE_APPEND);
                    file_put_contents($outputHtml, $this->generateFilterButtonsHtml($column, $mapFilterableValues), FILE_APPEND);
                    break;

                case "price":
                    file_put_contents($outputHtml, "Price", FILE_APPEND);
                    break;

                case "originalPrice":
                    file_put_contents($outputHtml, "Original Price", FILE_APPEND);
                    break;

                case "salePrice":
                    file_put_contents($outputHtml, "Sale Price", FILE_APPEND);
                    break;

                case "metaCriticScore":
                    file_put_contents($outputHtml, "Metacritic Score", FILE_APPEND);
                    file_put_contents($outputHtml, $this->generateFilterButtonsHtml($column, $mapFilterableValues), FILE_APPEND);
                    break;
            }
            file_put_contents($outputHtml, "</th>\n", FILE_APPEND);
        }
        file_put_contents($outputHtml, "\t</tr>\n", FILE_APPEND);
    }

    private function generateRowHTML($columnList, &$mapFilterableValues, $webUrl, $game)
    {
        $html = "\t\t<td ><a href='" . $webUrl . $game->getID() . "'>" . $game->getDisplayName() . "</a></td>\n";
        $class = "";
        foreach ($columnList as $column) {
            $html .= "\t\t<td>";
            switch ($column) {
                case "platforms":
                    foreach ($game->getPlatforms() as $plt) {
                        $html .= $plt . " ";
                        $mapFilterableValues["platforms"][$plt] = TRUE;
                    	$class .= ' '. $this->getClassNameFor($column, $plt);
                    }
                    break;

                case "psNow":
                    $filter = $game->isPSNow() ? "Yes" : "No";
                    $html .= $filter;
                    $class .= ' '.$this->getClassNameFor($column, $filter);
                    $mapFilterableValues[$column][$filter] = TRUE;
                    break;

                case "psVr":
		    $filter = $game->hasPsvr() ? "Yes" : "No";
                    $html .= $filter;
                    $class .= ' '.$this->getClassNameFor($column, $filter);// ? " onEaAccess" : " offEaAccess";
                    $mapFilterableValues[$column][$filter] = TRUE;
                    break;

                case "eaAccess":
		    $filter = $game->isEAAccess() ? "Yes" : "No";
                    $html .= $filter;
                    $class .= ' '.$this->getClassNameFor($column, $filter);// ? " onEaAccess" : " offEaAccess";
                    $mapFilterableValues[$column][$filter] = TRUE;
                    break;

                case "price":
                    $html .= $game->getSalePrice();
                    if ($game->getOriginalPrice() != $game->getSalePrice()) {
                        $html .= " (<strike>" . $game->getOriginalPrice() . "</strike>)";
                    }
                    break;

                case "originalPrice":
                    $html .= "$" . $game->getOriginalPrice();
                    break;

                case "salePrice":
                    $html .= "$" . $game->getSalePrice();
                    break;

                case "metaCriticScore":
                    $score = $game->getMetaCriticScore();
                    if ($score >= 75) {
                        $filter = "Good";
                    } else if ($score >= 60) {
                        $filter = "Okay";
                    } else if ($score > 0) {
                        $filter = "Bad";
                    } else {
                        $score = "TBD";
			$filter = "TBD";
                    }
                    $mapFilterableValues[$column][$filter] = TRUE;
		    $class .= " " . $this->getClassNameFor($column, $filter);
                    if ($game->getMetaCriticURL() == "") {
                        $html .= "Not Found";
                    } else {
                        $html .= "<a href='" . $game->getMetaCriticURL() . "'>" . $score . "</a>";
                    }
                    break;
            }
            $html .= "</td>\n";
        }

        $html .= "\t</tr>\n";
        $html = "\t<tr class='" . $class . "' >\n" . $html;
        return $html;
    }
    
    private function generateFilterButtonsHtml($prefix, $mapFilterableValues) {
        Debugger::verbose("Filterable map ", $mapFilterableValues);
        if (!array_key_exists($prefix, $mapFilterableValues)) { $mapFilterableValues[$prefix] = array(); }
        $values = array_keys($mapFilterableValues[$prefix]);
	ksort($values);
        $maxValues = count($values);
        
        if ($maxValues < 2){
	    Debugger::debug($prefix, " doesn't have enough values to filter: ", $maxValues);
            return ""; 
        }
        $res = "<br/>(";
        for ($i=-1; $i<$maxValues; $i++)  {
            $button = "<button class='filter' onclick='";
            $j=0;
            foreach ($values as $val) {
                if ($i == $j++ || $i == -1) {
                    $button .= 'showAllClasses(".';
                } else {
                    $button .= 'hideAllClasses(".';
                }
                $button .= $this->getClassNameFor($prefix, $val).'");';
            }
            $button.="'>";
            if ($i < 0) { 
                $button .="All";
            } else {
                $button .= $values[$i];
            }
            $button.="</button>";
            if ($i < $maxValues - 1) {
                $button.="|";
            }
            $res .= $button;
        }
        $res .= ")";
        return $res;
    }

    private function getClassNameFor($column, $value)
    {
        return $column . '' . str_replace(" ", "", $value);
    }
}
?>

