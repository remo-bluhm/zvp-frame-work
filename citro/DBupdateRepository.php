<?php

class DBupdateRepository {
	
	const UPDATE_HASH_KEY = "UpdateRepositoryHashKey";
	
	/**
	 *
	 * @var User
	 */
	private $_userId = NULL;
	
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	private $_conn = NULL;
	
	private $_updateObj = NULL;

	
	//private $_primaryId = NULL;
	
	//private $_primaryColumn = "id";
	

	
	//private $_tableName = NULL;
	
	
	
	/**
	 *
	 * @var Zend_Db_Table_Row_Abstract NULL
	 */
	private $_row = NULL;
	
	private $_tube = NULL;
	
	/**
	 * Inizialistiert das Update Objekt
	 * @param string $userId Die Id des verändernden Users
	 * @param Zend_Db_Adapter_Abstract $dbConnect 
	 */
	function __construct($userId, Zend_Db_Adapter_Abstract $dbConnect , I_DBUpdate $updateObj) {
			
		$this->_userId = $userId;
		$this->_conn = $dbConnect;
		$this->_updateObj = $updateObj;
		
	
	}
	
// 	private $_tubesArray = array();
	
	
// 	public function add(DbIUpdate $updateTube){
// 		$this->_tubesArray[] = $updateTube;
// 		return $this;
// 	}
// 	public function clear(){
// 		$this->_tubesArray = array();
// 		return $this;
// 	}
	
	public function Recovery($recoveryId, $overwriteData = NULL) {
		
		require_once 'citro/db/sys/sys_updatereposetory.php';
		$repTab = new sys_updatereposetory ();
		
		$select = new Zend_Db_Select ( $this->_conn );
		$select->from ( $repTab->getTableName () );
		$select->where ( "`" . sys_updatereposetory::SP_ID . "` = ?", $recoveryId );
		
		$stmt = $this->_conn->query ( $select->__toString () );
		
		$Daten = $stmt->fetch ();
		// 1. Daten aus der Recovery Tabelle
		$unserializeData = unserialize ( $Daten ["data"] );
		
		// 2. Actuelle Daten aus der upzudatenden Tabelle
		$aktuellData = $this->_row->toArray ();
		
		// Daten für das einschreiben in die Repository
		$data [sys_updatereposetory::SP_DATA_CREATE] = DBTable::DateTime ();
		$data [sys_updatereposetory::SP_USER_CREATE] = $this->_userId;
		$data [sys_updatereposetory::SP_KEY] = $this->_primaryId;
		$data [sys_updatereposetory::SP_TYPE] = $this->_tableName;
		$data [sys_updatereposetory::SP_DATA] = serialize ( $aktuellData );
		
		// 3 InsertQuery
		$repTab = new sys_updatereposetory ();
		$this->_conn->insert ( $repTab->getTableName (), $data );
		
		// Bereinigte Daten für das Update der Zeile
		// if (count ( $this->_updateColumn ) > 0) {
		// $cleanUpdateDate = array_intersect_key ( $updateDate, array_flip (
		// $this->_updateColumn ) );
		// } else {
		// $cleanUpdateDate = array_intersect_key ( $updateDate, $tubel );
		// }
		
		// 4 Updaten der Tabelle
		$this->_row->setFromArray ( $unserializeData );
		$this->_row->save ();
		
		return TRUE;
	
	}
	
	/**
	 * Liefert alle Updates der angefragten Tabelle zurück
	 * 
	 * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
	 */
	public function chronologicalSequence() {
		
		require_once 'citro/db/sys/sys_updatereposetory.php';
		$repTab = new sys_updatereposetory ();
		
		require_once 'Zend/Db/Select.php';
		$select = new Zend_Db_Select ( $this->_conn );
		
		$select->from ( $repTab->getTableName () );
		$select->where ( "`" . sys_updatereposetory::SP_KEY . "` = ?", $this->_primaryId );
		$select->where ( "`" . sys_updatereposetory::SP_TYPE . "` = ?", $this->_tableName );
		$select->order ( sys_updatereposetory::SP_DATA_CREATE . " DESC" );
		
		$stmt = $this->_conn->query ( $select->__toString () );
		
		$AllRep = $stmt->fetchAll ();
		
		return $AllRep;
	}
	
	/**
	 * Fürt das Update durch Schreibt dabei die alte zeile in die Repository
	 * 
	 * @param $updateDate array Die zu verändernden Daten
	 * @param $updateColunm array Angabe von den Spalten die verändert werden können
	 * @param $updateHashKey string Wenn übergeben wird dieser String als zu vergleichender Wert genutzt ansonsten prüft er ob der HashKey in den updateDaten mit enthalten ist
	 * @return boolean
	 */
	public function setUpdate($updateDate, $updateColunm = array(), $updateHashKey = NULL) {
		
		// Prüfen des HashKeys
		if ($updateHashKey === NULL) {
			if (array_key_exists ( self::UPDATE_HASH_KEY, $updateDate )) {
				$updateHashKey = $updateDate [self::UPDATE_HASH_KEY];
			} else {
				return FALSE;
			}
		
		}
		
		// einfügen der updatereposetory Tabelle
		require_once 'citro/db/sys/sys_updatereposetory.php';
		
		// Starten der Transaction
		$this->_conn->beginTransaction ();
		
		try {
			
			// 1 Query
			$tubel = $this->generateTubeData ();
			
			if ($tubel !== FALSE && $updateHashKey == $this->generateeHashKey ( $tubel )) {
				
				$data [sys_updatereposetory::SP_DATA_CREATE] = DBTable::DateTime ();
				$data [sys_updatereposetory::SP_USER_CREATE] = $this->_userId;
				$data [sys_updatereposetory::SP_KEY] = $this->_primaryId;
				$data [sys_updatereposetory::SP_TYPE] = $this->_tableName;
				$data [sys_updatereposetory::SP_DATA] = serialize ( $tubel );
				
				$repTab = new sys_updatereposetory ();
				
				// 2 InsertQuery
				$this->_conn->insert ( $repTab->getTableName (), $data );
				
				// Bereinigte Daten für das Update der Zeile
				if (is_array ( $updateColunm ) && count ( $updateColunm ) > 0) {
					$cleanUpdateDate = array_intersect_key ( $updateDate, array_flip ( $updateColunm ) );
				} else {
					$cleanUpdateDate = array_intersect_key ( $updateDate, $tubel );
				}
				
				// 3 UpdateQuery
				$this->_conn->update ( $this->_tableName, $cleanUpdateDate, array ($this->_primaryColumn . " = ?" => $this->_primaryId ) );
				
				// Alles Ok absetzen der Transaction
				$this->_conn->commit ();
				return TRUE;
			
			} else {
				// Fehler HashKey stimmnmte nicht überein
				$this->_conn->rollBack ();
				return FALSE;
			}
		
		} catch ( Exception $e ) {
			//die($e->getMessage());
			// Fehler bei einer query
			$this->_conn->rollBack ();
			return FALSE;
		}
	
	}
	
	
	
	public function getToUpdate(I_DBUpdate $dbUpdate) {
		$toUpdateData = $dbUpdate->getToUpdate();
		return $toUpdateData;
	
	
	}
	
	/**
	 * giebt die zu updatente Spalte mit dem HashKey zurück
	 * zurückgegeben wird ein Array mit Spaltenname(key)=>Wert(value)
	 *
	 * @param $columnArray array Eine Liste aller Spalten die zurückgegeben werden sollen falls	null dan werden alle zurückgegeben
	 * @param $withHashKey bool Wenn True dan ist in dem Zurüggegebenen array schon der	HashKey mit enthalten
	 * @return string
	 */
	public function getColumnToUpdate($columnArray = NULL, $withHashKey = TRUE) {
		
		$tubel = $this->generateTubeData ();
		
		if ($tubel !== FALSE) {
			
			if ($columnArray !== NULL && is_array ( $columnArray )) {
				$result = array_intersect_key ( $tubel, array_flip ( $columnArray ) );
			
			} else {
				$result = $tubel;
			}
			
			if ($withHashKey === TRUE) {
				$hashKeyValue = $this->generateeHashKey ( $tubel );
				$result [self::UPDATE_HASH_KEY] = $hashKeyValue;
			}
			return $result;
		}
		return False;
	}
	
	/**
	 * Erzeugt einen Hashkey der Zeile
	 * 
	 * @param bool Wenn True dann wird der Wert als Array zurückgegeben
	 * @return array string FALSE nicht generieren konnte
	 */
	public function getUpdateHashKey(DbIUpdate $dbUpdate, $asArray = TRUE) {
		
		$tubel = $this->generateTubeData ();
		
		if ($tubel !== FALSE) {
			
			$hashKeyValue = $this->generateeHashKey ( $tubel );
			
			if ($asArray === TRUE) {
				return array (self::UPDATE_HASH_KEY => $hashKeyValue );
			}
			
			return $hashKeyValue;
		}
		return FALSE;
	}
	

	


}

?>