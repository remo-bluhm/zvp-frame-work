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
	
	private $_art = NULL;
	private $_ort = NULL;
	private $_plz = NULL;
	private $_street = NULL;
	private $_land = NULL;
	private $_landpart = NULL;
	private $_contactId = NULL;
	
	const SP_ID = "id";
	const SP_CONTACT_ID = "contacts_id";
	const SP_ART = "art";
	const SP_LAND = "land";
	const SP_LAND_PART = "landpart";
	const SP_PLZ = "plz";
	const SP_ORT = "ort";
	const SP_STRASSE = "strasse";
	
	
	
	/**
	 * @return the $_art
	 */
	public function getArt() {
		return $this->_art;
	}
	/**
	 * @return the $_ort
	 */
	public function getOrt() {
		return $this->_ort;
	}

	/**
	 * @return the $_plz
	 */
	public function getPlz() {
		return $this->_plz;
	}

	/**
	 * @return the $_street
	 */
	public function getStreet() {
		return $this->_street;
	}

	/**
	 * @return the $_land
	 */
	public function getLand() {
		return $this->_land;
	}

	/**
	 * @return the $_landpart
	 */
	public function getLandpart() {
		return $this->_landpart;
	}
	
	/**
	 * @param NULL $_art
	 */
	public function setArt($art) {
		$result = self::testArt($art);
		if($result !== FALSE)$this->_art = $result;
		return $result;
	}
	
	/**
	 * @param NULL $_ort
	 */
	public function setOrt($ort) {
		$result = self::testOrt($ort);
		if($result !== FALSE)$this->_ort = $result;
		return $result;
	}

	/**
	 * @param NULL $_plz
	 */
	public function setZip($plz) {
		$result = self::testPLZ($plz);
		if($result !== FALSE)$this->_plz = $result;
		return $result;
	}

	/**
	 * @param NULL $_street
	 */
	public function setStreet($street) {
		$result = self::testStreet($street);
		if($result !== FALSE)$this->_street = $result;
		return $result;
	}

	/**
	 * @param NULL $_land
	 */
	public function setLand($land) {
		$result = self::testLand($land);
		if($result !== FALSE)$this->_land = $result;
		return $result;
	}

	/**
	 * @param NULL $_landpart
	 */
	public function setLandpart($landpart) {
		$result = self::testLandPart($landpart);
		if($result !== FALSE)$this->_landpart = $result;
		return $result;
	}

	
	public function setContactId($contactId){
		$result = self::testContactId($contactId);
		if($result!==FALSE){
			$this->_contactId = $result;
		}
		
	}
	
	
	
	function __construct($config = array()) {
		parent::__construct ( $config );
	
	}

	

	
	public static function testArt($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 12) return FALSE;
		return $value;
	}
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
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 150) return FALSE;
		return $value;
	}
	public static function testContactId($value){
		return DBTable::testId($value);
	}
	
	private function generateDate(){
		$data = array();
		if($this->_art !== NULL) $data[self::SP_ART] = $this->_art;
		if($this->_ort !== NULL) $data[self::SP_ORT] = $this->_ort;
		if($this->_plz !== NULL) $data[self::SP_PLZ] = $this->_plz;
		if($this->_street !== NULL) $data[self::SP_STRASSE] = $this->_street;
		if($this->_land !== NULL) $data[self::SP_LAND] = $this->_land;
		if($this->_landpart !== NULL) $data[self::SP_LAND_PART] = $this->_landpart;
		
		return $data;
	}
	
	public function updateData($id){
		if($this->_ort !== NULL){
			$data = $this->generateDate();	
			$where = $this->getAdapter()->quoteInto( self::SP_ID."= ?", $id);
			$this->update($data, $where);
		}
	}

	
	
	public function insertSetDataWithContId($accessId, $contactId, $ort){
		
		$this->setContactId($contactId);
		if($this->_contactId === NULL ) throw new Exception("Die contactId ist nicht valiede!",E_ERROR);
		
		$this->setOrt($ort);
		if($this->_ort === NULL ) throw new Exception("Der Ort ist nicht valiede!",E_ERROR);
		
		
		// Testen des Pflichtfeldes Last Name oder abbruch
		if($this->_ort === NULL)return NULL;

		
		$fields = $this->generateDate();
		$fields[self::SP_ACCESS_CREATE] = $accessId;
		$fields[self::SP_ACCESS_EDIT]=$accessId;
		$this->insert($this->generateDate());
		
	}
	
	
	
	
	
	
	
	
	public function deleteData($id){
		
	}
}

?>