<?php
require_once 'citro/DBTable.php';
/**
 * class resourt_orte
 *
 * Description for class resourt_orte
 *
 * @author:
*/
class ResortCity extends DBTable {
	
	protected $_name = 'resort_city';
	
	const SP_ID = "id";
	
	const SP_DATA_CREATE = "edata";
	const SP_DATA_EDIT = "vdata";

	
	const SP_NAME = "name";
	const SP_ZIP = "zip";
	const SP_LAND = "land";
	const SP_LAND_PART = "landpart";
	const SP_LAND_COUNTRY = "landpart";
	const SP_CONTINENT = "continent";
	
	const SP_TEXT = "text";
	const SP_IN_MENUE = "in_menu";
	
	const SP_MAP_X = "map_lat";
	const SP_MAP_Y = "map_lng";
	const SP_MAP_ZOOM = "map_zoom";

	
	const MAX_WHILE_SYSID = 10;
	
	const MAX_NAME_LENGTH = 45;
	const MAX_DESC_LENGTH = 65000;
	
	
	private $_insertData = array();
	
	public function clearData(){
		$this->_insertData = array();
	}
	
	
	public function setAccessCreateId($value){
		$result = DBTable::testId($value);
		if($result !== FALSE)$this->_insertData[self::SP_DATA_CREATE] = $result;
		return $result;
	}
	
	
	
	
	public function setName($value){
		$result = self::testName($value);
		if($result !== NULL)$this->_insertData[self::SP_NAME] = $result;
		return $result;
	}
	public function setZip($plz) {
		$result = self::testZip($plz);
		if($result !== FALSE)$this->_insertData[self::SP_ZIP] = $result;
		return $result;
	}
	public function setLand($land) {
		$result = self::testLand($land);
		if($result !== FALSE)$this->_insertData[self::SP_LAND] = $result;
		return $result;
	}
	public function setLandPart($landpart) {
		$result = self::testLandPart($landpart);
		if($result !== FALSE)$this->_insertData[self::SP_LAND_PART] = $result;
		return $result;
	}
	public function setLandCountry($landcountry) {
		$result = self::testLandCountry($landcountry);
		if($result !== FALSE)$this->_insertData[self::SP_LAND_COUNTRY] = $result;
		return $result;
	}
	public function setContinent($continent) {
		$result = self::testLandCountry($continent);
		if($result !== FALSE)$this->_insertData[self::SP_CONTINENT] = $result;
		return $result;
	}
	
	
	public static function testName($value){
		if(is_string($value)&& strlen($value) < 150 ){
			return $value;
		}
		return NULL;
	}
	public static function testZip($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 3) return FALSE;
		if(strlen($value) > 10) return FALSE;
		$value = trim($value);
		return $value;
	}
	public static function testLand($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 150) return FALSE;
		$value = trim($value);
		return $value;
	}
	public static function testLandPart($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 150) return FALSE;
		$value = trim($value);
		return $value;
	}
	public static function testLandCountry($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 150) return FALSE;
		$value = trim($value);
		return $value;
	}	
	public static function testContinent($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 150) return FALSE;
		$value = trim($value);
		return $value;
	}
	
	
	
	
	
	public function insertDataFull($accessId, $name, $zip, $landId, $data=array()){
		// Die Pflichtparameter
		$this->setAccessCreateId($accessId);
		
		$name = $this->setName($name);
		$zip = $this->setZip($zip);
		$land = $this->setLand($landId);

		if(array_key_exists("land_part",$data)) 		$this->setLandPart($data["land_part"]);
		if(array_key_exists("land_country",$data)) 		$this->setLandCountry($data["land_country"]);
		
		$data['access_create'] = $accessId;
		$data['access_edit'] = $accessId;
		
		if($name !== NULL && $zip !== FALSE){
		
			// prüfen ob die City schon Existiert
			$resultId = $this->exist($name,$zip);
			
			//prüfen ob nicht gefunden wurde
			if($resultId === FALSE){
				
			}
			var_dump($resultId);
		}

// 		// Falls schon existier dann den 
// 		if(is_string($resultFind)){
			
// 		}else{
			
// 		} 
			
	
// 		$ownerIdValue = $this->setOwnerId($ownerId);
	
// 		if($apartmName !== NULL  && $resultFind === FALSE ){
				
// 			if(array_key_exists("name",$data)) 		$this->setName($data["name"]);
				
// 			$insertId = $this->insert($this->_insertData);
// 			$cityRow = $this->find($insertId);
// 			return $cityRow->offsetGet("name_uid");
// 		}else{
// 			return NULL;
// 			//@TODO hier kann noch ein Fehler gesetzt werden
// 		}
	}
	
	
	public function insert($data){
		$data['edata'] = DBTable::DateTime();
		$data['vdata'] = DBTable::DateTime();

		return  parent::insert($data);
	}
	
	
	/**
	 * Findet eine City anhand ihres nameUid 
	 * @param string $name
	 * @return string|FALSE
	 */
	public function exist($name,$zip, $landKey ){
		$sel = $this->getAdapter()->select()->from("resort_city",array("name_uid"))->where("name=?",$name)->where("zip=?",$zip)->where("land=?",$landKey);
//		echo $sel->__toString();
		$value = $this->getAdapter()->fetchOne( $sel ) ;

		return $value;
	}
	/**
	 * Prüfen auf Existens der Uid mit rückgabe der Id
	 * @param string $uid die Uid des Datensatzes
	 * @return integer|bool Die id des Datensatzes wenn nicht gefunden dann FALSE
	 */
	public function existUid($uid){
		 
		$sel = $this->getAdapter()->select()->from($this->_name,array("id"))->where("uid=?",$uid);
		$id = $this->getAdapter()->fetchOne($sel);
		if($id !== FALSE);
		return (int) $id;
	}
	
}

?>