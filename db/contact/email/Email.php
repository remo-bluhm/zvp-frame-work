<?php
require_once 'citro/DBTable.php';

class Email extends DBTable {

	protected $_name = 'contact_email';
	
	private $_email = NULL;
	private $_text = NULL;
	private $_contactId = NULL;
	
	const SP_ID = "id";
		
	const SP_CONTACT_ID = "contacts_id";
	const SP_ADRESS = "mailadress";
	const SP_TEXT = "text";

	
	private $_insertData = array();
	
	public function clearData(){
		$this->_insertData = array();
	}
	
	/**
	 * @return the $_contactId
	 */
	public function getContactId() {
		if(array_key_exists(self::SP_CONTACT_ID, $this->_insertData)) return $this->_insertData[self::SP_CONTACT_ID];
		return NULL;
	}
	
	/**
	 * @return the $_email
	 */
	public function getEmail() {		
		if(array_key_exists(self::SP_ADRESS, $this->_insertData)) return $this->_insertData[self::SP_ADRESS];
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
	 * @param NULL $_contactId
	 */
	public function setContactId($contactId) {
		$result = self::testContactId($contactId);
		if($result !== FALSE)$this->_insertData[self::SP_CONTACT_ID] = $result;
		return $result;
	}
	
	/**
	 * @param NULL $_email
	 */
	public function setEmail($email) {
		$result = self::testEmail($email);
		if($result !== FALSE)$this->_insertData[self::SP_ADRESS] = $result;
		return $result;
	}

	/**
	 * @param NULL $_text
	 */
	public function setText($text) {
		$result = self::testText($text);
		if($result !== FALSE)$this->_insertData[self::SP_TEXT] = $result;
		return $result;
	}
	
	
	

	public static function testEmail($value){
		require_once 'Zend/Validate/EmailAddress.php';
		$validator = new Zend_Validate_EmailAddress();
		if(!$validator->isValid($value)) return FALSE;
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
		if($this->_email !== NULL){
			$data = $this->generateDate();
			$where = $this->getAdapter()->quoteInto( self::SP_ID."= ?", $id);
			$this->update($data, $where);
		}
	}
	
	
	
	public function insertDataFull($contactId){
		$primaryKey = NULL;
		
		// setzen der Pflichtfelder
		$contactId = $this->setContactId($contactId);
		
		
		if($contactId !== NULL && $this->getEmail() !== NULL){

			$primaryKey = $this->insert($this->_insertData);
		}
		return $primaryKey;
	}
	
	
	
	
	
	
	
	
	public function deleteData($id){
	
	}
	
	
	
}

?>