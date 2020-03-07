<?php
include_once "PlayStationGame.php";
include_once "Debugger.php";

class PlayStationGameFilter
{

    // PSN_GAME, FULL_GAME, cloud, ps4_cloud, DEMO
    public $allowedGameContentType = NULL;

    // PS4, PS3
    public $allowedPlayablePlatforms = NULL;

    public function meetsCriteria($game)
    {
        if (NULL != $this->allowedGameContentType) {
            if (count(array_intersect($this->allowedGameContentType, $game->getGameContentTypes())) == 0) {
                Debugger::debug($game->getID(), ": ", $game->getShortName(), " not a valid type (", $game->getGameContentTypes(), ") ", $this->allowedGameContentType);
                return false;
            }
        }
        
        if (NULL != $this->allowedPlayablePlatforms) {
            if (count(array_intersect($this->allowedPlayablePlatforms, $game->getPlatforms())) == 0) {
                Debugger::debug($game->getID(), ": ", $game->getShortName(), " not a valid platform (", $game->getPlatforms(), ") ", $this->allowedPlayablePlatforms);
                return false;
            }
        }
        return true;
    }
}
?>
