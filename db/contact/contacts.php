<?php
require_once 'citro/DBTable.php';

class contacts extends DBTable {

	

	
	const SP_ID = "id";
	const SP_UNID = "uid";
	
	const SP_DATA_CREATE = "edata";
	const SP_DATA_EDIT = "vdata";
	const SP_ACCESS_CREATE = "access_create";
	const SP_ACCESS_EDIT = "access_edit";
	const SP_DELETE = "deleted";

	
	const SP_TITLE = "title";
	const SP_FIRST_NAME = "first_name";
	const SP_FIRST_ADD_NAME = "first_add_name";	
	const SP_LAST_NAME = "last_name";
	const SP_AFFIX_NAME = "affix_name";
	
	const SP_FIRMA = "firma";
	const SP_FIRMA_POSITION = "position";
	
	const SP_KURZINFO = "kurz_info";

	const SP_ADRESS_ID = "main_contact_address_id";
	const SP_EMAIL_ID = "main_contact_email_id";
	const SP_PHONE_ID = "main_contact_phone_id";
	

	public function getKontaktWithId($id){
		
		$Select = $this->select ();
		$Select->where ( self::SP_ID . '= ?', $id );
		$Select->where ( self::SP_DELETE . '= ?', 0 );
		$Select->where ( self::SP_VISIBIL . '= ?', 1 );
			
		$row = $this->fetchRow ( $Select );
		return $row;
	}
	
	
	
// 	public function newContact($userId, $data){
		
// 		if(is_array($data) && count($data) > 0){
			
// 			$data[self::SP_DATA_CREATE] = DBTable::getDateTime();
// 			$data[self::SP_DATA_EDIT] = DBTable::getDateTime() ;
// 			$data[self::SP_USER_CREATE] = $userId;
// 			$data[self::SP_USER_EDIT] =  $userId ;
			
// 			$kontaktRow =  $this->createRow($data);
// 			$kontaktRow->save();
			
// 			return $kontaktRow;
// 		}
		

// 		return FALSE;
		
// 	}
	
// 	public function insert($data,$createAccessId){
	
// 		$data[self::SP_DATA_CREATE] = DBTable::getDateTime();
// 		$data[self::SP_DATA_EDIT] = DBTable::getDateTime() ;
// 		$data[self::SP_USER_CREATE] = $createAccessId;
// 		$data[self::SP_USER_EDIT] =  $createAccessId ;
// 		$data[self::SP_DELETE] =  0 ;
// 		$primaryId = parent::insert($data);
// 		return $primaryId;
// 	}
	
	

	/**
	 * Erstellt eine eindeutige Id des Contactes
	 * @param string $prefix eindeutigen Bezeichner der Firma oder der Datenbank
	 * @return string
	 */
	public static function generateUnId($prefix){
		return uniqid ($prefix."-");
	}
	
	
	
	public static function testTitle($value){
		if(strlen($value) > 50) return FALSE;
		return $value;
	}
	public static function testFirstName($value){
		if(is_string($value) && strlen($value) < 200 &&  strlen($value) > 2){
			return $value;
		}
		return FALSE;
	}
	public static function testFirstAddName($value){
		return $value;
	}
	/**
	 * Testet den Nachnamen
	 * @param string $value Nachname
	 * @return string|boolean Im Fehlerfall FALSE
	 */
	public static function testLastName($value){
		if(is_string($value) && strlen($value) < 200 &&  strlen($value) > 2){
			return $value;
		}
		return FALSE;
	}
	public static function testAffixName($value){
		return $value;
	}

	
	public static function testFirma($value){
		return $value;
	}
	public static function testPosition($value){
		return $value;
	}
	
	public static function testKurzInfo($value){
		return $value;
	}
}

?>