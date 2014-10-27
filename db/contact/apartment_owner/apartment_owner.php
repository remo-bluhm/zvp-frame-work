<?php
require_once 'citro/DBTable.php';

class apartment_owner extends DBTable {

	const SP_ID = "id";
	const SP_CONTACT_ID = "contact_id";
	const SP_TEXT = "texts";

	
	public function insertAccess($accessId,array $fields){
		
	}
	
	public function insert($data){
		
		require_once 'db/contact/Contacts.php';
		$contTab = new Contacts();
		$primKey = $contTab->insert($data);
		$dataApOw = array();
		$dataApOw['contact_id'] = $primKey;
		if(!empty($data["app_owner_text"])){
			$dataApOw["text"] = $data["app_owner_text"];
		}
		
		
		
		parent::insert($dataApOw);
		
	}
}

?>