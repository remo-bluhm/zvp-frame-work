<?php

require_once 'citro/service-class/AService.php';

/**
 * Dieser Serviece verwaltet alle Contacte
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceContactAddress extends AService {
	
	/**
	 * Der User construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}
	
	
	/**
	 * Giebt eine Liste von Adressen zurück die einen Contact gehören
	 * @param string $contactUid
	 * @return array
	 */
	public function ActionContactListOfContact($contactUid){
	
		require_once 'db/contact/contacts.php';
	
		$db = contacts::getDefaultAdapter();
	
	
		$addressSel = $db->select ();
	
		$spA = array();
		$spA["uid"] = "uid";
		$spA["title_name"] = "title_name";
		$spA["last_name"] = "last_name";
		$spA["first_name"] = "first_name";
		$spA["first_add_name"] = "first_add_name";
		$spA["affix_name"] = "affix_name";
		
		$spA["firma"] = "firma";
		$spA["position"] = "position";
		

	
		$addressSel->from(array('c' => contacts::getTableNameStatic()) ,$spA);

		
 		
 			
 		require_once 'db/contact/address/contact_address.php';
 		$adressSpaltenA = array();
 		$adressSpaltenA['a_id'] = "id";
 		$adressSpaltenA['a_land'] = "land";
 		$adressSpaltenA['a_landpart'] = "landpart";
 		$adressSpaltenA['a_name_row_1'] = "name_row_1";
 		$adressSpaltenA['a_name_row_2'] = "name_row_2";
 		$adressSpaltenA['a_art'] = "art";
 		$adressSpaltenA['a_plz'] = "plz";
 		$adressSpaltenA['a_ort'] = "ort";
 		$adressSpaltenA['a_strasse'] = "strasse";
 		$adressSpaltenA['a_info_text'] = "info_text";
 	
 		$addressSel->joinRight(array('a'=>contact_address::getTableNameStatic()), "c.id = a.contacts_id", $adressSpaltenA );


		$addressSel->where("c.deleted = ?", 0);
		$addressSel->where("c.uid = ?", $contactUid);
			

			
		//$resortSel->limit($count,$offset);
		$resort = $db->fetchAll( $addressSel );
	
		return $resort;
	
	
	}
	
		
	

}

?>