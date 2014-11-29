<?php
require_once 'citro/DBTable.php';

class Phone extends DBTable {


	protected $_name = 'contact_phone';
	
	const SP_ID = "id";
		
	const SP_DATA_CREATE = "edata";
	const SP_DATA_EDIT = "vdata";
	const SP_ACCESS_CREATE = "access_create";
	const SP_ACCESS_EDIT = "access_edit";
	const SP_CONTACT_ID = "contacts_id";
	
	const SP_ART = "art";
	const SP_NUMBER = "number";
	const SP_TEXT = "text";

	private $_insertData = array();
	
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
	 * @return the $_number
	 */
	public function getNumber() {
		if(array_key_exists(self::SP_NUMBER, $this->_insertData)) return $this->_insertData[self::SP_NUMBER];
		return NULL;
	}

	/**
	 * @return the $_text
	 */
	public function getText() {
		if(array_key_exists(self::SP_TEXT, $this->_insertData)) return $this->_insertData[self::SP_TEXT];
		return NULL;
	}

	/**
	 * @return the $_contactId
	 */
	public function getContactId() {
		if(array_key_exists(self::SP_CONTACT_ID, $this->_insertData)) return $this->_insertData[self::SP_CONTACT_ID];
		return NULL;
	}
	

	
	

	public function setContactId($contactId){
		$result = DBTable::testId($contactId);
		if($result !== FALSE)$this->_insertData[self::SP_CONTACT_ID] = $result;
		return $result;
	
	}
	public function setAccessCreateId($id){
		$result = DBTable::testId($id);
		if($result !== FALSE)$this->_insertData[self::SP_ACCESS_CREATE] = $result;
		return $result;
	}
	public function setAccessEditId($id){
		$result = DBTable::testId($id);
		if($result !== FALSE)$this->_insertData[self::SP_ACCESS_EDIT] = $result;
		return $result;
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
	 * @param NULL $_number
	 */
	public function setNumber($value) {
		$result = self::testPhoneNumber($value);
		if($result !== FALSE)$this->_insertData[self::SP_NUMBER] = $result;
		return $result;
	}

	/**
	 * @param NULL $_text
	 */
	public function setText($value) {
		$result = self::testText($value);
		if($result !== FALSE)$this->_insertData[self::SP_TEXT] = $result;
		return $result;
	}

	
	

	

	public static function testArt($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 12) return FALSE;
		return $value;
	}
	public static function testPhoneNumber($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 3) return FALSE;
		if(strlen($value) > 100) return FALSE;
		return $value;
	}
	public static function testText($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 255) return FALSE;
		return $value;
	}
	
	
	
	public function updateDataFull($data = array(), $id = NULL){
		
		if(!is_array($data))$data = array();
		
		if(array_key_exists("phone_id",$data)) 		$id = $data["phone_id"];
		if(array_key_exists("phone_art",$data)) 	$this->setArt($data["phone_art"]);
		if(array_key_exists("phone_number",$data)) 	$this->setNumber($data["phone_number"]);
		if(array_key_exists("phone_text",$data)) 	$this->setText($data["phone_text"]);


		if(self::testId($id) !== FALSE){
	
			$where = $this->getAdapter()->quoteInto( self::SP_ID."= ?", $id);
			$this->update($this->_insertData, $where);	
		}	
	}
	
	
	
	public function insertDataFull($accessId, $contactId, $data = array()){
		$primaryKey = NULL;
		
		// setzen der Pflichtfelder
		$contactId = $this->setContactId($contactId);
		
		if(array_key_exists("phone_art",$data)) $this->setArt($data["phone_art"]);
		if(array_key_exists("phone_number",$data)) $this->setNumber($data["phone_number"]);
		if(array_key_exists("phone_text",$data)) $this->setText($data["phone_text"]);
	
		if($contactId !== NULL && $this->getNumber() !== NULL){
			$primaryKey = $this->insert($this->_insertData);
		}
		return $primaryKey;
	}
	
	public function insert($data){

		
		return  parent::insert($data);
	
	}
	
	
	
	public function deleteData($id){
	
	}
	
	
	
	
}

?>