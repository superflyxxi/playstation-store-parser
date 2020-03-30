<?php
include_once "Debugger.php";
include_once "Properties.php";
include_once "metacritic/MetaCriticObject.php";
include_once "cache/MetaCriticCache.php";

class MetacriticApi
{

    private static $BEST_MATCH = array(
        "https://www.metacritic.com/game/playstation-4/",
        "https://www.metacritic.com/game/playstation-3/",
        "https://www.metacritic.com/game/playstation-2/",
        "https://www.metacritic.com/game/playstation/",
        "https://www.metacritic.com/game/pc/",
        "https://www.metacritic.com/game/xbox-one/"
    );

    private $user_agent;

    private $url;

    private $game;

    public function getUserAgent()
    {
        return $this->user_agent;
    }

    public function __construct($game)
    {
        $this->user_agent = "PlayStationStore Parser";
        $this->game = $game;
        $this->url = Properties::getProperty("metacritic.api.url");
    }

    public function find()
    {
        $now = new DateTime();
        $result = MetaCriticCache::getInstance()->get($this->game);
        if ($result == NULL || $result->getLastChecked()->diff($now)->days > Properties::getProperty("metacritic.cache.max.check.days", 30)) {
            Debugger::debug($this->game, " not found in cache or cache is older than 30 days.");
            if ($result != NULL) {
                MetaCriticCache::getInstance()->remove($result);
            }
            $arrResults = $this->search();

            // filter out results that are not for a game
            Debugger::verbose("Before filtering: ", $arrResults);
            $arrResults = array_filter($arrResults, function (MetaCriticObject $k) {
                /*
                 * $res = $k["refTypeId"] == 30; // Game
                 * return $res;
                 */
                return $k->isGame();
            });
            Debugger::verbose("After filtering: ", $arrResults);

            // sort results based on best match
            // 1000 points for having a score
            // 100 points for exact match name
            // 10 points for best match platform; 9 points for 2nd best; 8 points for 3rd etc.
            usort($arrResults, function (MetaCriticObject $a, MetaCriticObject $b) {
                $alphaResult = self::getRankingForGame($this->game, $a);
                $betaResult = self::getRankingForGame($this->game, $b);

                /*
                 * $alphaResult += $a->getScore() > 0 ? 1000 : 0;
                 * $betaResult += $b->getScore() > 0 ? 1000 : 0;
                 *
                 * $nameMatched = 0;
                 * foreach (self::$BEST_MATCH as $url) {
                 * $result = self::compareUrl($url, $a->getUrl(), $b->getUrl());
                 * if ($result < 0) {
                 * $alphaResult += 10 - $nameMatched * 1;
                 * break;
                 * } else if ($result > 0) {
                 * $betaResult += 10 - $nameMatched * 1;
                 * break;
                 * }
                 * $nameMatched ++;
                 * }
                 *
                 * Debugger::verbose($this->game, " = ", $a->getName(), " vs ", $b->getName());
                 * $alphaResult += strcasecmp($this->game, $a->getName()) == 0 ? 100 : 0;
                 * $betaResult += strcasecmp($this->game, $b->getName()) == 0 ? 100 : 0;
                 */
                Debugger::verbose($a->getUrl(), " vs ", $b->getUrl(), " = ", $alphaResult, " vs ", $betaResult);
                return $betaResult - $alphaResult;
            });
            Debugger::verbose("After sorting: ", $arrResults);
            $result = count($arrResults) > 0 ? $arrResults[0] : NULL;
            if ($result != NULL) {
                MetaCriticCache::getInstance()->add($result);
                MetaCriticCache::getInstance()->save();
            }
        }
        return $result;
    }

    private static function getRankingForGame($desiredGame, MetaCriticObject $meta)
    {
        $score = $meta->getScore() > 0 ? 10000 : 0;

        $name = strcasecmp($desiredGame, $meta->getName()) == 0 ? 1000 : 0;

        $systemMatched = count(self::$BEST_MATCH);
        $system = 0;
        foreach (self::$BEST_MATCH as $url) {
            if (strcmp($url, substr($meta->getUrl(), 0, strlen($url))) == 0) {
                $system += $systemMatched * 10;
                break;
            }
            $systemMatched --;
        }

        $total = $score + $name + $system;
        Debugger::verbose($desiredGame, " (", $meta->getUrl(), ") scored ", $total, " = ", $score, " (metaScore) + ", $system, " (system) + ", $name, " (name)");
        return $total;
    }

    private static function compareUrl($url, $a, $b)
    {
        $ps4 = (strcmp($url, substr($a, 0, strlen($url))) ? + 1 : 0) + (strcmp($url, substr($b, 0, strlen($url))) ? - 1 : 0);
        return $ps4;
    }

    private function search()
    {
        $query = $this->game;
        $data = [
            'search_term' => $query
        ];

        $response = $this->request($this->url, $data, "POST");
        $results = json_decode($response);

        Debugger::verbose("Results from search: ", $results);
        if (isset($results->autoComplete)) {
            $arrResults = array();
            foreach ($results->autoComplete as $res) {
                $arrResults[] = new MetaCriticObject($res, $this->game);
            }
            return $arrResults;
        } else {
            return array();
        }

        // return isset($results['autoComplete']) ? $results['autoComplete'] : [];
    }

    private function request($url, array $data = [], $method = "GET")
    {
        $curl = curl_init();

        if ($method == "POST") {
            curl_setopt_array($curl, [
                CURLOPT_POST => TRUE,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => [
                    'X-Requested-With: XMLHttpRequest',
                    'Referer: ' . $this->url
                ]
            ]);
        } elseif (! empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => $this->getUserAgent()
        ]);

        $response = curl_exec($curl);
        if ($response === FALSE) {
            $ex = new Exception(curl_error($curl), curl_errno($curl));
        }

        curl_close($curl);

        if (isset($ex)) {
            throw $ex;
        }

        return $response;
    }
}

