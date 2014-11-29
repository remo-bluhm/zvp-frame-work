<?php
// nun mal schauen
require_once 'citro/update/UpdateDb.php';

class ServiceContactUpdateHelper extends UpdateDb{
	
	private $_contactId = NULL;
	private $_accessId = NULL;
	
	/**
	 * @param sting $ContactId Die contact Id
	 */
	function __construct($ContactId,$accessId){
		
		$this->_contactId = $ContactId;
		$this->_accessId = $accessId;
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
		
		FireBug::setDebug($updateData,"inupdate");
		
		
		if(!is_array($updateData))$updateData = array();
		

		// Bereitstellen der Contact Tabelle
		$contTab = new contacts();
		$contTab->setDefaultAdapter($this->_connect);
		
		// Hollen der Id des Contactes zu der vorgegebenen Uid für abgleichungen der Adressen... 
		$contId = $contTab->exist($this->_contactId);
		// Einschreiben des verändernden Aceesses
		$contTab->setAccessEditId($this->_accessId);
		
		
		
		
		
 		// Adresse Update
  		if(array_key_exists("adresses", $updateData) && is_array($updateData["adresses"])){
	 		require_once 'db/contact/address/Address.php';
	 		$address = new Address();
	 		$address->setDefaultAdapter($this->_connect);

 			foreach ($updateData["adresses"] as $adr){
	
 				$address->clearData();
 				if(is_array($adr) ){
 				// Prüfen auf vorhanden sein der id um ein update auszuführen zu können
	 				if(array_key_exists("adr_id", $adr) && $adr["adr_id"] > 0 ){
	
	 					//Testen auf Main Adresse und setzen
	 					if(array_key_exists("is_main", $adr))if(strtoupper( $adr["is_main"]) == "TRUE" )$contTab->setMainAdressId($adr["adr_id"]);
	
	 					// Updaten der Adresse
	 					$address->updateDataFull($adr);
	 				}else {
	 					//insert
	 					$adressInsertId = $address->insertDataFull($this->_accessId, $contId,$adr);
	 					//Testen auf Main Adresse und setzen
	 					if(array_key_exists("is_main", $adr))if(strtoupper( $adr["is_main"]) == "TRUE" )$contTab->setMainAdressId($adressInsertId);
	 					
	 				}
 				}
 			}
  		}
  		
  		// Phone Update
  		if(array_key_exists("numbers", $updateData) && is_array($updateData["numbers"])){
			
  			require_once 'db/contact/phone/Phone.php';
			$phone = new Phone();
			$phone->setDefaultAdapter($this->_connect);
  		
  			foreach ($updateData["numbers"] as $numb){

  				$phone->clearData();
  				if(is_array($numb)){
  				
  					// Prüfen auf vorhanden sein der id um ein update auszuführen zu können
	  				if(array_key_exists("phone_id", $numb) && $numb["phone_id"] > 0  ){
	  					//Testen auf Main Adresse und setzen
	  					if(array_key_exists("is_main", $numb))if(strtoupper( $numb["is_main"]) == "TRUE" )$contTab->setMainPhoneId($numb["phone_id"]);
	  				
	  					// Updaten der Adresse
	  					$phone->updateDataFull($numb);
	  				}else {
	  				
	  					//insert
			  			$phoneInsertId = $phone->insertDataFull($this->_accessId, $contId,$numb);
	  					//Testen auf Main Adresse und setzen
	  					if(array_key_exists("is_main", $numb))if(strtoupper( $numb["is_main"]) == "TRUE" )$contTab->setMainPhoneId($phoneInsertId);
	  		
	  				}
	  			}
  			}
  		}
		
  		
  		// Email Update
  		if(array_key_exists("emails", $updateData) && is_array($updateData["emails"])){
  				
			require_once 'db/contact/email/Email.php';
			$email = new Email();
			$email->setDefaultAdapter($this->_connect);
  		
  			foreach ($updateData["emails"] as $maild){
  	
  				$email->clearData();
  				if(is_array($maild)){
  	
  					// Prüfen auf vorhanden sein der id um ein update auszuführen zu können
  					if(array_key_exists("email_id", $maild) && $maild["email_id"] > 0  ){
  						//Testen auf Main Adresse und setzen
  						if(array_key_exists("is_main", $maild))if(strtoupper( $maild["is_main"]) == "TRUE" )$contTab->setMainEmailId($maild["email_id"]);
  							
  						// Updaten der Adresse
  						$email->updateDataFull($maild);
  					}else {
  							
  						// insert
  						$mailInsertId = $email->insertDataFull($this->_accessId, $contId,$maild);
  						// Testen auf Main Adresse und setzen
  						if(array_key_exists("is_main", $maild))if(strtoupper( $maild["is_main"]) == "TRUE" )$contTab->setMainEmailId($mailInsertId);
  						 
  					}
  				}
  			}
  		}
		
		// Update durchfüren
		$contTab->updateDataFull($this->_contactId, $updateData);	
		




  		


// 		if(isset($updateData['mail_adress'])) 	$email->setEmail($updateData['mail_adress']);
// 		if(isset($updateData['mail_text'])) 	$email->setText($updateData['mail_text']);
		
// 		if($mainMailId !== NULL && $mainMailId > 0){
// 			$email->updateData($mainMailId);		// update
// 		}else{
// 			if( Email::testEmail( $updateData["mail_adress"]) !== FALSE ){
// 				$newMailId = $email->insertData($contId);			// Insert
// 				$contRow->offsetSet("main_contact_email_id", $newMailId);
// 			}
// 		}
		
		
		
		
	}

	
}

?>