<?php

class Properties {

	private static $prop = NULL;// = parse_ini_file("settings.ini");

	public static function getProperty($sName) {
		if (self::$prop == NULL) {
			self::$prop = array_merge(parse_ini_file("settings.ini"), parse_ini_file("settings_override.ini"));
		}

		return self::$prop[$sName];
	}

}

?>

