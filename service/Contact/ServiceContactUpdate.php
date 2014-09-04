<?php
require_once 'citro/update/UpdateDb.php';

class ServiceContactUpdate extends UpdateDb{
	
	private $_contactUId = NULL;
	
	function __construct($ContactUid){
		$this->_contactUId = $ContactUid;
	}


	/* (non-PHPdoc)
	 * @see UpdateDb::toUpdate()
	 */
	public function toUpdate() {
		
		require_once 'service/Contact/ServiceContact.php';

		$contact = new ServiceContact();
		$backData = $contact->ActionGetSingle($this->_contactUId);

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
		$where = $contTab->getDefaultAdapter()->quoteInto("uid = ?", $this->_contactUId) ;
		$contTab->update($contUpdateData,$where );

		
	}

	
}

?>