<?php
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
		$contTab = new contacts();
		$contTab->setDefaultAdapter($this->_connect);
		$contData = array(
				"title_name",
				"first_add_name",
				"first_name",
				"last_name",
				"affix_name"
				);
	
		
		$contUpdateData = array_intersect_key($updateData, array_flip($contData));
		$where = $contTab->getDefaultAdapter()->quoteInto("id = ?", $this->_contactId) ;
		$contTab->update($contUpdateData,$where );

		
	}

	
}

?>