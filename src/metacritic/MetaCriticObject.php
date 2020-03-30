<?php
include_once "cache/CacheObject.php";

class MetaCriticObject implements CacheObject
{

    private $refTypeId = NULL;

    private $metaScore = 0;

    private $url = NULL;

    private $name = NULL;

    private $lastChecked = NULL;

    private $requestedGame = NULL;

    public function __construct($decodedJson, $requestedGame = NULL)
    {
        if ($requestedGame != NULL) {
            $this->requestedGame = $requestedGame;
        } else if (isset($decodedJson->requestedGame)) {
            $this->requestedGame = $decodedJson->requestedGame;
        }
        $this->refTypeId = $decodedJson->refTypeId;
        $this->url = $decodedJson->url;
        $this->metaScore = $decodedJson->metaScore;
        $this->name = $decodedJson->name;
        $this->lastChecked = new DateTime();
        if (isset($decodedJson->lastChecked)) {
            $this->lastChecked->setTimestamp($decodedJson->lastChecked);
        }
    }

    public function jsonSerialize()
    {
        $json = array();
        $json["refTypeId"] = $this->refTypeId;
        $json["name"] = $this->name;
        $json["url"] = $this->url;
        $json["metaScore"] = $this->metaScore;
        $json["lastChecked"] = $this->lastChecked->getTimestamp();
        $json["requestedGame"] = $this->requestedGame;
        return json_encode($json);
    }

    public function isGame()
    {
        return $this->refTypeId == 30;
    }

    public function getScore()
    {
        return $this->metaScore;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLastChecked(): DateTime
    {
        return $this->lastChecked;
    }

    public function getCacheKey(): string
    {
        return $this->requestedGame;
    }
}

?>
