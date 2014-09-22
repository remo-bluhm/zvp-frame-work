<?php
require_once 'citro/DBTable.php';

class contact_email extends DBTable {


	private $_email = NULL;
	private $_text = NULL;
	
	const SP_ID = "id";
		
	const SP_CONTACT_ID = "contacts_id";
	const SP_ADRESS = "mailadress";
	const SP_TEXT = "text";

	
	
	/**
	 * @return the $_email
	 */
	public function getEmail() {
		return $this->_email;
	}

	/**
	 * @return the $_text
	 */
	public function getText() {
		return $this->_text;
	}

	/**
	 * @param NULL $_email
	 */
	public function setEmail($email) {
		$result = self::testEmail($email);
		if($result !== FALSE)$this->_email = $result;
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
	
	
	
	private function generateDate(){
		$data = array();
		if($this->_email !== NULL) $data[self::SP_ADRESS] = $this->_email;
		if($this->_text !== NULL) $data[self::SP_TEXT] = $this->_text;
	
		return $data;
	}
	
	public function updateData($id){
		if($this->_email !== NULL){
			$data = $this->generateDate();
			$where = $this->getAdapter()->quoteInto( self::SP_ID."= ?", $id);
			$this->update($data, $where);
		}
	}
	
	
	
	public function insertData($contactId){
		$primaryKey = NULL;
		$contactId = self::testContactId($contactId);
		if($contactId !== FALSE && $this->_email !== NULL){
			$data = $this->generateDate();
			$data[self::SP_CONTACT_ID] = $contactId;
			$primaryKey = $this->insert($data);
		}
		return $primaryKey;
	}
	
	
	
	
	
	
	
	
	public function deleteData($id){
	
	}
	
	
	
}

?>