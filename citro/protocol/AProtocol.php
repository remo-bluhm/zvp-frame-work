<?php

/**
 * class IProtocol
 *
 * Description for class IProtocol
 *
 * @author:
*/
abstract class AProtocol {
	
	
	
	protected $RefDataArray = NULL;
	
	protected $ProtTree = NULL;
	
	protected $Config = NULL;
	
	protected $ProtokollName = NULL;
	
	protected $Data = NULL;
	protected $DataArt = NULL;
	
	/**
	 * IProtocol constructor
	 *
	 * @param       	
	 *
	 *
	 */
	function AProtocol() {
	
	}
	
	public function getDataArt() {
		return $this->DataArt;
	}
	
	abstract protected function init($Config, $ProtokollString);
	
	
	/**
	 * Setzen der anfrage Daten
	 * @param string $Data
	 * @return unknown
	 */
	public function setRequest($Data) {
		
		$Data = $this->beforeRequest ( $Data );
		
		if ($this->ProtTree != NULL && is_subclass_of ( $this->ProtTree, "AProtocol" )) {	
			$Data = $this->ProtTree->setRequest ( $Data );
		}
		
		$Data = $this->backRequest ( $Data );
		
		return $Data;
	}
	
	
	protected function beforeRequest($Data) {
		return $Data;
	}

	
	protected function backRequest($Data) {
		return $Data;
	}
	
	private static $_access = NULL;
	
	public static function getAccess($guid = NULL){
		
		require_once 'citro/rights/roles/access/AccessWithGuId.php';
		if(self::$_access instanceof AccessWithGuId)
			return self::$_access;
	
		
		require_once 'citro/GuidCreate.php';
		if(GuidCreate::isProbablyGUID($guid)){
			$access = new AccessWithGuId($guid);
			self::$_access = $access;
			return $access;
		}
		return NULL;
		
	}
	

	
	public function setResponce($Data) {
		$Data = $this->beforeResponce ( $Data );
		if ($this->ProtTree != NULL && is_subclass_of ( $this->ProtTree, "AProtocol" )) {
			
			$Data = $this->ProtTree->setResponce ( $Data );
		}
		$Data = $this->backResponce ( $Data );
		return $Data;
	}
	
	
	protected function beforeResponce($Data) {
		return $Data;
	}
	
	
	protected function backResponce($Data) {
		return $Data;
	}
	
	
	public function getData() {
		return $this->Data;
	}
	
	public function setProtocollTree($ProtocollTree) {
		if ($ProtocollTree != NULL && is_subclass_of ( $ProtocollTree, "AProtocol" )) {
			$this->ProtTree = $ProtocollTree;
			return TRUE;
		}
		return FALSE;
	}
	
	public function getAllProtocolName(){
		$proNameA = array();
		$proNameA[] = $this->getProtocolName ();
		if ($this->ProtTree != NULL && is_subclass_of ( $this->ProtTree, "AProtocol" )) {
				
			$proTreeNameA = $this->ProtTree->getAllProtocolName ();
			$proNameA = array_merge($proNameA,$proTreeNameA);
		}

		return $proNameA;

	}
	
	public function getProtocolName() {
		
		if ($this->ProtokollName === NULL) {
			
			return get_class ( $this );
		} else {
			return $this->ProtokollName;
		}
	}

}

?>