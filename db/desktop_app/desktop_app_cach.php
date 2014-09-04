<?php
// include_once (PATH_SYS_DB . "zvp_zim_cach.php");
// include_once (PATH_SYS_DB . "zvp_zimmer.php");
// include_once PATH_ZEND . 'Registry.php';
/**
 * class zvp_orte
 *
 * Description for class zvp_orte
 *
 * @author :
 *        
 */
class desktop_app_cach extends DBTable {
	
	protected $_TableName = "desktop_app_cach";
	const SP_ID = "id";
	
	const SP_BT_ZIM_ID = "bt_zimmer_id";
	const SP_GESAMT_CACH = "gesamt_cach";
	const SP_ZIMMER_CACH = "zimmerdata";
	const SP_PREISE_CACH = "preise";
	const SP_BELEGUNG_CACH = "belegung";
	
// 	public function getAll() {
		
// 		$DBCon = Zend_Registry::get ( VAR_REG_DBCON );
// 		$select = $DBCon->select ();
// 		// Hinzuf�gen einer FROM Bedingung
// 		$select->FROM ( array ('z' => 'bt_zimmer' ), array ('zimmerid' => 'z.' . zvp_zimmer::SP_ID, 'obj_nr' => 'z.' . zvp_zimmer::SP_OBJ_NR, 'zim_nr' => 'z.' . zvp_zimmer::SP_ZIM_NR, 'zim_man_sperre' => 'z.' . zvp_zimmer::SP_SPERR_MAN, 'zim_sperre' => 'z.' . zvp_zimmer::SP_SPERR ) );
		
// 		// 03.09.10 werden nicht mehr gebraucht da es erst beim Programm
// 		// ausgewertet wird
// 		// $select->where( "'man_sperre' = ?",0);
// 		// $select->where( "sperre = ?",1);
		
// 		$select->joinLeft ( array ('c' => "bt_zimmer_cach" ), 'z.id = c.bt_zimmer_id', array ('cach_id' => 'c.' . desktop_app_cach::SP_ID, 'cach_ges' => 'c.' . desktop_app_cach::SP_GESAMT_CACH, 'cach_data' => 'c.' . desktop_app_cach::SP_ZIMMER_CACH, 'cach_beleg' => 'c.' . desktop_app_cach::SP_BELEGUNG_CACH, 'cach_preise' => 'c.' . desktop_app_cach::SP_PREISE_CACH )

// 		 );
// 		$select->order ( array ('z.obj_nr' ) );
// 		$back = $select->query ();
		
// 		return $back->fetchAll ();
	
// 	}
	
// 	public function CachExist($ZimmerId) {
// 		$select = $this->_DBCon->select ();
// 		$select->FROM ( $this->getDBTableName (), array (desktop_app_cach::SP_ID, desktop_app_cach::SP_BT_ZIM_ID ) );
		
// 		$select->where ( desktop_app_cach::SP_BT_ZIM_ID . " = ?", $ZimmerId, "INTEGER" );
// 		// echo $select->__toString();
		
// 		$back = $select->query ();
		
// 		$Zeile = $back->fetch ();
		
// 		if ($Zeile != null) {
			
// 			return $Zeile [desktop_app_cach::SP_ID];
// 		}
		
// 		return false;
// 	}
	
// 	public function setUpdate($CachId, $CachGes, $CachZim, $CachBel, $CachPreis) {
// 		$data = array (

// 		desktop_app_cach::SP_GESAMT_CACH => $CachGes, desktop_app_cach::SP_ZIMMER_CACH => $CachZim, desktop_app_cach::SP_PREISE_CACH => $CachPreis, desktop_app_cach::SP_BELEGUNG_CACH => $CachBel )

// 		;
		
// 		try {
// 			$n = $this->_DBCon->update ( $this->getDBTableName (), $data, zvp_zimmer::SP_ID . "=" . $CachId );
// 		} catch ( exception $EXUpdate ) {
// 			throw new exception ( "Der Updatevorgang des zimmers ist Fehlgeschlagen ZimmerId: " . $CachId );
// 		}
		
// 		return $n;
// 	}
	
// 	public function setNew($ZimId, $CachGes, $CachZim, $CachBel, $CachPreis) {
		
// 		$data = array (

// 		desktop_app_cach::SP_BT_ZIM_ID => $ZimId, desktop_app_cach::SP_GESAMT_CACH => $CachGes, desktop_app_cach::SP_ZIMMER_CACH => $CachZim, desktop_app_cach::SP_PREISE_CACH => $CachPreis, desktop_app_cach::SP_BELEGUNG_CACH => $CachBel )

// 		;
		
// 		$id = null;
// 		try {
// 			$this->_DBCon->insert ( $this->getDBTableName (), $data );
// 			$id = $this->_DBCon->lastInsertId ();
// 		} catch ( exception $insertEx ) {
// 			echo $insertEx->getMessage ();
// 			throw new Exception ( "Fehler beim einschreiben eines neuen zeile in der zimmer_cach tabelle" );
// 		}
		
// 		return $id;
	
// 	}
	
// 	public function deleteAsZimmerId($zimId) {
		
// 		// $n anzahl der betroffenen Zeilen
// 		$n = $this->_DBCon->delete ( $this->getDBTableName (), desktop_app_cach::SP_BT_ZIM_ID . ' = ' . $zimId );
	
// 	}

}

?>