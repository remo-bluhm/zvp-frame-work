<?php

/**
 * Enthält einen einfachen zugriff zu den Services Resourcen
 * 
 * Hiermit ist der zugriff zu den Services die Erlaubt und vorhanden sind abgedeckt
 * 
 * @author Remo Bluhm
 * @version 1.0
 * 
 *
 */
class ServiceResourceInstance {
	
	private static $_CacheIsOn = TRUE;
	private static $_CacheLifeTime = 3600;
	private static $_cacheDir = NULL;
	
	/**
	 * Hiermit kann der Cach ausgeschaltet werden
	 * 
	 * @param $isOn bool       	
	 */
	public static function setCacheOff($isOn = TRUE) {
		if (is_bool ( $isOn )) {
			self::$_CacheIsOn = $isOn;
		}
	}
	
	/**
	 * Setzt die Zeit in Sekunden wann der Cache verfallen soll
	 * Standart mäsig verfällt der Cache nie er auser es wird die Methode
	 * CacheRefresch aufgerufen
	 * 
	 * @param $Sek integer       	
	 */
	public static function setCacheLifeTime($Sek) {
		if ($Sek > 0) {
			self::$_CacheLifeTime = $Sek;
		}
	}
	
	
	
	
	
	/**
	 * Setzt das Verzeichnis zurück in den die Cachdaten gespeichert werden
	 * sollen
	 *
	 * @return string
	 */
	public static function setCachDir($dir) {
		if(is_dir($dir)){
			self::$_cacheDir = $dir;
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * Giebt das Verzeichnis zurück in den die Cachdaten gespeichert werden
	 * sollen
	 * 
	 * @return string
	 */
	public static function getCachDir() {
		//$dir = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "serviceResource_Cach";
		return self::$_cacheDir;
	}
	
	/**
	 * Löscht den Cache
	 */
	public static function CacheRefresch() {
		$cache = self::getCacheObj ();
		$cache->remove ( "resource" );
	}
	
	/**
	 * Giebt das Cacheobjekt zurück
	 * 
	 * @return Ambigous <Zend_Cache_Core, Zend_Cache_Frontend, mixed>
	 */
	private static function getCacheObj() {
		
		$FE_cache ["lifetime"] = self::$_CacheLifeTime;
		$FE_cache ["automatic_serialization"] = TRUE;
		$FE_cache ["caching"] = self::$_CacheIsOn;
		// echo self::getCachDir();
		$BE_cache ["cache_dir"] = self::getCachDir ();
		
		// Ein Zend_Cache_Core Objekt erzeugen
		require_once 'Zend/Cache.php';
		$cache = Zend_Cache::factory ( 'Core', 'File', $FE_cache, $BE_cache );
		
		return $cache;
	
	}
	
	// Fertig Inizialiserte und abgearbeitete ResourcenObjekte
	private static $Instances = array ();
	// ist die übergebene noch nicht abgearbeitete ServiceResourcen objekte nur
	// Inizialisiert
	private static $ServResourceObj = array ();
	
	/**
	 * Giebt die MAIN Resourchen Instance
	 * 
	 * @return ServiceResource
	 */
	public static function getMainInstance() {
		return self::getInstance ( "MAIN" );
	}
	
	public static function getInstance($Name) {

		// prüfen ob sie schon mal in der anfrage geladen wurde
		if (array_key_exists ( $Name, self::$Instances )) {
			$servRes = self::$Instances [$Name];
			return $servRes;
		}
	
		require_once 'Zend/Cache.php';
		$cache = self::getCacheObj ();
		
		// Nachsehen, ob der Cache bereits existiert:
		if (! $Res = $cache->load ( "resource_" . $Name )) {
			
			if (array_key_exists ( $Name, self::$ServResourceObj )) {
				
				$Res = self::$ServResourceObj [$Name];
				
				$Res->Create ();
				
			} else {
				// todo ServiceResourcenobjekt exestiet nicht vieleicht eine ausnahme
			}
			
			$cache->save ( $Res, "resource_" . $Name );
		}
	
// 		echo "<pre>";
// 		print_r($Res);
		self::$Instances [$Name] = $Res;
		return $Res;
	}
	
	public static function setMainInstance($ServiceResource) {
		self::setInstance ( "MAIN", $ServiceResource );
	}
	
	/**
	 * Erstellt eine Objekt vom Type ServiceResorce (Singelton)
	 * Beim Setzen der Config mit den CacheDaten wird hier noch ein cache
	 * abgearbeitet
	 * 
	 * @return ServiceResource Ambiguous
	 */
	public static function setInstance($Name, ServiceResource $ServiceResource) {
		
		self::$ServResourceObj [$Name] = $ServiceResource;
	
	}

}

?>