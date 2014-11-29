<?php
require_once 'citro/DBTable.php';

class Address extends DBTable {

	protected $_name = 'contact_address';

//	protected $_dependentTables = array('Contactse');
	protected $_referenceMap    = array(
			'Contacts' => array(
					'columns'           => array('contacts_id'),
					'refTableClass'     => 'Contacts',
					'refColumns'        => array('id')
			)
	);
	
	
	function __construct($config = array()) {
		parent::__construct ( $config );
	
	}
	

	
	private $_insertData = array();
	
	const SP_ID = "id";
	
	const SP_DATA_CREATE = "edata";
	const SP_DATA_EDIT = "vdata";
	const SP_ACCESS_CREATE = "access_create";
	const SP_ACCESS_EDIT = "access_edit";
	const SP_CONTACT_ID = "contacts_id";
	
	const SP_ART = "art";
	const SP_NAMELINE = "nameline";
	const SP_LAND = "land";
	const SP_LAND_PART = "landpart";
	const SP_ZIP = "plz";
	const SP_ORT = "ort";
	const SP_STREET = "strasse";
	const SP_INFOTEXT = "infotext";
	
	
	public function clearData(){
		$this->_insertData = array();
	}
	
	
	
	/**
	 * @return the $_art
	 */
	public function getArt() {
		if(array_key_exists(self::SP_ART, $this->_insertData)) return $this->_insertData[self::SP_ART];
		return NULL;
	}
	
	/**
	 * @return the $_art
	 */
	public function getNameLine() {
		if(array_key_exists(self::SP_NAMELINE, $this->_insertData)) return $this->_insertData[self::SP_NAMELINE];
		return NULL;
	}
	
	/**
	 * @return the $_ort
	 */
	public function getOrt() {
		if(array_key_exists(self::SP_ORT, $this->_insertData)) return $this->_insertData[self::SP_ORT];
		return NULL;
	}

	/**
	 * @return the $_plz
	 */
	public function getZip() {
		if(array_key_exists(self::SP_ZIP, $this->_insertData)) return $this->_insertData[self::SP_ZIP];
		return NULL;
	}

	/**
	 * @return the $_street
	 */
	public function getStreet() {
		if(array_key_exists(self::SP_STREET, $this->_insertData)) return $this->_insertData[self::SP_STREET];
		return NULL;
	}

	/**
	 * @return the $_land
	 */
	public function getLand() {
		if(array_key_exists(self::SP_LAND, $this->_insertData)) return $this->_insertData[self::SP_LAND];
		return NULL;
	}

	/**
	 * @return the $_landpart
	 */
	public function getLandpart() {
		if(array_key_exists(self::SP_LAND_PART, $this->_insertData)) return $this->_insertData[self::SP_LAND_PART];
		return NULL;
	}
	
	
	
	
	
	
	
	/**
	 * @param NULL $_art
	 */
	public function setArt($art) {
		$result = self::testArt($art);
		if($result !== FALSE)$this->_insertData[self::SP_ART] = $result;
		return $result;
	}
	
	/**
	 * @param NULL $_art
	 */
	public function setNameLine($value) {
		$result = self::testNameLine($value);
		if($result !== FALSE)$this->_insertData[self::SP_NAMELINE] = $result;
		return $result;
	}
	
	/**
	 * @param NULL $_ort
	 */
	public function setOrt($ort) {
		$result = self::testOrt($ort);
		if($result !== FALSE)$this->_insertData[self::SP_ORT] = $result;
		return $result;
	}

	/**
	 * @param NULL $_plz
	 */
	public function setZip($plz) {
		$result = self::testZip($plz);
		if($result !== FALSE)$this->_insertData[self::SP_ZIP] = $result;
		return $result;
	}

	/**
	 * @param NULL $_street
	 */
	public function setStreet($street) {
		$result = self::testStreet($street);
		if($result !== FALSE)$this->_insertData[self::SP_STREET] = $result;
		return $result;
	}

	/**
	 * @param NULL $_land
	 */
	public function setLand($land) {
		$result = self::testLand($land);
		if($result !== FALSE)$this->_insertData[self::SP_LAND] = $result;
		return $result;
	}

	/**
	 * @param NULL $_landpart
	 */
	public function setLandpart($landpart) {
		$result = self::testLandPart($landpart);
		if($result !== FALSE)$this->_insertData[self::SP_LAND_PART] = $result;
		return $result;
	}

	
	public function setContactId($contactId){
		$result = self::testContactId($contactId);
		if($result !== FALSE)$this->_insertData[self::SP_CONTACT_ID] = $result;
		return $result;
		
	}
	
	public function setAccessCreateId($id){
		$result = self::testContactId($id);
		if($result !== FALSE)$this->_insertData[self::SP_ACCESS_CREATE] = $result;
		return $result;
	
	}
	
	public function setAccessEditId($id){
		$result = self::testContactId($id);
		if($result !== FALSE)$this->_insertData[self::SP_ACCESS_EDIT] = $result;
		return $result;
	
	}
	
	


	

	
	public static function testArt($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 12) return FALSE;
		$value = trim($value);
		return $value;
	}
	public static function testNameLine($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 200) return FALSE;
		$value = trim($value);
		return $value;
	}
	
	public static function testOrt($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 3) return FALSE;
		if(strlen($value) > 150) return FALSE;
		$value = trim($value);
		return $value;
	}
	public static function testZip($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 3) return FALSE;
		if(strlen($value) > 10) return FALSE;
		$value = trim($value);
		return $value;
	}
	public static function testStreet($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 3) return FALSE;
		if(strlen($value) > 150) return FALSE;
		$value = trim($value);
		return $value;
	}
	public static function testLand($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 100) return FALSE;
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
	/**
	 * Testet die ContactId
	 * @param Integer $value
	 * @return Integer|boolean im Feher fall False ansonsten die Id
	 */
	public static function testContactId($value){
		return DBTable::testId($value);
	}
	

	
	public function updateDataFull($data,$id = NULL){
	
		if(!is_array($data))$data = array();
		
		if(array_key_exists("adr_id",$data)) 		$id = $data["adr_id"];
		if(array_key_exists("adr_art",$data)) 		$this->setArt($data["adr_art"]);
		if(array_key_exists("adr_nameline",$data)) 	$this->setNameLine($data["adr_nameline"]);
		if(array_key_exists("adr_street",$data)) 	$this->setStreet($data["adr_street"]);
		if(array_key_exists("adr_ort",$data)) 		$this->setOrt($data["adr_ort"]);
		if(array_key_exists("adr_zip",$data)) 		$this->setZip($data["adr_zip"]);
		if(array_key_exists("adr_land",$data)) 		$this->setLand($data["adr_land"]);
		if(array_key_exists("adr_landpart",$data)) 	$this->setLandpart($data["adr_landpart"]);

		if(self::testId($id) !== FALSE){
	
			$where = $this->getAdapter()->quoteInto( self::SP_ID."= ?", $id);
			$this->update($this->_insertData, $where);	
		}	

	}
	

	
	public function insertDataFull($accessId, $contactId, $data = array()){
		$insertId = NULL;
		
		// Testen des Pflichtfeldes Contact Id oder abbruch
		$contactId = $this->setContactId($contactId);
		
		if(array_key_exists("adr_art",$data)) 		$this->setArt($data["adr_art"]);
		if(array_key_exists("adr_nameline",$data)) 	$this->setNameLine($data["adr_nameline"]);
		if(array_key_exists("adr_street",$data)) 	$this->setStreet($data["adr_street"]);
		if(array_key_exists("adr_ort",$data)) 		$this->setOrt($data["adr_ort"]);
		if(array_key_exists("adr_zip",$data)) 		$this->setZip($data["adr_zip"]);
		if(array_key_exists("adr_land",$data)) 		$this->setLand($data["adr_land"]);
		if(array_key_exists("adr_landpart",$data)) 	$this->setLandpart($data["adr_landpart"]);
		
		if($this->getNameLine() !== NULL && $contactId !== FALSE){

			$insertId = $this->insert($this->_insertData);
	
		}
		
		return $insertId;
	}
	
	
	public function insert($data){

		return  parent::insert($data);

	}
	
	
	
	
	
	public function deleteData($id){
		
	}
}

?>