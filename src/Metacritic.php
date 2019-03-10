<?php

class Metacritic {

  const URL = 'https://www.metacritic.com/search/popular';

  protected $user_agent;

  public function getUserAgent() {
    return $this->user_agent;
  }

  public function __construct() {
    $this->user_agent = "PlayStationStore Parser";
  }

  public function search($query) {
    $data = [
      'search_term' => $query
    ];

    $response = $this->request(self::URL, $data, "POST");
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
          'Referer: ' . self::URL,
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

