<?php
require_once 'citro/DBTable.php';
/**
 * class zvp_zimmer
 *
 * Description for class zvp_zimmer
 *
 * @author:
*/
class Resort extends DBTable {
	
	protected $_name = 'resort';

	
	const SP_ID = "id";
	const SP_UID = "uid";
	const SP_NAME = "name";
	
	const SP_DATA_CREATE = "edata";
	const SP_DATA_EDIT = "vdata";
	const SP_ACCESS_CREATE = "access_create";
	const SP_ACCESS_EDIT = "access_edit";
		
	const SP_VISIBIL = "visibil";
	const SP_DELETED = "deleted";
	
	const SP_CITY_ID = "city_id";
	const SP_STREET = "strasse";
		
	const SP_GMAPS_ID = "gmaps_id";
	const SP_KURZTEXT = "kurztext";


	private $_insertData = array();
	
	public function clearData(){
		$this->_insertData = array();
}
	
	
	/**
	 * Setzen des Namens
	 * @param string $value Der Algemeine Name
	 * @return string|NULL Im Fehlerfall NULL
	 */
	public function setName($value){
		$result = self::testName($value);
		if($result !== NULL)$this->_insertData[self::SP_NAME] = $result;
		return $result;
	}
	public function setStreet($value){
		$result = self::testStreet($value);
		if($result !== NULL)$this->_insertData[self::SP_STREET] = $result;
		return $result;
	}
	/**
	 * Setzt die CityId
	 * @param integer $value
	 * @return number|boolean Im Fehlerfall FALSE
	 */
	public function setCityId($value){
	    $result = DBTable::testId($value);
	    if($result !== FALSE)$this->_insertData[self::SP_CITY_ID] = $result;
	    return $result;
	}
	/**
	 * Setzt den Erstellenden AccessId 
	 * @param integer $value
	 * @return number|boolean Im Fehlerfall FALSE
	 */
	public function setAccessCreateId($value){
	    $result = DBTable::testId($value);
	    if($result !== FALSE)$this->_insertData[self::SP_ACCESS_CREATE] = $result;
	    return $result;
	}
	/**
	 * Setzt den Bearbeitenten AccessId
	 * @param integer $value
	 * @return number|boolean Im Fehlerfall FALSE
	 */
	public function setAccessEditId($value){
	    $result = DBTable::testId($value);
	    if($result !== FALSE)$this->_insertData[self::SP_ACCESS_EDIT] = $result;
	    return $result;
	}
	public function setUid($value){
	    
	    $result = self::testUid($value);
	    if($result !== FALSE)$this->_insertData[self::SP_UID] = $result;
	    return $result;
	}
	
	
	
	
	/** 
	 * Prüfen auf Existens der Uid mit rückgabe der Id
	 * @param string $uid die Uid des Datensatzes 
	 * @return integer|bool Die id des Datensatzes wenn nicht gefunden dann FALSE
	 */
	public function existUid($uid){
	    
	    $sel = $this->getAdapter()->select()->from($this->_name,array("id"))->where("uid=?",$uid);
	    $id = $this->getAdapter()->fetchOne($sel);
	    return $id;
	}
	


	
	
	
	public static function testUid($value){
	    if(is_string($value) && strlen($value) < 150 &&  strlen($value) > 2)return $value;
	    return NULL;    
	}
	public static function testName($value){
		if(is_string($value) && strlen($value) < 200 &&  strlen($value) > 2)return $value;
		return NULL;
	}
	public static function testStreet($value){
	    $value = preg_replace( array('/[ ]+/', '/^-|-$/'), array( ' ', ''), $value);
	    if(is_string($value) && strlen($value) < 150 &&  strlen($value) > 2)return $value;

		return NULL;
	}
	
	
	/**
	 * Schreibt ein Resort neu ein
	 * @param integer $accessId Der einzuschreibende Access
	 * @param integer  $cityId Die City Id 
	 * @param string $name Die Uid des Resorort
	 * @param string $name Der Hauptname des Resorts
	 * @param array $data Weitere Daten
	 * @return boolean
	 */
	public function  insertDataFull($accessId, $cityId,$resortUid,$name,$data = array()){

	    
	    // Setzen des erstellers 
	    if($this->setAccessCreateId($accessId) === FALSE) return FALSE ;
	    if($this->setAccessEditId($accessId) === FALSE) return FALSE ;
	    
	    // Prüfen auf vorhandensein der City
	    if($this->setCityId($cityId) === FALSE)return FALSE;
	 
	    // setzen der standart variablen
	    if($this->setName($name) === NULL) return FALSE;

	    // setzen der Uid
	    if($this->setUid( $resortUid ) === NULL) return FALSE;
	    
	    // setzen der Variablen Daten
	    if(is_array($data)){
	 
	       if(array_key_exists("street",$data)){
	           $this->setStreet($data["street"]);	   
	       }
	    }
	    
	    
	    
	    return $this->insert($this->_insertData);

	}
	
	public  function insert($data){
	    $data[self::SP_DATA_CREATE] = self::DateTime();
	    $data[self::SP_DATA_EDIT] = self::DateTime();

	    //print_r($data);
	   return parent::insert($data);
	}
	
	
	
	
	
	
	
	
	
	
}

