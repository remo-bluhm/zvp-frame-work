<?php

class SelectFactory {
	
	
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_connect = NULL;
	
	/**
	 * Die Daten nach aufruf der toUpdate Methode des UpdateObjektes
	 * @var mixed|null
	 */
	protected $_toUpdateBackData = NULL;

	/**
	 * @var I_Update
	 */
	protected $_toUpdateObj = NULL;
	
	
	

	

	
	/**
	 * Inizialisiert die Klasse
	 * Und setzt den UpdateObjekt den connect.
	 * 
	 * @param Zend_Db_Adapter_Abstract $dbConnect
	 * @param UpdateDb $updateObj
	 */
	function __construct(Zend_Db_Adapter_Abstract $dbConnect ,UpdateDb $updateObj){
	
		$this->_connect = $dbConnect;
		$this->_toUpdateObj = $updateObj;
		$this->_toUpdateObj->setConnect($dbConnect);

	}
	
	
	/**
	 * giebt die Daten zurück die aus den übergebenen UpdateObjekt von der Methode toUpdate zurückgegeben wurde zurück
	 * @param UpdateDb $updateObj
	 * @return UpdateDb
	 */
	public  function toUpdate() {

 			$this->_toUpdateObj->setConnect($this->_connect);
			
 
			$updateData = $this->_toUpdateObj->toUpdate();
			$this->_toUpdateBackData = $updateData;
			

			return $updateData;		
	}
	
	
	/**
	 * giebt die Daten zurück die nach aufruf der Methode toUpdate generierten daten zurückgegeben wurden
	 * @return mixed|null die Daten
	 */
	public function getToUpdate(){
		return $this->_toUpdateBackData;
	}
	
	
	
	/**
	 * giebt den erstellten Hashkey zurück
	 * 
	 * @throws Exception wenn vorher nicht die toUpdate Methode aufgerufen wurde
	 * @return string der HashKey
	 */
	public function getHashKey(){
		
		if($this->_toUpdateBackData === NULL)
			throw new Exception("Es muss zuerst die toUpdate methode aufgerufen werden!", E_ERROR);
		
		$hashKey = $this->generateHashKey($this->_toUpdateBackData);
		return $hashKey;
	}
	
	

	/**
	 * Erstellt einen HashKey 
	 * @param unknown_type $data
	 * @return string Der HashKey
	 */
	protected function generateHashKey($data){
		$hashKey = sha1( serialize($data));
		return $hashKey;
	}

}

?>