<?php

/**
 * citro Framwork
 * @author Remo Bluhm
 * @date 25.09.2012
 * 
 * Ist für die verbindung zur Datenbank
 *
 */
class DBConnect {
	
	/**
	 * Enthällt die Configuration des Datenbankconnects
	 * 
	 * @var Zend_Config NULL
	 */
	private static $config = NULL;
	
	/**
	 * Enthällt das Datenbank connect
	 * 
	 * @var Zend_Db_Adapter_Abstract NULL
	 */
	private static $connect = NULL;
	
	/**
	 * Ist für die configuration der Mainknoten für die connect configuration
	 * 
	 * @var string
	 */
	const CONF_MAIN_KNOT = "database";
	const CONF_DB_PRAFIX = "prafix";
	
	public static function Connect(Zend_Config $dbConfig) {

		if ($dbConfig === NULL) {
			throw new Exception ( "Dem Datenbank Connect wurde keine oder eine falsche Configuration Übergeben!", E_ERROR );
		}
		self::$config = $dbConfig;
		
		try {
			
			require_once 'Zend/Db.php';
			require_once 'Zend/Db/Adapter/Pdo/Mysql.php';
		
			$db = Zend_Db::factory (self::$config );
			self::setAdappter ( $db );
			$db->getConnection ();
			
			// setzt das Profiling für den Fierfox
			if($dbConfig->get("profiling_firefox", NULL) === "true"){
				self::setProfilingFirebug($db);
			}
			
		
			
			self::$connect = $db;
		
		} catch ( Exception $e ) {
			// @todo Die Exception noch loggen
			throw new Exception ( "Fehler beim Erstellen des Datenbank connect mit Zend_DB!", E_ERROR );
		}
	
	}
	
	
	private static function setProfilingFirebug($dbConn){
		require_once 'Zend/Db/Profiler/Firebug.php';
		$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
		$profiler->setEnabled(true);
		//$profiler->setFilterQueryType(32);
		$dbConn->setProfiler($profiler);
	}
	
	private static function setAdappter(Zend_Db_Adapter_Abstract $conn) {
		
		require_once 'Zend/Db/Table/Abstract.php';
		Zend_Db_Table_Abstract::setDefaultAdapter ( $conn );
		
		$Prafix = self::$config->get ( self::CONF_DB_PRAFIX, FALSE );
		
		if ($Prafix) {
			
			require_once 'citro/DBTable.php';
			DBTable::setDBPrafix ( $Prafix );
		}
		// require_once 'citro/ErrorHandling.php';
		// ErrorHandling::setIsOneDBWrite(TRUE);
		
		// Zend_Registry::set(VAR_REG_DBCON, $db);
	
	}
	
	/**
	 * Gieb den Standart DatebankConnect zurück
	 * 
	 * @return Zend_Db_Adapter_Abstract NULL
	 */
	public static function getConnect() {
		return self::$connect;
	}

}

?>