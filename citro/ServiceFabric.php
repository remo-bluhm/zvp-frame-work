<?php

/**
 * @author Max Plank
 *
 */
class ServiceFabric {
	
	
// 	const SERVOBJ_FILE_PRE_KEY = "Service";
	
// 	const SERVOBJ_KEY_SEPARATING = "_";
// 	const SERVOBJ_KEY_MAX = 10000;
	
 	const CONF_SERVICEFABRIC = "servicFabric";
	
	
	/**
	 * Die Configuration für die Service Fabric
	 * 
	 * @var Zend_Config NULL
	 */
	private $conf = array ();
	

	
	/**
	 * Enthällt die Configurationen die den einzelnen Services mit übergeben
	 * werden
	 * 
	 * @var Zend_Config
	 */
	private $serviceConfig = NULL;
	
	/**
	 * Erstellt ein Fabric für die abarbeitung von Request, Service und Action
	 * Objekten
	 *
	 * @param $Config Zend_Config Liste der Configuratioen die den Services mit übergeben werden sollen
	 */
	function __construct(Zend_Config $Config) {
		
		$this->conf = $Config;
	
	}
	

	
	
	/**
	 * Erstellt ein ServiceObjekt
	 * 1. Inizialisiert es 
	 * 2. Setzt die config für das Serviceobjekt(wenn vorhanden)
	 * 3. Setzt die übergebenen Objkete (User,Groupp,RightsAcl) 
	 * 4. Setzt die Parameter
	 * 5. Ruft die main Methode auf
	 *
	 * @param $service Service       	
	 * @return AService NULL
	 */
	public function getService(Service $service, ServiceResource $serviceResource, RightsAcl $rights) {
		
		// Setzt der Service Namen
		$ServName = $service->getName ();
	
		// Hollen des ServiceObjektes (require_once) falls es nicht exestiert wird False zurückgegeben
		/* @var $ServObj AService */
		$ServObj = $serviceResource->getServiceObject ( $ServName );
		
		// Prüfen auf vorhandensein des Objektes
		if ($ServObj !== FALSE) {
			
			
			// Setzen falls vorhanden die configuration des Service
			if ($this->serviceConfig !== NULL) {
				
				$servConf = $this->serviceConfig->get ( $ServName, FALSE );
				
				if ($servConf !== FALSE) {
					$ServObj->setConfig ( $servConf );
				}
			
			}
			
			// setzen der Service Fabric für weitere Unterfragen
			$ServObj->_setServiceFabric($this);
			
			// setzt das Rechtemanagement des users
			$ServObj->_setRightsAcl( $rights );
			
		
			// setzt die Resourcen
			$ServObj->_setResource( $serviceResource );
				
			// Setzen der Service Parameter
			$ServObj->setParams ( $service->getParams () );
			
			// aufrufen der Inizialisirungsmetode main()
			$ServObj->main ();
			
			return $ServObj;
		
		}
		
		return NULL;
	
	}
	

	
	/**
	 * Ruft ein Action(ActionsMethode) in dem übergebenen Services auf
	 *
	 * @param Service Actioner Auszuwertende Service mit wenn vorhandenseine Parametern
	 * @param string Actione Die Auszuwertente ActionsMethode
	 * @throws Exception Etliche Fehlermeldungen falls es nicht möglich ist die Action aufzurufen
	 * @return mixed Die Rückgabe der Action im Service Objekt
	 */
	public function getAction($resource, AService $service, Action $Action) {
	
		
 	
		// Erstellen des Methodennamens(Actionsname)
		$FullActionMethodName = ServiceResource::ACTIONPREE . $Action->getName ();

		// prüfen ob es eine Function mit dem namen der Action gibt
		if (method_exists ( $service, trim($FullActionMethodName) )) {
			
			
			// Hollt die ParameterDocu der Action
			$ParamsDocu = $resource->getParamsDocu ( $service->getName (), $Action->getName () );
	
			if($ParamsDocu === NULL){
				throw new Exception ( "Die Action: ".$service->getName ()."::".$Action->getName ()." konnte nicht gefunden werden! Vieleicht ist sie ausgestell oder private deklariert."  );
			}
			// enthällt nachher die Parameter die der function
			// call_user_func_array übergeben werden
			$SendParams = array ();
		
			// Durchläuft in der Docu die Parameter der Action
			// Falls die Docu nicht genau das wiederspiegelt welche realen
			// parameter die Methode besitzt kann es hier zu problemen
			// Führen
			foreach ( $ParamsDocu as $ParamName => $ParamValue ) {
				
				// Prüfen auf vorhandensein der Parameterdocu in der Docu
				if (is_array ( $ParamValue )) {
					
					// Feststellung der Position (Parameterposition)
					if (array_key_exists ( "POSITION", $ParamValue )) {
						$Position = $ParamValue ["POSITION"];
					} else {
						// Fehler muss den key POSITION besitzen
						throw new Exception ( "Fehler in der Docu Datei beim Key POSITION. Bitte Ihren Administator melden" );
					}
					
					// Prüfen ob der Parameter Optional oder Pflicht ist
					if (array_key_exists ( "OPTIONAL", $ParamValue )) {
						$Optional = $ParamValue ["OPTIONAL"];
					
					} else {
						
						// Fehler muss den key OPTIONAL besitzen
						throw new Exception ( "Fehler in der Docu Datei beim Key OPTIONAL. Bitte Ihren Administator melden" );
					}
					
					if (array_key_exists ( "TYPE", $ParamValue )) {
						$Type = $ParamValue ["TYPE"];
					} else {
						// Fehler muss den key TYPE besitzen
						throw new Exception ( "Fehler in der Docu Datei beim Key TYPE. Bitte Ihren Administator melden" );
					}
					
					// ist der zu übergebende Parameterwert
					$Value = NULL;
			
					// Hollen des Gesendeten Parameters
					/* @var $sendParam Param|NULL */
					$sendParam = $Action->getParam ( $ParamName );
							
					// Falls der Action die Parameternamen nicht übergeben wurden dann die Position Testen
					if ($sendParam === NULL) $sendParam = $Action->getParam ( $Position );
					
					
					// Prüfen ist der Parameter Optional oder Plicht
					if ($Optional == "TRUE") {
						
						// Prüfen ob es ein Standartbereich existiert falls nicht mit Fehler abbrechen
						if (array_key_exists ( "DEFALT", $ParamValue )) {
							$Default = $ParamValue ["DEFALT"];
							
							// auch hier Prüfen des Standartbereiches und auch hier mit Fehler abbrechen
							if ($Default !== FALSE && is_array ( $Default )) {
								
								if (array_key_exists ( "VALUETYPE", $Default ) && array_key_exists ( "VALUE", $Default )) {
									
									if ($sendParam !== NULL && is_a ( $sendParam, "Param" )) {
										
										require_once 'citro/service-class/ParamConverts.php';
										$paramConvValue = ParamConverts::Convert ( $sendParam->getValue (), $Default ["VALUETYPE"] );
										$Value = $paramConvValue;
						
									} else {
										// nimmt die Standartwerte an
										require_once 'citro/service-class/ParamConverts.php';
										$paramConvValue = ParamConverts::Convert ( $Default ["VALUE"], $Default ["VALUETYPE"] );
										$Value = $paramConvValue;
									
									}
								
								} else {
									throw new Exception ( "Fehler in der Docu Datei beim Key DEFALT sind kein VALUETYPE oder kein VALUE vorhanden. Bitte Ihren Administator melden" );
								}
							
							} else {
								throw new Exception ( "Fehler in der Docu Datei beim Key DEFALT (is not Set). Bitte Ihren Administator melden" );
							}
						
						} else {
							// Fehler muss den key DEFALT besitzen
							throw new Exception ( "Fehler in der Docu Datei beim Key DEFALT. Bitte Ihren Administator melden" );
						}
					
					} else {
						
						// ab hier Pflicht
						if ($sendParam !== NULL && is_a ( $sendParam, "Param" )) {
							// Es wurde ein parameter mit den namen Gesendet
							
							require_once 'citro/service-class/ParamConverts.php';
							$isValParam = ParamConverts::isValueType($sendParam->getValue (), $Type);
							if($isValParam === NULL)
								throw new Exception ( "Der Parameter( " . $ParamName . " ) in '".$service->getName ()."::".$Action->getName()."' ist mit keinen der vorgegebenen Typen valiede." );
							
							if($isValParam === FALSE)
								throw new Exception ( "Der Parameter( " . $ParamName . " ) in '".$service->getName ()."::".$Action->getName()."' wurde der Falsche Type übergeben." );
									
								
							$paramConvValue = ParamConverts::Convert ( $sendParam->getValue (), $Type );
							$Value = $paramConvValue;
							
						
						} else {
							// wurden kein Parameter mit den Namen gesendet
							// ABBRUCH
							throw new Exception ( "Der Parameter( " . $ParamName . " ) in der Action(" . $Action->getName() . ")  ist ein Pflichtparamere." );
						}
					
					} // Ende der Prüfung auf Pflicht
					
					$SendParams [$Position] = $Value;
				
				} else {
					throw new Exception ( "Fehlend Parameterangaben in der Docu für den Parmeter( " . $ParamName . " )" );
				}
			
			} // Ende Foreach schleife mit Parameter setzen
			
			try {
				
				$responce = call_user_func_array ( array ($service, $FullActionMethodName ), $SendParams );
			
			} catch ( Exception $e ) {
				
				//throw new Exception ( "Fehler beim directen aufruf des Services(".$service->getName().") der Action( " . $FullActionMethodName . " ) mit den Fehler: <br>" . $e->getMessage () );
				throw new Exception ( $e->getMessage () );
			}
			
			return $responce;
		}
		
	
	
	}
	

}

?>