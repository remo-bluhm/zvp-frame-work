<?php
require_once 'citro/update/UpdateFactory.php';


class ChronologicalFactory extends UpdateFactory{

	protected $accessId = NULL;
	protected $key = NULL;
	protected $type = NULL;
	
	/**
	 * Inizialisiert die Klasse
	 * Und setzt den UpdateObjekt den connect.
	 *
	 * @param string $type Bezeichner um welchen Datenbereich es sich handelt zb Contacts Wohnung ..
	 * @param int $accessId Der den Update ausführt
	 * @param Zend_Db_Adapter_Abstract $dbConnect
	 * @param UpdateDb $updateObj
	 */
	function __construct($type, $accessId, Zend_Db_Adapter_Abstract $dbConnect ,UpdateDb $updateObj){
	
		$this->accessId = $accessId;
		$this->type = $type;
		parent::__construct($dbConnect, $updateObj);
	
	}
	
	protected function afterUpdate(){
		$db = $this->_connect ;
		$oldData = $this->getToUpdate();
		
		$daten = array();
		$daten["access_id"] = $this->accessId;
		$daten["key"] = $this->key;
		$daten["type"] = $this->type;
		$daten["data"] = serialize($oldData);
		
		$db->insert("sys_updatereposetory", $daten);

	}
	
	
	/**
	 * Wendet das Update auf das updatobjekt an
	 * @param string $key Der Id oder key für den Type bei Contactes kann es die uid sein zum beispiel
	 * @param string $haschKey Der vorher erfragtet hashKey aus der toUpdate methode
	 * @param mixed $updateData Die neuen zu updatenden Daten
	 * @return boolean
	 */
	public function update($key, $haschKey, $updateData) {
		$this->key = $key;
		return parent::update($haschKey, $updateData);
	}
	
	
}

?>