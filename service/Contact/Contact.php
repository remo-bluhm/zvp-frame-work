<?php

/** 
 * @author Max Plank
 * 
 * 
 */

class Contact {
	
	private $_contactRow = NULL;
	
	private $_contactId = NULL;
	private $_fullName = NULL;
	private $_firstName = NULL;
	private $_lastName = NULL;
	
	
		
	
	
	/**
	 * @return the $_lastName
	 */
	public function getLastName() {
		return $this->_lastName;
	}

	/**
	 * @param NULL $_lastName
	 */
	public function setLastName($_lastName) {
		$this->_lastName = $_lastName;
	}

	/**
	 * @return the $_firstName
	 */
	public function getFirstName() {
		return $this->_firstName;
	}

	/**
	 * @param NULL $_firstName
	 */
	public function setFirstName($_firstName) {
		$this->_firstName = $_firstName;
	}

	/**
	 * @return the $_fullName
	 */
	public function getFullName() {
		return $this->_firstName." ".$this->_lastName;
	}

	/**
	 * @param NULL $_fullName
	 */
	public function setFullName($_fullName) {
		$this->_fullName = $_fullName;
	}

	function __construct($contactData = NULL) {
		
		if($contactData instanceof Zend_Db_Table_Row_Abstract){
			$contactData = $contactData->toArray();
		}
		
		if(is_array($contactData)){
		
			require_once 'db/contact/Contacts.php';
			
			if(array_key_exists(Contacts::SP_ID, $contactData)){
				$this->_contactId = $contactData[Contacts::SP_ID];
			}
			
			if(array_key_exists(Contacts::SP_FIRST_NAME, $contactData)){
				$this->_firstName = $contactData[Contacts::SP_FIRST_NAME];
			}
			
			if(array_key_exists(Contacts::SP_LAST_NAME, $contactData)){
				$this->_lastName = $contactData[Contacts::SP_LAST_NAME];
			}
			
			
			if(array_key_exists(Contacts::SP_ID, $contactData)){
				$this->setContactId($contactData[Contacts::SP_ID]);
			}
		}
		
	}
	
	public function setContactId($ContactId){
		$this->_contactId = $ContactId;
	}
	public function getContactId(){
		return $this->_contactId;
	}
	
	
	
	
	
	
}

?>