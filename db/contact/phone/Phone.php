<?php
require_once 'citro/DBTable.php';

class Phone extends DBTable {


	protected $_name = 'contact_phone';
	
	private $_contactId = NULL;
	private $_art = NULL;
	private $_number = NULL;
	private $_text = NULL;
	
	
	const SP_ID = "id";
		
	const SP_CONTACT_ID = "contacts_id";
	const SP_ART = "art";
	const SP_NUMBER = "number";
	const SP_TEXT = "text";

	
	
	
	/**
	 * @return the $_contactId
	 */
	public function getContactId() {
		return $this->_contactId;
	}

	/**
	 * @return the $_art
	 */
	public function getArt() {
		return $this->_art;
	}

	/**
	 * @return the $_number
	 */
	public function getNumber() {
		return $this->_number;
	}

	/**
	 * @return the $_text
	 */
	public function getText() {
		return $this->_text;
	}

	/**
	 * @param NULL $_contactId
	 */
	public function setContactId($contactId) {
		$result = self::testContactId($contactId);
		if($result !== FALSE)$this->_contactId = $result;
		return $result;
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
	 * @param NULL $_number
	 */
	public function setNumber($number) {
		$result = self::testPhoneNumber($number);
		if($result !== FALSE)$this->_number = $result;
		return $result;
	}

	/**
	 * @param NULL $_text
	 */
	public function setText($text) {
		$result = self::testText($text);
		if($result !== FALSE)$this->_text = $result;
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
	public static function testContactId($value){
		return DBTable::testId($value);
	}
	
	
	
	public function updateData($id){
		if($this->_number !== NULL){
			$data = $this->generateDate();	
			$where = $this->getAdapter()->quoteInto( self::SP_ID."= ?", $id);
			$this->update($data, $where);
		}
	}
	
	
	
	public function insertSetData($contactId,$number){
		$primaryKey = NULL;
		
		// setzen der Pflichtfelder
		$this->setContactId($contactId);
		$this->setNumber($number);
		
		
		if($this->_contactId !== NULL && $this->_number !== NULL){
			$data = array();
			$data[self::SP_CONTACT_ID] = $contactId;
			$data[self::SP_NUMBER] = $this->_number;
			
			if($this->_art !== NULL)	$data[self::SP_ART] = $this->_art;
			if($this->_text !== NULL) 	$data[self::SP_TEXT] = $this->_text;
			
			$primaryKey = $this->insert($data);
		}
		return $primaryKey;
	}
	
	
	
	public function deleteData($id){
	
	}
	
	
	
	
}

?>