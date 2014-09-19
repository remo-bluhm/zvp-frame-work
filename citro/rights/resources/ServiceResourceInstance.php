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
	
	const CONF_SERVICERESOURCE = "serviceResource";
	
	private $_cacheName = "Main";
	private $_CacheIsOn = TRUE;
	private $_CacheLifeTime = 3600;
	private $_cacheDir = NULL;
	
	private $_cachePreFileName = "resource_";
	
	private $_confServiceResource = NULL;
	private $conf = NULL;
	
	function __construct( Zend_Config $servResConf = NULL){
		
		$this->conf = $servResConf;
		
		if(is_a($servResConf, "Zend_Config")) $this->workConfig ( $servResConf );
		

	}
	
	
	/**
	 * @param cachConf
	 */
	private function workConfig(Zend_Config $conf) {
		
		
		if($conf->__isset("cache") && is_a($conf->cache, "Zend_Config")) {
			
			$cachConf = $conf->cache;
						
			if($cachConf->__isset("name")) $this->setName($cachConf->name);
			
			if($cachConf->__isset("frontendOption") && is_a($cachConf->frontendOption, "Zend_Config")){
				
				if($cachConf->frontendOption->__isset("caching")) $this->setCacheOff($cachConf->frontendOption->caching);
				if($cachConf->frontendOption->__isset("lifetime")) $this->setCacheLifeTime($cachConf->frontendOption->lifetime);
				
			}
			
			if($cachConf->__isset("backendOption") && is_a($cachConf->backendOption, "Zend_Config")){
				if($cachConf->backendOption->__isset("cache_dir")) $this->setCachDir($cachConf->backendOption->cache_dir);					
			}

		}
		
		if($conf->__isset("serviceResource") && is_a($conf->serviceResource, "Zend_Config")) {
				
			$this->_confServiceResource = $conf->serviceResource;
		}
	}

	
	
	
	
	/**
	 * Hiermit kann der Cach ausgeschaltet werden
	 * 
	 * @param $isOn bool|string  by String True or False       	
	 */
	public function setCacheOff($isOn = TRUE) {
		
		if(is_string($isOn)){
			switch ( strtoupper( $isOn )){
				case "TRUE":$isOn = TRUE;break;
				case "FALSE":$isOn = FALSE;break;
			}
		}
			
			
		if (is_bool ( $isOn ))	$this->_CacheIsOn = $isOn;
		
		
	}
	
	
	
	
	/**
	 * Setzt die Zeit in Sekunden wann der Cache verfallen soll
	 * Standart mäsig verfällt der Cache nie er auser es wird die Methode
	 * CacheRefresch aufgerufen
	 * 
	 * @param $Sek integer       	
	 */
	public function setCacheLifeTime($Sek) {
		if(is_numeric($Sek)){
			if ($Sek > 0) $this->_CacheLifeTime = $Sek;
		}
	}
	
	
	
	
	/**
	 * Setzt den Namen für die Cache Datei
	 * @param string $name
	 */
	public function setName($name){
		$this->_cacheName = $name;
	}
	
	
	
	
	/**
	 * Kann separat gennommen werden um der ServiceResourche einene config mitzugeben
	 * standartmäsig kann diese aber auch bei der inizialisierungs config mit eingeschlossen werden
	 * @param Zend_Config $conf
	 */
	public function setConfigServiceResource(Zend_Config $conf){
		$this->_confServiceResource = $conf;
	}
	
	
	
	
	/**
	 * Setzt das Verzeichnis zurück in den die Cachdaten gespeichert werden
	 * sollen
	 *
	 * @return string
	 */
	public function setCachDir($dir) {
		if(is_dir($dir)){$this->_cacheDir = $dir;return TRUE;
		}else{return FALSE;	}
	}
	
	
	
	
	
	/**
	 * Giebt das Verzeichnis zurück in den die Cachdaten gespeichert werden
	 * sollen
	 * 
	 * @return string
	 */
	public function getCachDir() {
		//$dir = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "serviceResource_Cach";
		return $this->_cacheDir;
	}
	
	
	
	
	/**
	 * Löscht den Cache
	 */
	public function CacheRefresch() {
		$cache = $this->getCacheObj();
		$cache->remove ( "resource" );
	}
	

	
	// Fertig Inizialiserte und abgearbeitete ResourcenObjekte
	private  $serviceResourceInst = NULL;
	// ist die übergebene noch nicht abgearbeitete ServiceResourcen objekte nur
	// Inizialisiert
	//private static $ServResourceObj = array ();
	

	public function existInstance(){
		if(is_a($this->serviceResourceInst, "ServiceResource")) return TRUE;
		return FALSE;
	}
	
	public function getInstance() {

		if($this->existInstance()){
			return $this->serviceResourceInst;
		}
	
		$servRes = $this->getInstanceFromCache();
		$this->serviceResourceInst = $servRes;
		return $servRes;
		

	}
	


	private function getInstanceFromCache(){
		
		$cache = $this->getCacheObj();
		
		require_once 'citro/rights/resources/ServiceResource.php';
		
		// Nachsehen, ob der Cache bereits existiert:
		if (! $Res = $cache->load ( $this->_cachePreFileName . $this->_cacheName )) {

			$Res = $this->getInstanceCreate();
		
			$cache->save ( $Res, $this->_cachePreFileName . $this->_cacheName );
		}
		
		return $Res;
	}
	
	
	
	
	
	
	private function getInstanceCreate(){
		
		$servRes = new ServiceResource($this->_confServiceResource);
		$servRes->Create ();
		return $servRes;
	}
	
	
	/**
	 * Giebt das Cacheobjekt zurück
	 *
	 * @return Ambigous <Zend_Cache_Core, Zend_Cache_Frontend, mixed>
	 */
	private function getCacheObj() {
	
		$FE_cache ["lifetime"] = $this->_CacheLifeTime;
		$FE_cache ["automatic_serialization"] = TRUE;
		$FE_cache ["caching"] = $this->_CacheIsOn;
	
		$BE_cache ["cache_dir"] = self::getCachDir ();
	
		require_once 'Zend/Cache.php';
		$cache = Zend_Cache::factory ( 'Core', 'File', $FE_cache, $BE_cache );
	
		return $cache;
	
	}
	
}

?>