<?php
require_once 'citro/DBTable.php';

class contact_phone extends DBTable {


	
	const SP_ID = "id";
		
	const SP_CONTACT_ID = "contacts_id";
	const SP_ART = "art";
	const SP_NUMBER = "number";
	const SP_TEXT = "text";

	
	
	public static function testPhoneNumber($value){
		return $value;
	}
	public static function testText($value){
		return $value;
	}
}

?>