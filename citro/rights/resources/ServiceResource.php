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
class ServiceResource {
	
	/**
	 * enthällt den Ordner wo die Services liegen
	 * @var array 
	 */
	private $ServiceDictionary = array (); 
	
	/**
	 * Resourcen und deren Beschreibungen
	 * @var array
	 */
	private $_resourceDocu = NULL; 
	
	private $ResourcenDocuIsON = NULL;
	
	private $_confService = NULL;
	
	const SERVICEPREE = "Service";
	const ACTIONPREE = "Action";
	const SERVICEACTIONSEPARATOR = "_";
	
	const RES_SERVNAME = "SERVICENNAME";
	const RES_SHORTDESC = "SHORTDESCRIPTION";
	const RES_LONGDESC = "LONGDESCRIPTION";
	const RES_ACTIONEN = "ACTIONS";
	
	const CONF_SERVICERESOURCE = "serviceResource";
	const CONF_SERVICE = "service";
	const CONF_ALLDIRECTORY = "allDirectory";
	const CONF_SERVICEDIR = "dir";
	
	private static $FileDescTag_CitroIsOn_Text = "citro_isOn";
	private static $FileDescTag_protocollExist_Text = "citro_protocollExist";
	private static $FileDescTag_CitroIsOn_Value = "false";
	
	private static $ConfigTag_Service_IsOn_Text = "isOn";
	private static $ConfigTag_Service_IsOn_Value = "false";
	
	private static $ConfigTag_Actions_Text = "actions";
	private static $ConfigTag_Action_IsOn_Text = "isOn";
	private static $ConfigTag_Action_IsOn_Value = "false";
	
// 	public function getServiceConfig() {
// 		return $this->_confService;
// 	}

	/**
	 * Giebt de gesamte Resourcendocku zurück
	 *
	 * @return array
	 */
	public function getDocu() {

		return $this->_resourceDocu;
	
	}
	
	function __construct($Conf = NULL) {
		
		if ($Conf !== NULL) {
			$this->setConfig ( $Conf );
		}
	
	}
	
	public function Create() {
		
		$this->ResourcenDocuIsON = NULL;
		$this->_resourceDocu = self::getServResDocu ( $this->ServiceDictionary, $this->_confService );
	}
	
	/**
	 *
	 * @var Zend_Config NULL die Configurations einstellungen für die Services
	 *      mit Ihnen kann man den Service oder die Actionen ausstellen
	 *      Wenn dieses nicht explizit gesetzt wurden ist, ist der Wert NULL
	 */
	public function setConfig(Zend_Config $conf) {
		
		$allServDir = $conf->get ( self::CONF_ALLDIRECTORY, array () );
		if (is_array ( $allServDir )) {
			$allServDirA = $allServDir;
		} else {
			$allServDirA = $allServDir->toArray ();
			$allServDirA = $allServDirA [self::CONF_SERVICEDIR];
		}
		
		$this->_setDirectory ( $allServDirA );
		
		$this->_confService = $conf->get ( self::CONF_SERVICE, NULL );
	}
	
	
	
	/**
	 * Setzen der Verzeichnise als Url(string)
	 * oder als Array
	 * 
	 * @param $Directory string|array       	
	 */
	private function _setDirectory($Directory) {
		
		// enthällt die exestierenten Servises mit Ihren Namen und Adresse aus
		// einen Verzeichnis Baum
		if (is_string ( $Directory )) {
			$this->ServiceDictionary = self::getAllServicesFiles ( $Directory );
		}
		
		// Falls ein Array von Verzeichnissen übergeben wird dann das abarbeiten
		if (is_array ( $Directory )) {
			foreach ( $Directory as $DictionaryStr ) {
				$this->ServiceDictionary = array_merge ( $this->ServiceDictionary, self::getAllServicesFiles ( $DictionaryStr ) );
			}
		}
	}
	

	
	
	/**
	 * Prüft in der Docu des Services oder seiner Action ob die geforderten Services vorhanden sind 
	 * Die ProtocollNameA kommen aus den Protocoll objekt 
	 * 
	 * @param array $protocollNameA Eine Liste von den gesendeten Protocollen
	 * @param string $ServiceName
	 * @param string $ActionName
	 */
	public function isProtocolleOk(array $protocollNameA, $ServiceName, $ActionName = NULL){

		// hollen der ServiceDoumentation aus allen docus nicht nur aus den eingeschalteten
		$servDocu = $this->getServiceDocu($ServiceName);
		
		if( $servDocu !== FALSE){

				if( $this->testProtocoll($protocollNameA,$servDocu) === TRUE ) {
				
					if($ActionName !== NULL ){
							
						
						$actionDocu = $this->getActionDocu($ServiceName,$ActionName);
						if($actionDocu !== FALSE ){
					
							if($this->testProtocoll($protocollNameA,$actionDocu) === TRUE){
								return TRUE;
							}
						}
						

					return FALSE;
					}
					
				return TRUE;
				}
		}		
	return FALSE;
	}

	private function testProtocoll($secLevel,$servDocu){
		
		if(is_array($servDocu) && array_key_exists("PROTOCOLL_EXIST", $servDocu)){

			
			$secLevDocu = $servDocu["PROTOCOLL_EXIST"];

			
			if(is_array($secLevDocu) && array_key_exists("PARAM", $secLevDocu)){
		
				$secLevParamDocu =  $secLevDocu["PARAM"];
				if(is_array($secLevParamDocu)){
						
					// Durchläuft im Docu die enthaltenen SecurityLevels des jeweiligen services
					foreach ($secLevParamDocu as $secLevelElement){
						//Prüfft ob der Sicherheitswert in der Anfrage enthalten ist
						if(in_array($secLevelElement, $secLevel)){
							// wurde gefunden	
							return TRUE;
								
						}
					}	
				}
		
			}
			return FALSE;
				
		}else {
			return TRUE;
		}
	}
	
	
	
	/**
	 * Liefert auf grund der ResourcenListe nur die Docus die in der
	 * Resourcenliste enthalten sind
	 * 
	 * @param $myResourceList array       	
	 */
	public function getMyDocu($myResourceList, $onlyIsOn = TRUE) {
		
		if (! is_array ( $this->_resourceDocu )) 
			return NULL;
		
		
		$myDocu = array ();
		
		foreach ( $this->_resourceDocu as $servName => $servData ) {
			
			$actions = array ();
			if (array_key_exists ( "ACTIONS", $servData ) && is_array ( $servData ["ACTIONS"] )) {
				
				foreach ( $servData ["ACTIONS"] as $actionName => $actionData ) {
					
					if (in_array ( $servName . self::SERVICEACTIONSEPARATOR . $actionName, $myResourceList )) {
						if ($this->_testOnlyIsOn ( $onlyIsOn, $actionData )) {
							
							if ($onlyIsOn) {
								unset ( $actionData ["ISON"] );
								unset ( $actionData ["CONF_ISON"] );
							}
							$actions [$actionName] = $actionData;
						}
					
					}
				
				}
			
			}
			
			if (in_array ( $servName, $myResourceList )) {
				
				if ($this->_testOnlyIsOn ( $onlyIsOn, $servData )) {
					if ($onlyIsOn) {
						unset ( $servData ["ISON"] );
						unset ( $servData ["CONF_ISON"] );
					}
					
					$myDocu [$servName] = $servData;
					$myDocu [$servName] ["ACTIONS"] = $actions;
				}
			
			}
		
		}
		
		return $myDocu;
	
	}
	
	private function _testOnlyIsOn($onlyIsOne, $data) {
		$allowIsOn = TRUE;
		
		if ($onlyIsOne === TRUE) {
			if (array_key_exists ( "ISON", $data ) && $data ["ISON"] == "FALSE") {
				
				$allowIsOn = FALSE;
			}
			
			if (array_key_exists ( "CONF_ISON", $data ) && $data ["CONF_ISON"] == "FALSE") {
				$allowIsOn = FALSE;
			}
		
		}
		
		return $allowIsOn;
	}
	
	/**
	 * Erstellt eine Resourcendockumentation in form einer Array von den
	 * übergebenen Service Verzeichnissen(verzeichnis)
	 *
	 * Die Rückgabe erfollgt ohne sepparrierung der in der Configurtionsdatei
	 * eingestellten "isOn" werte
	 * Die Rückgabe erfollgt ohne sepparrierung der in der Servicdockumentation
	 * und Methodenduckumentation eingestellten "isOn" werte
	 * Ausgelesen werden aber die "isOn" werte schon
	 *
	 * @param $FieleUrlWithServiceName array ServicNamen mit den Pfaden zu den Verzeichnissen zb. $FieleUrlWithServiceName[$Servicname] = $Url
	 * @return array
	 *
	 */
	public static function getServResDocu(array $FieleUrlWithServiceName, Zend_Config $Config = NULL) {
		
		$ServiceDocu = array ();
		
		// Durchlaufen der Gesamten Files mit rückgabe des ServiceName und
		// dessen adresse ServFile
		foreach ( $FieleUrlWithServiceName as $ServiceName => $ServFile ) {
			
			// Der Klassenname des Services
			$ClassName = self::SERVICEPREE . $ServiceName;
			
			// wird geprüft ob der service in der Config Ausgeschalten ist und
			// ob der Fiele existiert
			
			if (file_exists ( $ServFile )) {
				
				// muss required werden befor Reflection ausgeführt werden kann
				require_once $ServFile;
				require_once 'Zend/Reflection/File.php';
				$ReflFile = new Zend_Reflection_File ( $ServFile );
				
				// TODO falls es hier zu einer Ausnahme kommt sollte diese
				// klasse nicht mit aufgenommen werden
				// Falls es keine Klasse mit den Namen gibt wirft die function
				// eine Exception
				try {
					$ServClass = $ReflFile->getClass ( $ClassName );
					$classIsReflection = TRUE;
				} catch ( Exception $e ) {
					$classIsReflection = FALSE;
				
				}
				
				if ($classIsReflection) {
					
					$ServiceA = array ();
					
					// Hollt die Classen Dockumentation
					$ServiceA = self::_ClassenDocu ( $ServClass );
					
					$ServiceA ["CONF_ISON"] = "TRUE";
					if (self::ServActIsOnInConf ( $Config, $ServiceName ) === FALSE) {
						$ServiceA ["CONF_ISON"] = "FALSE";
					}
					
					// Actionen werden geprüfft
					$Actionen = array ();
				
					
					// durchläuft erstmal alle Methoden der Classe
					/*
					 * @var $Methode Zend_Reflection_Method
					 */
					foreach ( $ServClass->getMethods () as $Methode ) {
					
						// Hollt sich den Methoden namen erstmal
						$MethodName = $Methode->getName ();
						
						// Prüfft ob der Methode ein ActionPree(Action)
						// vorrangestellt ist
						if (substr ( $MethodName, 0, strlen ( self::ACTIONPREE ) ) === self::ACTIONPREE) {
							// sommit währe es jetzt eine Actionsmethode
							
							// hollt sich erst mal den
							// Actionsnamen(Methodennamen ohne ActionsPree)
							$ActionName = substr ( $MethodName, strlen ( self::ACTIONPREE ) );
							
							// 1 Prüfen ob die Methode den ActionsPre
							// vorrangestellt hat
							// 2 Prüfen ob in der Config eingeschaltet ist
							if ($ActionName !== FALSE) {
								
								require_once 'Zend/Reflection/Parameter.php';
								
								// 2 Prüfft ob die Actionsmethode public ist
								if ($Methode->isPublic ()) {
									
									$Action = self::_MethodenDocu ( $Methode );
									
									$Action ["CONF_ISON"] = "TRUE";
									
									if (self::ServActIsOnInConf ( $Config, $ServiceName, $ActionName ) === FALSE) {
										$Action ["CONF_ISON"] = "FALSE";
									}
									
									$Actionen [$ActionName] = $Action;
								
								}
							}
						}
					
					}
					
					$ServiceA [self::RES_ACTIONEN] = $Actionen;
					$ServiceDocu [substr ( $ServClass->getName (), strlen ( self::SERVICEPREE ) )] = $ServiceA;
				
				}
			
			}
			
		}
		
		return $ServiceDocu;
	
	}
	
	/**
	 * Durchläuft den Docblock eine Classe und schreibt die Parameter in ein
	 * Array
	 * 
	 * @param $ClassReflection Zend_Reflection_Class       	
	 * @return array
	 */
	private static function _ClassenDocu(Zend_Reflection_Class $ClassReflection) {
		
		$ServiceArray = array ();
		$ServiceName = $ClassReflection->getName ();
		
		try {
			// lösst ausnahme aus wenn es kein Dockblock giebt Dockumentation ist vorhanden
			/* @var $ClassDockBlock Zend_Reflection_Docblock */
			$ClassDockBlock = $ClassReflection->getDocblock (); 
			
			$DockBlockClass = TRUE;
		} catch ( Exception $e ) {
			// Dockumentation Fehlt
			$DockBlockClass = FALSE;
		}
		
		// hiermit ist auch ohne Dockumentation der Service an
		$ServiceArray ["ISON"] = "TRUE";
		if ($DockBlockClass === TRUE) {
			
			
			$secLevel = self::_docblock_ProtocollExistValue($ClassDockBlock);
			if($secLevel !== FALSE){
				$ServiceArray ["PROTOCOLL_EXIST"] = $secLevel;
			}
			
			// Prüfft ob der service im Dockblock ausgeschalten ist
			if (self::_docblock_CitroIsOn ( $ClassDockBlock ) == FALSE) {
				$ServiceArray ["ISON"] = "FALSE";
			}
			// Setzen des Service Namens
			$ServiceArray [self::RES_SERVNAME] = $ServiceName;
			// Service Description
			$ServiceArray [self::RES_SHORTDESC] = $ClassDockBlock->getShortDescription () ;
			// Service Description Long
			$ServiceArray [self::RES_LONGDESC] = $ClassDockBlock->getLongDescription () ;
		
		}
		
		return $ServiceArray;
	}
	
	/**
	 * Durchläuft den Docblock eine Action und schreibt die Parameter in ein Array
	 * 
	 * @param $MethodReflection Zend_Reflection_Method       	
	 * @return array
	 */
	private static function _MethodenDocu(Zend_Reflection_Method $MethodReflection) {
		
		$DockuArray = array ();
		
		// standartwert falls es keine Parameter vorhanden sind Enthällt alle Parameter der Actionsmethode
		$DockuArray ["PARAMS"] = array (); 
		$MethodParamsArray = $MethodReflection->getParameters ();
		if (count ( $MethodParamsArray ) > 0) {
			
			$ParamMethode = array ();
			require_once 'Zend/Reflection/Parameter.php';
			/*
			 * @var $ZRParm Zend_Reflection_Parameter
			 */
			foreach ( $MethodParamsArray as $ZRParm ) {
				
				
				$ParamMethode [$ZRParm->getName ()] ["NAME"] = $ZRParm->getName ();
				$ParamMethode [$ZRParm->getName ()] ["POSITION"] = $ZRParm->getPosition ();
				
				
				if ($ZRParm->isDefaultValueAvailable ()) {
								
					try {
						$ParamMethode [$ZRParm->getName ()] ["DEFALT"] ["TYPE"] = $ZRParm->getType ();
					} catch (Exception $e) {
				
						throw new Exception("Die Docu der Action: ".$MethodReflection->getName()." ist falsch Documentiert",E_ERROR,$e);
					
					}
					
					$ParamMethode [$ZRParm->getName ()] ["DEFALT"] ["VALUE"] = $ZRParm->getDefaultValue ();
					$ParamMethode [$ZRParm->getName ()] ["DEFALT"] ["VALUETYPE"] = gettype ( $ZRParm->getDefaultValue () );
				
				} else {
					$ParamMethode [$ZRParm->getName ()] ["DEFALT"] = "FALSE";
				}
				
				// parameter ist optional
				$ZRParm->isOptional () === True ? $opt = "TRUE" : $opt = "FALSE";
				$ParamMethode [$ZRParm->getName ()] ["OPTIONAL"] = $opt;
			
			}
		$DockuArray ["METHODPARAMS"] = $ParamMethode;
		$DockuArray ["PARAMS"] = $ParamMethode;
		}
		
		// Setzt standartwerte
		$DockuArray ["DOCKBLOCK"] = "NO_SET";
		$DockuArray ["SHORTDESC"] = "NO_SET";
		$DockuArray ["LONGDESC"] = "NO_SET";
		$DockuArray ["ISON"] = "TRUE";
		
		try {
			// Enthällt den Dockblock der Actionsmethode
			/*
			 * @var $MethodDocblock Zend_Reflection_Docblock
			 */
			$MethodDocblock = $MethodReflection->getDocblock ();
			$DockuArray ["DOCKBLOCK"] = "ACTUAL";
			$DockBlockIsOk = TRUE;
		
		} catch ( Exception $e ) {
			$DockuArray ["DOCKBLOCK"] = "ERROR";
			$DockBlockIsOk = FALSE;
		}
		
	
		
		// prüfft ob er Dockblock überhaubt existiert
		if ($DockBlockIsOk) {
			
			
			
			
			
			
			$secLevel = self::_docblock_ProtocollExistValue($MethodDocblock);
			if($secLevel !== FALSE){
				$DockuArray ["PROTOCOLL_EXIST"] = $secLevel;
			}
			
			
			
			
			
			// prüfft ob im Dockblock die Action ausgeschaltet ist
			if (self::_docblock_CitroIsOn ( $MethodDocblock ) === FALSE) {
				$DockuArray ["ISON"] = "FALSE";
			}
			
			
			
			
			// Enthällt die Beschreibung der Action
			$DockuArray ["SHORTDESC"] = $MethodDocblock->getShortDescription () ;
			$DockuArray ["LONGDESC"] = $MethodDocblock->getLongDescription () ;
			
			require_once 'Zend/Reflection/Docblock/Tag/Param.php';
			
			// Durchläuft alle Teags des Dockblocks
			/* @var $DockTag Zend_Reflection_Docblock_Tag_Param*/
			foreach ( $MethodDocblock->getTags () as $DockTag ) {
				
				/*
				 * @var $Param Zend_Reflection_Docblock_Tag
				 */
				$Name = $DockTag->getName (); // Der Name des Tags zb. param,throw,return
				$Desc = $DockTag->getDescription (); // die Beschreibung des Tags
				
				//$DockuArray ["PARAMS"]["test"] =  $DockTag->getVariableName ();
				if (is_a($DockTag, "Zend_Reflection_Docblock_Tag_Param") ) {
					/*
					 * @var $P_Param Zend_Reflection_Docblock_Tag_Param
					 */
					$P_Param = $DockTag;
					$VName = $P_Param->getVariableName ();
					$NameParm = substr ( $VName, 1 );
					
					if ($NameParm != FALSE && array_key_exists ( $NameParm, $DockuArray ["PARAMS"] )) {
						$DockuArray ["PARAMS"] [$NameParm] ["TYPE"] = $P_Param->getType ();
						$DockuArray ["PARAMS"] [$NameParm] ["DESC"] =  $Desc ;
					}
				
				}
				
				
				if ($Name == "throws") {
					/*
					 * @var $P_Throw Zend_Reflection_Docblock_Tag
					 */
					// $P_Throw = $DockTag;
					$DockuArray ["THROWS"] [] = array ("DESC" => $Desc , "TYPE" => $Name );
				}
				if ($Name == "return") {
					/*
					 * @var $P_Return Zend_Reflection_Docblock_Tag_Return
					 */
					// $P_Return = $DockTag;
					$DockuArray ["RETURN"] ["TYPE"] = $DockTag->getType ();
					$DockuArray ["RETURN"] ["DESC"] =  $Desc ;
				
				}
				
				if ($Name == "deprecated") {
					$DockuArray ["DEPRECATED"] = $Desc ;
				}
				
				// ist nur für die künftige einbindung
				$allTag ["TAGNAME"] = $Name;
				$allTag ["DESC"] = $Desc ;
				$DockuArray ["ALLTAGS"] [] = $allTag;
			
			}
		
		}
		
		return $DockuArray;
	}
	
	/**
	 * Giebt alle Dateinamen eines Servicesverzeichnis in einen Array zurück.
	 * Giebt nur die datein die den Prefix "Service" haben.
	 * 
	 * @param string Verzeichnisory
	 * @throws Exception "Der Pfad zu der Resource ( $Directory ) ist nicht vorhanden"
	 * @throws Exception "Die Übergabe des Verzeichnises mus ein String sein"
	 * @return array:
	 */
	public static function getAllServicesFiles($Directory) {
		
		$Files = array ();
		
		// Prüfen auf String
		if (! is_string ( $Directory )) {
			throw new Exception ( "Die Übergabe des Verzeichnises mus ein String sein" );
		}                                                                                                    

		// Test auf Verzeichniss
		if (is_dir ( $Directory )) {
			
			$Verzeichnis = opendir ( $Directory );
			
			
			
			while ( FALSE !== ($serviceName = readdir ( $Verzeichnis )) ) {
				
				if (($serviceName != ".") and ($serviceName != "..")) {
				
					if(substr($Directory, -1) != DIRECTORY_SEPARATOR) $Directory = $Directory.DIRECTORY_SEPARATOR;
					
					
					// Prüfen ob es ein Serviceverzeichnis giebt
					if(is_dir($Directory.$serviceName)) {
		
							
						$servName = substr ( $serviceName, strlen ( self::SERVICEPREE ), - strlen ( ".php" ) );
						
						// URL zusammenbauen Verzeichnis mit Separator + ServiceName (ordner) + Separator mit Dateinamen (inkl .php)
						$ServiceFileUrl =  $Directory. $serviceName . DIRECTORY_SEPARATOR . self::SERVICEPREE . $serviceName . ".php" ;
						
					
						// Einschreiben des Files 
						if(is_file ( $ServiceFileUrl)){
							$Files [$serviceName] = $ServiceFileUrl;
						}
							
						
							
					
						
						
						
						
					}
				
					

				
				}
			}
			
			closedir ( $Verzeichnis );
		} else {
			throw new Exception ( "Der Pfad zu der Resource (" . $Directory . ") ist nicht vorhanden" );
		}
	
		return $Files;
	
	}
	
	/**
	 * Prüft ob in einen Docblock der Parameter citro_isOn gesetzt ist und
	 * dieser auf false steht
	 * Giebt immer TRUE zurück wenn nicht "citro_isOn false" im docblock gesetzt
	 * ist da wird FALSE zurückgegeben
	 * 
	 * @param $ClassDockBlock Zend_Reflection_Docblock       	
	 * @return boolean
	 */
	private static function _docblock_CitroIsOn(Zend_Reflection_Docblock $ClassDockBlock) {
		
		$classIsOnIsFalse = TRUE;
		
		if (is_object ( $ClassDockBlock )) {
			
			$classIsOnDocValue = NULL;
			
			if ($ClassDockBlock->hasTag ( self::$FileDescTag_CitroIsOn_Text )) {
				
				/* @var $ClassIsOnDoc Zend_Reflection_Docblock_Tag */
				$ClassIsOnDoc = $ClassDockBlock->getTag ( self::$FileDescTag_CitroIsOn_Text );
				// Prüfen auf ein "false" im DockBlock des CitroIsOn Tags
				$classIsOnDocValue = substr ( trim ( $ClassIsOnDoc->getDescription () ), 0, strlen ( self::$FileDescTag_CitroIsOn_Value ) );
			}
			
			if ($classIsOnDocValue == self::$FileDescTag_CitroIsOn_Value) {
				$classIsOnIsFalse = FALSE;
			
			}
		
		}
		return $classIsOnIsFalse;
	
	}
	
	/**
	 * Prüft ob in einen Docblock der Parameter citro_isOn gesetzt ist und
	 * dieser auf false steht
	 * Giebt immer TRUE zurück wenn nicht "citro_isOn false" im docblock gesetzt
	 * ist da wird FALSE zurückgegeben
	 *
	 * @param $ClassDockBlock Zend_Reflection_Docblock
	 * @return boolean|array
	 */
	private static function _docblock_ProtocollExistValue(Zend_Reflection_Docblock $ClassDockBlock) {
	
		$securityLevelA = array();
	
		if (is_object ( $ClassDockBlock )) {
				
			if ($ClassDockBlock->hasTag ( self::$FileDescTag_protocollExist_Text )) {
	
				/* @var $ClassIsOnDoc Zend_Reflection_Docblock_Tag */
				$ClassIsOnDoc = $ClassDockBlock->getTag ( self::$FileDescTag_protocollExist_Text );
				
				// Prüfen auf ein "false" im DockBlock des CitroIsOn Tags
				
				$secLevDocu = trim ( $ClassIsOnDoc->getDescription () );
				
				// Giebt die Position des Leerzeichens nach den securityLevel einträgen zurück
				// FALSE position nicht gefunden weil vieleicht keine Dockumentation dazu ist
				$strpos = strpos($secLevDocu, " ");
				
				$secLevelArray = array();
				$secLevelDocu = "";
				
				if($strpos === FALSE && strlen($secLevDocu) > 3 ){
					$secLevelArray = explode("|", $secLevDocu);
					
				}
				
				if($strpos !== FALSE){
					$secLevString = substr( $secLevDocu, 0, $strpos);
					$secLevelArray = explode("|", $secLevString);
					
					$secLevelDocu = substr( $secLevDocu, $strpos+1);
				}
				
				$securityLevelA["DOCU"] = $secLevelDocu;
				$securityLevelA["PARAM"] = $secLevelArray;
				
			}else {
				return FALSE;
			}
				
	
		}
		return $securityLevelA;
	
	}
	
	/**
	 * Prüft ob in der Configuration ob der Service oder die action
	 * ausgeschalten ist
	 * Die Action des Services ist erst dann OFF wenn in der config under den
	 * Services im bereich "activities die Action isOn = false" ist.
	 * ansonsten gild er immer als ON
	 * Soll nur der Service geprüft werden muss den zweiten
	 * Parameter(ActionName) den Standartwert NUll übergeben werden
	 * Falls keine config übergeben wird ist der service auch ON
	 * 
	 * @param $ServiceName string       	
	 * @param $config Zend_Config       	
	 * @return boolean
	 */
	public static function ServActIsOnInConf(Zend_Config $config, $ServiceName, $ActionName = NULL) {
		
		if ($config !== NULL) {
			
			/*
			 * @var $ConfObjServ Zend_Config|NULL
			 */
			$ConfObjServ = $config->get ( $ServiceName, NULL );
		
			
			if ($ConfObjServ !== NULL) {
				
				if ($ConfObjServ->get ( self::$ConfigTag_Service_IsOn_Text, TRUE ) === self::$ConfigTag_Service_IsOn_Value) {
					
					return FALSE;
				
				} else {
					
					if ($ActionName !== NULL) {
						
						$Activities = $ConfObjServ->get ( self::$ConfigTag_Actions_Text, NULL );
						if ($Activities != NULL) {
							
							$ConfObjAct = $Activities->get ( $ActionName, NULL );
							if ($ConfObjAct != NULL && $ConfObjAct->get ( self::$ConfigTag_Action_IsOn_Text, TRUE ) === self::$ConfigTag_Action_IsOn_Value) {
								return FALSE;
							
							}
						}
					}
				}
			}
			
		
		}
		return TRUE;
	}
	
	/**
	 * Giebt aus der Docku nur die Services und Actionen die Eingeschalten sind
	 * in dem File sowie in der Config
	 *
	 * @param $FileAll bool	 Ist eingeschaltet(TRUE) dann wird die File IsOn ignoriert
	 * @param $ConfAll bool	 Ist eingeschaltet(TRUE) dann wird die Config IsOn ignoriert
	 * @return multitype:unknown
	 */
	public function getDocuIsON($FileAll = FALSE, $ConfAll = FALSE) {
	
		if ($this->_resourceDocu === NULL) {
			return NULL;
		}
		
		if ($FileAll && $ConfAll) {
			return $this->_resourceDocu;
		}
		
		// macht die abfrage zum Singelton muster
		if ($this->ResourcenDocuIsON != NULL) {
			return $this->ResourcenDocuIsON;
		}
		
		$Services = array ();
		
		foreach ( $this->_resourceDocu as $ServKey => $ServValue ) {
			
			
			if ($ServValue ["CONF_ISON"] == "TRUE" && $ServValue ["ISON"] == "TRUE") {
				
				$Actionen = array ();
				foreach ( $ServValue [self::RES_ACTIONEN] as $ActKey => $ActValue ) {
					
					if ($ActValue ["CONF_ISON"] == "TRUE" && $ActValue ["ISON"] == "TRUE") {
						
						$Actionen [$ActKey] = $ActValue;
					}
				}
				
				$Services [$ServKey] = $ServValue;
				$Services [$ServKey] [self::RES_ACTIONEN] = $Actionen;
			}
		}
		$this->ResourcenDocuIsON = $Services;
		return $Services;
	}
	

	
	/**
	 * Giebt die ServiceDoumentation zurück aber nur die On sind
	 * @param unknown_type $ServiceName
	 * @param unknown_type $ServiceName
	 * @return Ambigous <unknown>
	 */
	public function getServiceDocu($ServiceName, $docuIsOn = TRUE) {
		if($docuIsOn === TRUE){
			$Docu = $this->getDocuIsON ();
		}else {
			$Docu = $this->getDocu();
		}
		
		
		if (is_array ( $Docu ) && is_string($ServiceName) && array_key_exists ( $ServiceName, $Docu )) {
			return $Docu [$ServiceName];
		}
		return FALSE;
	
	}
	public function getActionDocu($ServiceName, $ActionName, $docuIsOn = TRUE) {
		
	
		if($docuIsOn === TRUE){
			$Docu = $this->getServiceDocu ( $ServiceName );
		}else {
			$Docu = $this->getServiceDocu ( $ServiceName , FALSE);
		}
		
		
		if ($Docu !== FALSE && is_array ( $Docu ) && array_key_exists ( "ACTIONS", $Docu )) {
			
			$ActionsDocu = $Docu ["ACTIONS"];
			
			if (is_array ( $ActionsDocu ) && array_key_exists ( $ActionName, $ActionsDocu )) {
				
				return $ActionsDocu [$ActionName];
			
			}
		
		}
		return FALSE;
	
	}
	public function getParamsDocu($ServiceName, $ActionName) {
		$ActionDocu = $this->getActionDocu (  $ServiceName, $ActionName );
		
		if (is_array ( $ActionDocu ) && array_key_exists ( "PARAMS", $ActionDocu )) {
			
			$ParamsDocu = $ActionDocu ["PARAMS"];
			return $ParamsDocu;
		
		}
	
	}
	

	private $_resourceList = NULL;
	/**
	 * Prüft ob eine Resource Existiert
	 *
	 * @param string $resourceName 
	 * @return boolean
	 */
	public function ResourceExist($resourceName){
		
		if($this->_resourceList === NULL)	
			$this->_resourceList =  $this->getResourcenList ( TRUE );
		
		$inArray = in_array ( $resourceName, $this->_resourceList );
		return $inArray;
		
	}
	
	/**
	 * Giebt ein Resourcen Liste(zweidimensional) zurück in dem Alle Resourcen mit Ihren Namen enthalten sind
	 * Hollt nur Resourcen die mit den Parameter(citro_isOn) ind der Docku nicht "false" gescheltet sind
	 * Ist für RightsAcl gedacht
	 */
	public function getResourcenArray() {
		
	
		if ($this->getDocuIsON () === NULL)
			return array();
		
		$ResList = array ();
		
		foreach ( $this->getDocuIsON () as $ServKey => $ServValue ) {
			
			$ResListAction = array ();
			if (array_key_exists ( self::RES_ACTIONEN, $ServValue )) {
				
				$ResAct = $ServValue [self::RES_ACTIONEN];
				if (is_array ( $ResAct )) {
					foreach ( $ResAct as $ActKey => $ActVal ) {
						$ResListAction [] = $ActKey;
					}
				}
			}
			
			$ResList [$ServKey] = $ResListAction;
		}
		
		return $ResList;
	}
	
	
	
	public function getResourcenList($withAction = FALSE) {
		
		
		if ($this->getDocuIsON () === NULL) 
			return array();
		
		
		
		$ResList = array ();
		foreach ( $this->getDocuIsON () as $ServKey => $ServValue ) {
			
			$ResList [] = $ServKey;
			
			if ($withAction) {
				if (array_key_exists ( self::RES_ACTIONEN, $ServValue )) {
					
					$ResAct = $ServValue [self::RES_ACTIONEN];
					if (is_array ( $ResAct )) {
						foreach ( $ResAct as $ActKey => $ActVal ) {
							$ResList [] = $ServKey.self::SERVICEACTIONSEPARATOR.$ActKey;
						}
					}
				}
			}
		
		}
		
		return $ResList;
	}
	
	/**
	 * Giebt das Service Objekt Zurück falls es nicht gespert und es gefunden
	 * wurde
	 * 
	 * @param $DateiName string       	   	
	 * @return boolean aServiceObjekt FALSE Objekt nicht gefunden oder nicht erlaubt
	 */
	public function getServiceObject($ServName) {
		
		$DateiName = $this->ServiceDictionary [$ServName];
		
		$ClassName = self::SERVICEPREE . $ServName;
		
		// prüfen ob die datei wirklich exestiert und dann erst includen
		try {
			require_once $DateiName;
		
		} catch ( Exception $e ) {
			
			return FALSE;
		}
		
		if (class_exists ( $ClassName, false )) {
			
			// erstellen der Webservice Klasse
			/*
			 * @var $WSClass aServiceObjekt
			 */
			$WSClass = new $ClassName ();
			
			// $WSClass->setConfig($ServConf);
			if (is_object ( $WSClass )) {
				
				// Rückgabe der Klasse
				return $WSClass;
			
			} else {
				return FALSE;
			}
		
		} else {
			return FALSE;
		}
	
	}

}

?>