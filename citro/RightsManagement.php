<?php


class RightsManagement {


	
	
	
	//private $_userGuid = NULL;
	private $_userCachKey = NULL;
	private $_resourcenInstance = NULL;
	private $_rightsAcl = NULL;
	private $_sysAccess = NULL;
	
	
	/**
	 * Enthällt die configuration für dieses Objekt
	 * @var Zend_Config
	 */
	private static $config = NULL; 
	
	/**
	 * @return RightsAcl
	 */
	public function getRightsAcl(){
		return $this->_rightsAcl;
	}
	
	/**
	 * Constructor des RightsManagement 
	 * Inizialisiert die Resorcen Instance
	 * @param ServiceResource $resourcenInstance
	 * @param Zend_Config $config
	 */
	function __construct(ServiceResource $resourcenInstance, Zend_Config $config){

		$this->_resourcenInstance = $resourcenInstance;		
		self::$config = $config;

	}



	
	/**
	 * Muss angewendet werden wenn sich der Accesses ändert
	 */
	public static function deletetAccessCach(Access $access){
		$tags = array();
		$tags[] = $access->getRoleId();
		$cache = self::createCache ();
		$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,$tags);
	}
		
	/**
	 * Muss angewendet werden wenn sich die Gruppe ändert
	 */
	public static function deletetGroupCach(){
	
	}
	
	/**
	 * Löscht Alle Caches 
	 */
	public static function cleanAllCache(){
		require_once 'Zend/Cache.php';
		$cache = self::createCache ();
		$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
	}
	
	/**
	 * Löscht alle abgelaufenen Caches 
	 */
	public static function cleanModeOldCache(){
		require_once 'Zend/Cache.php';
		$cache = self::createCache ();
		$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
	}
	
	/**
	 * Generiert einen CachKey
	 *
	 * @param $GUID string
	 * @return string
	 */
	private function CreateCacheName($GUID) {
		$Tag = str_replace ( "-", "_", $GUID );
		return $Tag;
	}
	
	
	/**
	 * Läd das Rechtesystem
	 * @param Access|string $access Übergabe eines AccessObjektes oder einer GuId
	 * @throws Exception
	 * @return boolean
	 */
	public function load(Access $access){
	
	
		

	
		// Prüfen auf instance von Access
		if($access instanceof Access){
				
			$this->_sysAccess = $access;
			$this->_userCachKey = $this->CreateCacheName( $access->getGuId() );
				
			//Testen ob das RecheAcl Objekt schon im RAM exestiert
			if($this->_rightsAcl instanceof RightsAcl)
				return $this->_rightsAcl;
				
	
			$cache = self::createCache ();

	
			// Die Classen includen die gecacht werden sollen
			require_once 'citro/rights/RightsAcl.php';
			require_once 'citro/rights/roles/group/GroupParent.php';
 			//require_once 'citro/rights/roles/access/Access.php';
			require_once 'citro/rights/roles/access/AccessWithGuId.php';
			require_once 'citro/rights/roles/access/AccessWithLoginData.php';
// 			require_once 'db/sys/access/groups/sys_access_groups.php';
			// Nachsehen, ob der Cache bereits existiert:
			if (! $acl = $cache->load ($this->_userCachKey )) {
	
				
				$groupTab = new sys_access_groups ();
				// Hollt auf grund der AccessGruppenId die Hirachie bis zur obersten ebene von Gruppen
				$groupHira = $groupTab->getParent($access->getGroupId());
	
			// umwandeln von einer Flachen GroupHierachie(Array) in eine Objektorientieren GroupParent
				$groupParent = NULL;
				/* @var $groupHira Zend_Db_Table_Rowset */
				/* @var $group Zend_Db_Table_Row */
				foreach($groupHira as $group){
					if($groupParent == NULL){
						$groupParent = new GroupParent($group);
							
					}else{
						$groupChild = new GroupParent($group);
						$groupChild->setParent($groupParent);
						$groupParent = $groupChild;
					}
				}
	
				require_once 'citro/rights/RightsAcl.php';
				// Inizialisieren eines Rechtesystems
				$acl = new RightsAcl ( $this->_resourcenInstance , $access, $groupParent);
				
				// setzen der Tags
				$tags = array();
				$groupRolIds = $groupParent->getGroupParentRoleIds();
				if(is_array($groupRolIds))$tags = $groupRolIds;
				$tags[] = $access->getRoleId();
	
				// Sichern des Caches
				$cache->save ( $acl, $this->_userCachKey ,$tags);
				
				
			
			}
	
		
			$this->_rightsAcl = $acl;
			return $acl;
		}
		return NULL;
	}
	
	
	
	/**
	 * 
	 */
	private static function createCache() {
		// erstellen des Frontendcaches
		$FECache = self::$config->get("frontendOption")->toArray();
		if($FECache["caching"] == "true" || $FECache["caching"] == "TRUE"){
			$FECache["caching"] = TRUE;
		}
		else{$FECache["caching"] = FALSE;
		}
		$FECache["automatic_serialization"] = TRUE;

		// Ein Zend_Cache_Core Objekt erzeugen
		require_once 'Zend/Cache.php';
		$cache = Zend_Cache::factory ( 'Core', 'File', $FECache, self::$config->get("backendOption")->toArray() );
		return $cache;
	}

	
	
	
	
	
}

?>