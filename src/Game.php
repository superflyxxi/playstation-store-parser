<?php

class Game {

	public $actualName="";
	public $name="";
	public $originalPrice=0.00;
	public $salePrice=0.00;
	public $metaCritic=0;

	function __construct($oName, $oPrice, $oSalePrice) {
		$arrName;
		preg_match_all("/[A-Za-z0-9-\':\.]+/", $oName, $arrName);
		$this->name=implode($arrName[0], " ");
		$this->actualName = $oName;
		$this->originalPrice=$oPrice;
		$this->salePrice=$oSalePrice;
	}	
}
?>

