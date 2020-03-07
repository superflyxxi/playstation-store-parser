<?php

class Properties
{

    private static $prop = NULL;

    private static $resourceDir = NULL;

    static function init()
    {
        if (NULL == self::$resourceDir) {
            self::$resourceDir = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "resources";
        }
    }

    // = parse_ini_file("settings.ini");
    public static function getProperty($sName)
    {
        if (self::$prop == NULL) {
            if (file_exists(self::$resourceDir . DIRECTORY_SEPARATOR . "settings_override.ini")) {
                self::$prop = array_merge(parse_ini_file(self::$resourceDir . DIRECTORY_SEPARATOR . "settings.ini"), parse_ini_file(self::$resourceDir . DIRECTORY_SEPARATOR . "settings_override.ini"));
            } else {
                self::$prop = parse_ini_file(self::$resourceDir . DIRECTORY_SEPARATOR . "settings.ini");
            }
        }
        
        return array_key_exists($sName, self::$prop) ? self::$prop[$sName] : NULL;
    }
}

Properties::init();

?>

