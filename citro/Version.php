<?php

class Version {
	
	/**
	 *
	 * @var VersionAbstract
	 */
	private $_vObj = NULL;
	
	private $_userId = NULL;
	
	const UPDATE_HASH_KEY = "UpdateHashKeyForCitro";
	
	function __construct(VersionAbstract $VersionsObjekt, $UserId) {
		$this->_vObj = $VersionsObjekt;
		$this->_userId = $UserId;
	}
	
	/**
	 * Hollt die Daten für die aufbereitung des Formulars
	 * 
	 * @param $queryData unknown_type
	 *       	 Die Anfrage Daten zb. id zur genauen identivizierung
	 * @return Ambigous <unknown, multitype:> Die Daten für die aufbereitung des
	 *         Formulars
	 */
	public function getUpdateData($queryData) {
		
		// $VersionKey = $this->_vObj->getVersionKey();
		
		$BlankData = $this->_vObj->getVersionDataToUpdate ( $queryData );
		
		$HashKey = $this->getUpdateHashKey ( $BlankData );
		
		$BlankData [self::UPDATE_HASH_KEY] = $HashKey;
		
		$BackData = $this->_vObj->getDateForUpdate ( $BlankData );
		return $BackData;
	}
	
	/**
	 * Setzt ein Update auf die Daten
	 *
	 * @param $queryData unknown_type
	 *       	 Die Anfrage Daten zb. id zur genauen identivizierung der Zeile
	 * @param $updateHashKey string
	 *       	 UPDATE_HASH_KEY
	 * @param $newData unknown_type       	
	 */
	public function setUpdate($queryData, $updateHashKey, $newData) {
		
		$BlankData = $this->_vObj->getVersionDataToUpdate ( $queryData );
		
		$HashKey = $this->getUpdateHashKey ( $BlankData );
		
		if ($HashKey == $updateHashKey) {
			
			// kann Update gemacht werden da die Daten nicht verändert wurden
			
			// unset($Data[self::UPDATE_HASH_KEY]);
			
			// Hier die Alten Daten in die sys_version Tabelle einschreiben
			$this->writeTable ( $queryData, $BlankData );
			
			$this->_vObj->setUpdate ( $newData );
		
		}
		{
			// Daten wurden verändert
		
		}
	
	}
	
	/**
	 *
	 * @param $recoveryId integer
	 *       	 Die Id in der Tabelle
	 */
	public function Recovery($recoveryId) {
		// Hollt aus der Datenbank die spalte mit den daten
		// Und übergiebt die Daten an die RecoveryMethode der Abstacten klasse
		$Daten = array ();
		
		// Wiederherstellen der gesicherten Daten
		$queryData = unserialize ( $Daten ["querydata"] );
		$unserializeData = unserialize ( $Daten ["data"] );
		
		$BlankData = $this->_vObj->getVersionDataToUpdate ( $queryData );
		$SeriData = serialize ( $BlankData );
		
		$this->writeTable ( $SeriData );
		
		// der RecoveryMethode übergeben
		$this->_vObj->setRecoveryDate ( $unserializeData );
	
	}
	
	private function getUpdateHashKey($queryData) {
		$queryDataSeri = serialize ( $queryData );
		$queryDataSha1 = sha1 ( $queryDataSeri );
		return $queryDataSha1;
	}
	
	private function getQueryDataHashKeyForDB($queryData) {
		
		$queryDataHashKey = sha1 ( serialize ( $queryData ) );
		return $queryDataHashKey;
	}
	
	public function chronologicalSequence($queryData) {
		
		$VersionsKey = $this->_vObj->getVersionType ();
		$queryDataHashKey = $this->getQueryDataHashKeyForDB ( $queryData );
	
	}

}

?>