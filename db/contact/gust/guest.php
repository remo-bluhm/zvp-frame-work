<?php
require_once 'citro/DBTable.php';

class Guest extends DBTable {


	protected $_name = 'contact_phone';
	
	const SP_ID = "id";
	const SP_CONTACT_ID = "contacts_id";
	const SP_INFO_TEXT_PRIVATE = "info_text_private"; // soll nur sichtbar für den adjustor sein

	private $_insertData = array();
	
	public function clearData(){
		$this->_insertData = array();
	}

	/**
	 * @return the $_art
	 */
	public function getInfoTextPrivat() {
		if(array_key_exists(self::SP_INFO_TEXT_PRIVATE, $this->_insertData)) return $this->_insertData[self::SP_INFO_TEXT_PRIVATE];
		return NULL;
	}
	/**
	 * @param NULL $_art
	 */
	public function setArt($art) {
		$result = self::testArt($art);
		if($result !== FALSE)$this->_insertData[self::SP_INFO_TEXT_PRIVATE] = $result;
		return $result;
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


	public static function testInfoTextPrivate($value){
		if(empty($value)) return FALSE;
		if(strlen($value) < 1) return FALSE;
		if(strlen($value) > 255) return FALSE;
		return $value;
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