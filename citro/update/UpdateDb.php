<?php


abstract class UpdateDb{
	

	
	/**
	 * Da die update metode In einer Transaction ausgeführt wird,
	 * sollte bei datenbakabfragen immer der connect genommen werden.
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_connect = NULL;
	
	/**
	 * Setzt den connect 
	 * wird von der Factory Classe gesetzt
	 * 
	 * @param Zend_Db_Adapter_Abstract $dbConnect
	 */
	public function setConnect(Zend_Db_Adapter_Abstract $dbConnect){	
		$this->_connect = $dbConnect;
	}
	


	/**
	 * Ist die Function die die Daten hollt die verändert werden sollen
	 * 
	 * @return mixed Die Daten
	 */
	abstract function toUpdate();
	
	/**
	 * Ist die Function die das Update ausführt
	 * 
	 * @param mixed $updateData Die neuen zu updatenten Daten
	 */
	abstract function update($updateData);


}

?>