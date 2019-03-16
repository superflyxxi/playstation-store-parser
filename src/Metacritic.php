<?php
include_once "Debugger.php";
include_once "Properties.php";

class Metacritic {

	private static $BEST_MATCH = array("https://www.metacritic.com/game/playstation-4/",
		"https://www.metacritic.com/game/playstation-3/",
		"https://www.metacritic.com/game/playstation-2/",
		"https://www.metacritic.com/game/playstation/",
		"https://www.metacritic.com/game/pc/");

	private $user_agent;
	private $url;
	private $game;

	public function getUserAgent() {
		return $this->user_agent;
	}

	public function __construct($game) {
		$this->user_agent = "PlayStationStore Parser";
		$this->game = $game;
		$this->url = Properties::getProperty("metacritic.api.url");
	}

	public function find() {
		$arrResults = $this->search();

		// filter out results that are not for a game
		$arrResults = array_filter($arrResults, function($k) {
			$res = $k["refTypeId"] == 30;// Game
			return $res;
		});
		Debugger::verbose("After filtering: ", $arrResults);

		// sort results based on best match
		usort($arrResults, function($a, $b) {
			foreach (self::$BEST_MATCH as $url) {
				$result = self::compareUrl($url, $a["url"], $b["url"]);
				if ($result != 0) {
					return $result;
				}
			}
			
			$name = (strcmp($this->game, $a["name"]) ? + 1 : 0)
				+ (strcmp($this->game, $b["name"]) ? -1 : 0);
			return $name;
		});
		Debugger::verbose("After sorting: ", $arrResults);
		return sizeof($arrResults) > 0 ? $arrResults[0] : NULL;
	}

	private static function compareUrl($url, $a, $b) {
		$ps4 = (strcmp($url, substr($a, 0, strlen($url))) ? +1 : 0)
			+ (strcmp($url, substr($b, 0, strlen($url))) ? -1 : 0);
		return $ps4;
	}	

	public function search() {
		$query = $this->game;
		$data = [
			'search_term' => $query
		];

		$response = $this->request($this->url, $data, "POST");
		$results = json_decode($response, TRUE);

		return isset($results['autoComplete']) ? $results['autoComplete'] : [];
	}

  private function request($url, array $data = [], $method = "GET") {
    $curl = curl_init();

    if ($method == "POST") {
      curl_setopt_array($curl, [
        CURLOPT_POST => TRUE,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => [
          'X-Requested-With: XMLHttpRequest',
          'Referer: ' . $this->url,
        ],
      ]);
    }
    elseif (!empty($data)) {
      $url .= '?' . http_build_query($data);
    }

    curl_setopt_array($curl, [
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_URL => $url,
      CURLOPT_USERAGENT => $this->getUserAgent(),
    ]);

    $response = curl_exec($curl);

    if ($response === FALSE) {
      throw new Exception(curl_error($curl), curl_errno($curl));
    }

    curl_close($curl);

    return $response;
  }
}

