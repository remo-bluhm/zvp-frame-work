<?php
require_once 'citro/DBTable.php';

class contact_address extends DBTable {

	


	
	const SP_ID = "id";

	const SP_CONTACT_ID = "contacts_id";
	
	const SP_LAND = "land";
	const SP_LAND_PART = "landpart";
	const SP_PLZ = "plz";	
	const SP_ORT = "ort";
	const SP_STRASSE = "strasse";

	

	public static function testOrt($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 3) return FALSE;
		if(strlen($value) > 150) return FALSE;
		return $value;
	}
	public static function testPLZ($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 3) return FALSE;
		if(strlen($value) > 10) return FALSE;
		return $value;
	}
	public static function testStreet($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 3) return FALSE;
		if(strlen($value) > 150) return FALSE;
		return $value;
	}
	public static function testLand($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 100) return FALSE;
		return $value;
	}
	public static function testLandPart($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 3) return FALSE;
		if(strlen($value) > 150) return FALSE;
		return $value;
	}
	


}

?>