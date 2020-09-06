<?php

include_once "Debugger.php";

function getGameJson($gameId)
{
    $json = file_get_contents("https://store.playstation.com/store/api/chihiro/00_09_000/container/US/en/999/" . $gameId);
    Debugger::verbose($gameId, " JSON: ", $json);
    return $json;
}

class GameJSON
{

    public $url = "";

    public $id = "";

    public $name = "";

    public $default_sku = NULL;

    public $playable_platform = array();

    public $gameContentTypesList = array();
}

/*
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
 */
class ContentType
{

    public function __construct($key, $name)
    {
        $this->key = $key;
        $this->name = $name;
    }

    public $key = NULL;

    public $name = NULL;
}

class ContentTypeCloud extends ContentType
{

    public function __construct()
    {
        parent::__construct("cloud", "PS Now");
    }
}

class ContentTypePS4Cloud extends ContentType
{

    public function __construct()
    {
        parent::__construct("ps4_cloud", "PS Now");
    }
}

class ContentTypeGame extends ContentType
{

    public function __construct()
    {
        parent::__construct("FULL_GAME", "Full Game");
    }
}

class SKUJSON
{

    public function __construct($price = 0)
    {
        $this->price = $price;
    }

    public $price = 0;

    public $rewards = array();
}

class RewardJSON
{

    public $price = 0;
}

?>

