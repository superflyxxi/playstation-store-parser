<?php

class Properties {

	private static $prop = NULL;// = parse_ini_file("settings.ini");

	public static function getProperty($sName) {
		if (self::$prop == NULL) {
			if (file_exists("settings_override.ini")) {
				self::$prop = array_merge(parse_ini_file("settings.ini"), parse_ini_file("settings_override.ini"));
			} else {
				self::$prop = parse_ini_file("settings.ini");
			}
		}

		return self::$prop[$sName];
	}

}

?>

