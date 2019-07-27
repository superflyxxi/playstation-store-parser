<?php
include_once "Properties.php";

class Debugger
{

    private static $loglevel = NULL;

    const errorLevel = 1;

    const warnLevel = 2;

    const infoLevel = 3;

    const debugLevel = 4;

    const verboseLevel = 5;

    private static function getLogLevel()
    {
        if (self::$loglevel == NULL) {
            self::$loglevel = Properties::getProperty("log.level");
        }
        return self::$loglevel;
    }

    private static function logmsg($arrData, $level)
    {
        if (self::getLogLevel() >= $level) {
            print_r(date("Y-m-d H:i:s"));
            print_r(":");
            switch ($level) {
                case 1:
                    print_r("ERROR  ");
                    break;
                case 2:
                    print_r("WARN   ");
                    break;
                case 3:
                    print_r("INFO   ");
                    break;
                case 4:
                    print_r("DEBUG  ");
                    break;
                case 5:
                    print_r("VERBOSE");
                    break;
                
                default:
                    break;
            }
            
            print_r(": ");
            foreach ($arrData as $arg) {
                print_r($arg);
            }
            print_r("\n");
        }
    }

    public static function error()
    {
        return self::logmsg(func_get_args(), self::errorLevel);
    }

    public static function info()
    {
        return self::logmsg(func_get_args(), self::infoLevel);
    }

    public static function debug()
    {
        return self::logmsg(func_get_args(), self::debugLevel);
    }

    public static function verbose()
    {
        return self::logmsg(func_get_args(), self::verboseLevel);
    }

    public static function debug2()
    {
        if (self::getLogLevel() >= self::$debugLevel) {
            print_r(date("Y-m-d H:i:s"));
            print_r(":DEBUG: ");
            foreach (func_get_args() as $arg) {
                print_r($arg);
            }
            print_r("\n");
        }
    }
}

?>

