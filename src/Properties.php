<?php

class Properties {

	private static $prop = NULL;// = parse_ini_file("settings.ini");

	public static function getProperty($sName) {
		if (self::$prop == NULL) {
			self::$prop = parse_ini_file("settings.ini");
		}

		return self::$prop[$sName];
	}

}

?>

