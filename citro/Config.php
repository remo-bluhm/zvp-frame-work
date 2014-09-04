<?php

class Config {
	
	/**
	 *
	 * @var Zend_Config Enthällt das ConfigObjekt
	 */
	private static $conf = NULL;
	
	public static function setConfigAsXml($fileDir) {
		if (self::$conf === NULL) {
			
			if (file_exists ( $fileDir )) {
				try {
					require_once 'Zend/Config/Xml.php';
					self::$conf = new Zend_Config_Xml ( $fileDir );
				} catch ( Exception $e ) {
					throw new Exception ( "Probleme mit der Inizialisierung der Configuration von Zend_Config_Xml.", E_ERROR, $e );
				
				}
			} else {
				throw new Exception ( "Configuration konnte nicht Inizeialisiert werden da die FileAdresse nicht korrekt ist oder nicht gefunden wurde.", E_ERROR );
			}
		
		}
	
	}
	
	/**
	 * Giebt eine Instance des Configurationsfiles zurück
	 * 
	 * @return Zend_Config|NULL Null falls keine Instance vorhanden ist.
	 */
	public static function getInstance() {
		
		return self::$conf;
	
	}
	
	/**
	 * Giebt einen Hauptknoten zurück
	 * 
	 * @param $MainNameStr string Der Name Des Hauptknoten     	
	 * @return Zend_Config|NULL Null bei nicht vorhandensein
	 */
	public static function getInstanceMainKnot($MainNameStr) {
		return self::$conf->get ( $MainNameStr, NULL );
	}

}

?>