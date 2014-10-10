<?php
require_once 'citro/update/SelectFactory.php';

class UpdateFactory extends SelectFactory {


	/**
	 * Inizialisiert die Klasse
	 * Und setzt den UpdateObjekt den connect.
	 *
	 * @param Zend_Db_Adapter_Abstract $dbConnect
	 * @param UpdateDb $updateObj
	 */
	function __construct(Zend_Db_Adapter_Abstract $dbConnect ,UpdateDb $updateObj){
	
		parent::__construct($dbConnect, $updateObj);
	
	}


	/**
	 * Kann von einer überschreibenden Klasse genutzt werden um zusätzliche functionalität hinzuzufügen
	 * wird zum beispiel für die ChronologicalSequence Klasse genutzt
	 * 
	 */
	protected function afterUpdate(){
	}
	
	
	/**
	 * Wendet das Update auf das updatobjekt an 
	 * @param string $haschKey Der vorher erfragtet hashKey aus der toUpdate methode
	 * @param mixed $updateData Die neuen zu updatenden Daten
	 * @return boolean
	 */
	public function update($haschKey,$updateData) {
		// Starten der Transaction
		$this->_connect->beginTransaction ();
		try {
			
			$oldData = $this->toUpdate();
			$oldHashKey = $this->getHashKey();
			
			if( $haschKey !== $oldHashKey ){
				$this->_connect->rollBack ();
				throw new Exception("Es wurde ein Falscher HashKey beim Update Übergeben", E_ERROR);
			}
							
			
			$this->_toUpdateObj->update($updateData);
	
			$this->afterUpdate();
			
			
			$this->_connect->commit ();
	
			return TRUE;
			
		} catch ( Exception $e ) {
		
			$this->_connect->rollBack ();
			throw $e;
		
			
		}
	
	}
	

	
}

?>