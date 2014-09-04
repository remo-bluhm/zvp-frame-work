<?php
require_once 'citro/DBTable.php';

class contact_email extends DBTable {


	
	const SP_ID = "id";
		
	const SP_CONTACT_ID = "contacts_id";
	const SP_ADRESS = "mailadress";
	const SP_TEXT = "text";

	
	
	public static function testEmail($value){
		// @todo die Email muss noch geprüft werden
		return $value;
	}
	public static function testText($value){
		return $value;
	}
}

?>