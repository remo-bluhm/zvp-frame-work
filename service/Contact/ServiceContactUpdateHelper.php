<?php
// nun mal schauen
require_once 'citro/update/UpdateDb.php';

class ServiceContactUpdateHelper extends UpdateDb{
	
	private $_contactId = NULL;
	
	/**
	 * @param sting $ContactId Die contact Id
	 */
	function __construct($ContactId){
		$this->_contactId = $ContactId;
	}


	/* (non-PHPdoc)
	 * @see UpdateDb::toUpdate()
	 */
	public function toUpdate() {
		
		require_once 'service/Contact/ServiceContact.php';

		$contact = new ServiceContact();
		$backData = $contact->ActionSingle($this->_contactId,"*");

		return $backData;
	
	}



	/* (non-PHPdoc)
	 * @see DbIUpdate::update()
	 */
	public function update($updateData) {
		

		
		$contData = array(
				"title_name",
				"first_add_name",
				"first_name",
				"last_name",
				"affix_name"
		);
		$contUpdateData = array_intersect_key($updateData, array_flip($contData));
		$contTab = new contacts();
		$contUpdateData['edata'] = $contTab->getDateTime();
		

		$contTab->setDefaultAdapter($this->_connect);
		$sel = $contTab->select();
		$sel->where("uid = ?", $this->_contactId);
		$contRow = $contTab->fetchRow($sel);
		$contRow->setFromArray($contUpdateData);
			
		
		$contId = $contRow->offsetGet("id");	
		$mainAddressId = $contRow->offsetGet("main_contact_address_id");	
		$mainPhoneId = $contRow->offsetGet("main_contact_phone_id");	
		$mainMailId = $contRow->offsetGet("main_contact_email_id");	

		
		
		// Adresse Update
		
		require_once 'db/contact/address/Address.php';
		$address = new Address();
		$address->setDefaultAdapter($this->_connect);
		
		if(isset($updateData['adr_art'])) 		$address->setArt($updateData['adr_art']);
		if(isset($updateData['adr_ort'])) 		$address->setOrt($updateData['adr_ort']);
		if(isset($updateData['adr_plz'])) 		$address->setPlz($updateData['adr_plz']);
		if(isset($updateData['adr_strasse'])) 	$address->setStreet($updateData['adr_strasse']);
		if(isset($updateData['adr_land'])) 		$address->setLand($updateData['adr_land']);
		if(isset($updateData['adr_landpart'])) 	$address->setLandpart($updateData['adr_landpart']);
		
		
		if($mainAddressId !== NULL && $mainAddressId > 0){
			$address->updateData($mainAddressId);		// update
		
		
		}else{
			
			if( Address::testOrt( $updateData["adr_ort"]) !== FALSE ){
				$address->setArt("main");
				$addrPrimId = $address->insertData($contId);			// Insert
				$contRow->offsetSet("main_contact_address_id", $addrPrimId);
			}
		}
		
		// Phone Update
		
		require_once 'db/contact/phone/Phone.php';
		$phone = new Phone();
		$phone->setDefaultAdapter($this->_connect);
		
		if(isset($updateData['phone_number'])) 	$phone->setNumber($updateData['phone_number']);
		if(isset($updateData['phone_text'])) 	$phone->setText($updateData['phone_text']);
		
		if($mainPhoneId !== NULL && $mainPhoneId > 0){
			$phone->updateData($mainPhoneId);		// update
		}else{
			if( Phone::testPhoneNumber( $updateData["phone_number"]) !== FALSE ){
				$phone->setArt("main");
				$newPhoneId = $phone->insertData($contId);			// Insert
				$contRow->offsetSet("main_contact_phone_id", $newPhoneId);
			}
		}
	
		// Email Update
		
		require_once 'db/contact/email/Email.php';
		$email = new Email();
		$email->setDefaultAdapter($this->_connect);
		
		if(isset($updateData['mail_adress'])) 	$email->setEmail($updateData['mail_adress']);
		if(isset($updateData['mail_text'])) 	$email->setText($updateData['mail_text']);
		
		if($mainMailId !== NULL && $mainMailId > 0){
			$email->updateData($mainMailId);		// update
		}else{
			if( Email::testEmail( $updateData["mail_adress"]) !== FALSE ){
				$newMailId = $email->insertData($contId);			// Insert
				$contRow->offsetSet("main_contact_email_id", $newMailId);
			}
		}
		
		
		$contRow->save();
		
		
		
	}

	
}

?>