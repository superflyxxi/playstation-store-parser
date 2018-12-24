<?php

include_once "metacritic_api-1.2/metacritic.php";
include_once "Debugger.php";

class PlayStationGame {

	private $actualName="";
	private $shortName="";
	private $originalPrice=0.00;
	private $salePrice=0.00;
	private $storeUrl = "";
	private $arrPlatform = array();
	private $metaCriticLoaded = false;
	private $metaCriticScore = -1;
	private $metaCriticUrl = "";
	private $url = "";
	private $id = "";
	
	function __construct($json) {
	    if (array_key_exists("url", $json)) {
	        $this->url = $json->url;
	    }
		$this->id = $json->id;
		$this->actualName = $json->name;
		$arr = array();
		$gameName = $json->name;
		Debugger::debug("PlayStation Game Name: ", $gameName);
		$gameName = str_replace("’", "'", $gameName); // replace weird apostrophe with normal '
		preg_match_all("/[A-Za-z0-9\-'&+!:.]+/", $gameName, $arr);
		$this->shortName = implode($arr[0], " ");
		$this->shortName = str_replace(" :", ":", $this->shortName); // remove space before :
		Debugger::debug("Converted Game Name: ", $this->shortName);
		foreach ($json->playable_platform as $platform) {
			$arr = array();
			preg_match_all("/[A-Za-z0-9-\':\.]+/", $platform, $arr);
			$this->arrPlatform[] = implode($arr[0], " ");
		}
		$this->originalPrice = $json->default_sku->price/100;
		$this->salePrice = $this->originalPrice;
		foreach ($json->default_sku->rewards as $singleReward) {
			$this->salePrice = min($this->salePrice, $singleReward->price/100);
			if (isset($singleReward->bonus_price)) {
				$this->salePrice = min($this->salePrice, $singleReward->bonus_price/100);
			}
		}
	}

	public function getID() {
		return $this->id;
	}

	public function getShortName() {
		return $this->shortName;
	}

	public function getPlatforms() {
		return $this->arrPlatform;
	}

	public function getOriginalPrice() {
		return $this->originalPrice;
	}

	public function getSalePrice() {
		return $this->salePrice;
	}

	public function getURL() {
		return $this->url;
	}
	
	private function loadMetaCriticDataIfNecessary() {
	    if (!$this->metaCriticLoaded) {
	        $arrSystems = array("playstation-4", "pc", "playstation-2");
	        foreach ($arrSystems as $system) {
	            if ($this->metaCriticScore < 0) {
	                $mcApi = new MetacriticAPI($system);
	                $testName = $this->shortName;
	                $testName = preg_replace("/\.\.\./", " ", $testName);
	                $arrGameName = explode(" ", $this->shortName);
	                Debugger::debug("Original Game Name: ", $this->shortName);
	                for ($i=count($arrGameName); $i>0; $i--) {
	                    $testName = "";
	                    for ($j=0; $j<$i; $j++) {
	                        $testName.=$arrGameName[$j]." ";
	                    }
	                    $testName = trim($testName);
	                    Debugger::debug("Testing Metacritic for system (", $system, "): ", $testName);
	                    $this->metaCriticUrl = $mcApi->get_metacritic_page($testName);
	                    $mcResult = json_decode($mcApi->get_metacritic_scores());
	                    if (isset($mcResult->metascritic_score) && $mcResult->metascritic_score > 0) {
	                        $this->metaCriticScore = $mcResult->metascritic_score;
	                        break;
	                    }
	                }
	            }
	        }
	        if ($this->metaCriticScore < 0) {
	            $this->metaCriticScore = 0;
	        }
	        $this->metaCriticLoaded = true;
	        Debugger::debug("Loaded metacritic score for \"", $this->shortName, "\" = ", $this->metaCriticScore);
	    }
	}

	public function getMetaCriticScore() {
	    $this->loadMetaCriticDataIfNecessary();
		return $this->metaCriticScore;
	}
	
	public function getMetaCriticURL() {
	    $this->loadMetaCriticDataIfNecessary();
	    return $this->metaCriticUrl;
	}
	
}
	
?>

