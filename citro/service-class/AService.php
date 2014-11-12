<?php

//require_once 'citro/Service.php';

/**
 * class a_wsclass
 *
 * Description for class a_wsclass
 *
 * @author :
 *        
 */
// abstract class AService extends Service{
abstract class AService {
	
	
	protected $_securityLevel = 0;
	
	/**
	 * Der Main User also der Anfragende User
	 * 
	 * @var User
	 * @deprecated da sollte nur noch _user benutzt werden
	 */
	protected $_MainUser = NULL;
	
	/**
	 * Der Main User also der Anfragende User
	 * @var User
	 */
	protected $_user = NULL;
	
	/**
	 * Die Hauptgruppe des Anfragenden Users mit seinen überlegenen gruppen
	 * 
	 * @var Group
	 */
	protected $_MainGroup = NULL;
	
	/**
	 * Das Rechtemanegement des Anfragenden Users
	 * 
	 * @var RightsAcl
	 */
	protected $_rightsAcl = NULL;
	
	/**
	 * Die Configuration des Erstellten Services
	 * 
	 * @var Zend_Config;
	 */
	protected $_Config = NULL;
	
	private $alias = NULL;
	private $name = NULL;
	private $params = array ();
	private $actions = array ();
	
	function __construct() {
		
		require_once 'citro/rights/resources/ServiceResource.php';
		$ServiceName = substr ( get_class ( $this ), strlen ( ServiceResource::SERVICEPREE ) );
		
		$this->name = $ServiceName;
	
	}
	
	/**
	 * @var ServiceFabric
	 */
	protected $_serviceFabric = NULL;
	
	/**
	 * Setzen der ServiceFabric beim Inizialisierun um Unterabfragen zu gewährleisten
	 * @param ServiceFabric $serviceFab
	 */
	public function _setServiceFabric(ServiceFabric $serviceFab){
		$this->_serviceFabric = $serviceFab;
	}
	public function getServiceFabric(){
		return $this->_serviceFabric;
	}
	

	
	/**
	 * @var ServiceResource
	 */
	protected $_resource = NULL;
	public function _setResource(ServiceResource $resource){
		$this->_resource = $resource;
	}

	/**
	 * @return ServiceResource
	 */
	public function getResource(){
		return $this->_resource;
	}
	/**
	 * Giebt den Access des anfragenden zurück
	 * @return Access|NULL
	 */
	public function getAccess(){
		if(is_a($this->_rightsAcl,"RightsAcl") && is_a( $this->_rightsAcl->getAccess(),"Access")){
			return $this->_rightsAcl->getAccess();
		}
		return NULL;
	}
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Kann überschrieben werde um Standartaufgaben zu erledigen
	 */
	public function main() {
	
	}
	
	/**
	 * Setzt ein Parameterobjekt oder überschreibt seinen Wert
	 * 
	 * @param $Name string       	
	 * @param $Value unknown_type       	
	 * @param $Type string       	
	 */
	public function setParam($name, $value) {
		
		$this->$name = $value;
// 		if (array_key_exists ( $name, $this->params )) {
// 			$this->params [$name] = $value;
// 		} else {
// 			$param = new Param ( $name, $value );
// 			$this->params [$name] = $param;
// 		}
	
	}
	
	/**
	 * giebt ein Paramobjekt zurück
	 * 
	 * @return Param NULL:
	 */
	public function getParam($name) {
// 		if (array_key_exists ( $name, $this->params )) {
// 			return $this->params [$name];
// 		}
// 		return NULL;

		if(isset($this->$name)){
			return $this->$name;
		}
		else {
			return NULL;
		}
		
	}
	/**
	 * giebt den Wert eines Parameters zurück
	 * 
	 * @return Param NULL:
	 */
	protected function getParamValue($name) {
		if (array_key_exists ( $name, $this->params )) {
			/*
			 * @var $param Param
			 */
			$param = $this->params [$name];
			return $param->getValue ();
		}
		return NULL;
	}
	
	/**
	 * ein Array von ParamObjekten
	 * 
	 * @return Params
	 */
	public function setParams(array $paramObj) {
		
		/*
		 * @var $paramObjekt Param
		 */
		foreach ( $paramObj as $paramObjekt ) {
			if (is_a ( $paramObjekt, "Param" )) {
				$this->params [$paramObjekt->getName ()] = $paramObjekt;
			}
		}
	
	}
	
	/**
	 * ein Array von ParamObjekten
	 * 
	 * @return Params
	 */
	public function getParams() {
		return $this->params;
	}
	
	/**
	 * ein Array von ParamObjekten
	 * 
	 * @return Params
	 */
	public function getParamsAsArray() {
		$params = array ();
		foreach ( $this->params as $param ) {
			$params [$param->getName ()] = $param->getValue ();
		}
		return $params;
	}
	

	
	
	
	

	/**
	 * Zum Setzen des Rechtemanagement des Users
	 * 
	 * @param $RM RightsAcl       	
	 */
	public function _setRightsAcl(RightsAcl $RM = NULL) {
		$this->_rightsAcl = $RM;
		if(is_a($RM,"RightsAcl")){
			$this->_MainUser = $RM->getAccess();
			$this->_user = $RM->getAccess();
			$this->_MainGroup = $RM->getGroup();
		}
	}
	

	
	
	

	
	
	/**
	 * Setzt die Configuration für diesen Service aus der Configdatei
	 *
	 * @param $config array
	 *       	 die Daten aus der Configdatei
	 *       	
	 */
	public function setConfig(Zend_Config $config = NULL) {
		$this->_Config = $config;
	}
	

	
	
	
	
	
	
	
	
	
	public function getFetchArray($AsUtf8Encoding = FALSE) {
		
		// encodiert in den utf8code
		if (is_array ( $this->FetchArray )) {
			
			if ($AsUtf8Encoding == true) {
				return $this->utf8Encode ( $this->FetchArray );
			} else {
				
				return $this->FetchArray;
			}
		
		} else {
			return array ();
		}
	}
	public function utf8Encode($FetchArray) {
		
		if (is_array ( $FetchArray ) && count ( $FetchArray ) > 0) {
			$SA = array ();
			foreach ( $FetchArray as $elem ) {
				
				$SE = array ();
				/*
				 * @var $USER String ist nur der Value
				 */
				foreach ( $elem as $Key => $Val ) {
					// todo: muss getestet werden ob der �bergebene wert auch
					// ein string ist ansonsten nicht �bergeben
					if (is_string ( $Val )) {
						$SE [$Key] = utf8_encode ( $Val );
					} else {
						$SE [$Key] = utf8_encode ( ( string ) $Val );
					}
				
				}
				$SA [] = $SE;
			
			}
		} else {
			// todo: Fehlerloggen
			$SA = array ();
		}
		return $SA;
	
	}

}

?>