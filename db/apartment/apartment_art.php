<?php

/**
 * class zvp_zimmer
 *
 * Description for class zvp_zimmer
 *
 * @author:
*/
class apartment_art extends DBTable {
	
	protected $_TableName = "apartment_art";
	const SP_ID = "id";
	
	const SP_E_DATA = "edatum";
	const SP_V_DATA = "vdatum";
	const SP_USER_CREAT = "usercreat";
	const SP_USER_EDIT = "useredit";
	
	const SP_SYS_NAME = "sys_name";
	const SP_MENUE_NAME = "menu_name";
	const SP_KURZTEXT = "kurztext";
	const SP_IN_MENUE = "in_menu";
	
	
	/**
	 * Erstellt eine Zimmer art wenn schon vorhanden dann giebt er diese zurück
	 * @param integer $userId
	 * @param string $sysName
	 * @return NULL|Zend_Db_Table_Row_Abstract
	 */
	public function createZimmerArt($userId, $sysName){
		$artSelect = $this->select();
		$artSelect->where(self::SP_SYS_NAME." = ?", $sysName);
		$artRow = $this->fetchRow($artSelect); 
		if($artRow === NULL){
			
			if(self::testSysName($sysName) === FALSE) return NULL; 
			
			$data = array();
			$data[self::SP_DATA_CREATE] = DBTable::getDateTime();
			$data[self::SP_DATA_EDIT] = DBTable::getDateTime() ;
			$data[self::SP_USER_CREAT] = $userId;
			$data[self::SP_USER_EDIT] =  $userId ;
			
			$data[self::SP_SYS_NAME] = $sysName;
			$data[self::SP_MENUE_NAME] = $sysName;
			$data[self::SP_IN_MENUE] = 1;
			
			$artRow = $this->createRow($data);
			$artRow->save();
			return $artRow;
			
		}else {
			return $artRow;
		}
		
	}
	
	
	public static function testSysName($value){
		return $value;
	}

}

?>