<?php
include_once "playstation/PlayStationContainer.php";

class PlayStationContainerRepository
{

    private $mapContainers = array();

    private static $instance = NULL;

    private function __construct()
    {}

    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new PlayStationContainerRepository();
        }
        return self::$instance;
    }

    public function addContainer(PlayStationContainer $container)
    {
        if (! array_key_exists($container->getID(), $this->mapContainers)) {
            $this->mapContainers[$container->getID()] = $container;
            return $container;
        }
        return $this->mapContainers[$container->getID()];
    }

    public function getContainer($id)
    {
        if (array_key_exists($id, $this->mapContainers)) {
            return $this->mapContainers[$id];
        }
        return NULL;
    }

    public function getAllContainer()
    {
        return $this->mapContainers;
    }
}

?>

