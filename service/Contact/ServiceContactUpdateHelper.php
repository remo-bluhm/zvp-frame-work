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
		$backData = $contact->ActionGetSingle($this->_contactId);

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
		$contRow->save();

	
		
		$contId = $contRow->offsetGet("id");	
		$mainAddressId = $contRow->offsetGet("main_contact_address_id");	
		
		

	
		 if($mainAddressId !== NULL && $mainAddressId > 0){
				
				$addrData = array(
						"adr_land",
						"adr_landpart",
						"adr_plz",
						"adr_ort",
						"adr_strasse"
				);
				$addrUpdateData = array_intersect_key($updateData, array_flip($addrData));
				$this->_connect->update("contact_address", $addrUpdateData,$this->_connect->quoteInto("id = ?", $mainAddressId));
		}else{
			// Insert
		}
		
	}

	
}

?>