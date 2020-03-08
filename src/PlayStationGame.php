<?php
include_once "Metacritic.php";
include_once "Debugger.php";

class PlayStationGame implements JsonSerializable
{

    private $actualName = "";

    private $shortName = "";

    private $originalPrice = 0.00;

    private $salePrice = 0.00;

    private $storeUrl = "";

    private $arrPlatform = array();

    private $metaCriticLoaded = false;

    private $metaCriticScore = - 1;

    private $metaCriticUrl = "";

    private $url = "";

    private $id = "";

    private $imageUrl = "";

    private $gameContentTypes = array();

    function __construct($decodedJson)
    {
        if (array_key_exists("url", $decodedJson)) {
            $this->url = $decodedJson->url;
        }
        $this->originalPrice = 0.0;
        $this->id = $decodedJson->id;
        $this->actualName = $decodedJson->name;
        $arr = array();
        $gameName = $decodedJson->name;
        Debugger::verbose("PlayStation Game Name: ", $gameName);
        $gameName = str_replace("’", "'", $gameName); // replace weird apostrophe with normal '
        preg_match_all("/[A-Za-z0-9\-'&+!:.]+/", $gameName, $arr);
        $this->shortName = implode($arr[0], " ");
        $this->shortName = str_replace(" :", ":", $this->shortName); // remove space before :
        Debugger::verbose("Converted Game Name: ", $this->shortName);
        if (isset($decodedJson->playable_platform)) {
            foreach ($decodedJson->playable_platform as $platform) {
                $arr = array();
                preg_match_all("/[A-Za-z0-9-\':\.]+/", $platform, $arr);
                $this->arrPlatform[] = implode($arr[0], " ");
            }
        }
        if (isset($decodedJson->default_sku)) {
            $this->originalPrice = $decodedJson->default_sku->price / 100;
            $this->salePrice = $this->originalPrice;
            foreach ($decodedJson->default_sku->rewards as $singleReward) {
                $this->salePrice = min($this->salePrice, $singleReward->price / 100);
                if (isset($singleReward->bonus_price)) {
                    $this->salePrice = min($this->salePrice, $singleReward->bonus_price / 100);
                }
            }
        }
        if (isset($decodedJson->images)) {
            // same the first image as the image
            $this->imageUrl = $decodedJson->images[0]->url;
        }
        /*
         * "gameContentTypesList": [
         * {
         * "name": "Full Game",
         * "key": "FULL_GAME"
         * },
         * {
         * "name": "PS Now",
         * "key": "ps4_cloud"
         * },
         * {
         * "name": "PS Now",
         * "key": "cloud"
         * },
         * ]
         */
        if (isset($decodedJson->gameContentTypesList)) {
            foreach ($decodedJson->gameContentTypesList as $content) {
                $this->gameContentTypes[] = $content->key;
            }
        }
    }

    public function getID()
    {
        return $this->id;
    }

    public function getShortName()
    {
        return $this->shortName;
    }

    public function getPlatforms()
    {
        return $this->arrPlatform;
    }

    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    public function getSalePrice()
    {
        return $this->salePrice;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function isPSNow()
    {
        return in_array("cloud", $this->gameContentTypes) || in_array("ps4_cloud", $this->gameContentTypes);
    }

    public function getGameContentTypes()
    {
        return $this->gameContentTypes;
    }

    private function loadMetaCriticDataIfNecessary()
    {
        if (! $this->metaCriticLoaded) {
            $testName = $this->shortName;
            $testName = preg_replace("/\.\.\./", " ", $testName);
            try {
                $mcResult = self::testMetacritic($testName);
                if ($mcResult !== FALSE) {
                    $this->metaCriticScore = $mcResult["metaScore"];
                    $this->metaCriticUrl = $mcResult["url"];
                }
                $this->metaCriticLoaded = true;
                Debugger::debug("Loaded metacritic score for \"", $this->shortName, "\" = ", $this->metaCriticScore, " (", $this->metaCriticUrl, ")");
            } catch (Exception $e) {
                Debugger::error("Got an error (", $e->getMessage(), ") while getting score for ", $testName);
                Debugger::debug("Skipping this for now.");
            }
        }
    }

    private static function testMetacritic($name)
    {
        $testName = rtrim(trim($name), ":-");
        Debugger::debug("Testing Metacritic for ", $testName);
        $mcApi = new Metacritic($testName);
        $mcResult = $mcApi->find();
        if (isset($mcResult["url"])) {
            return $mcResult;
        } else if (preg_match("/Remaster[ed]*/i", $testName)) {
            return self::testMetacritic(preg_replace("/Remaster[ed]*/i", "", $testName));
        } else if (preg_match("/[a-zA-Z0-9]+ Edition/i", $testName)) {
            return self::testMetacritic(preg_replace("/[a-zA-Z0-9]+ Edition/i", "", $testName));
        } else if (preg_match("/PS[1-5]$/i", $testName)) {
            return self::testMetacritic(preg_replace("/PS[1-5]$/i", "", $testName));
        } else {
            return FALSE;
        }
    }

    public function getMetaCriticScore()
    {
        $this->loadMetaCriticDataIfNecessary();
        return $this->metaCriticScore;
    }

    public function getMetaCriticURL()
    {
        $this->loadMetaCriticDataIfNecessary();
        return $this->metaCriticUrl;
    }

    public function jsonSerialize()
    {
        $json = array();
        $json["id"] = $this->id;
        $json["name"] = $this->actualName;
        $json["url"] = $this->url;
        $json["playable_platform"] = $this->arrPlatform;
        $json["default_sku"]["price"] = $this->originalPrice * 100;
        $json["default_sku"]["rewards"][0]["price"] = $this->salePrice * 100;
        $json["images"][0]["url"] = $this->imageUrl;
        foreach ($this->gameContentTypes as $key) {
            $json["gameContentTypesList"][]["key"] = $key;
        }
        return json_encode($json);
    }
}

?>

