<?php

/**
 * class protocol
 *
 * Description for class protocol
 *
 * @author:
*/
class Protocol {
	
	const MAX_TYPE_LENGTH = 5;
	const CONFIG_TEXT_PROTOCOLL_LENGTH = "protocollength";
	const CONFIG_TEXT_PROTOCOLL_FILENAME = "filename";
	const CONFIG_TEXT_PROTOCOLL_CLASSNAME = "classname";
	
	private $ConfigProdocole = NULL;
	private $RawSendSting = null;
	
	private static $MaxProtokollLang = 20;
	private static $ProtokollTrenner = ":";
	
	/**
	 * @var AProtocol
	 */
	private $ProtocolTree = NULL;

	
	/**
	 * protocol constructor
	 *
	 * @param $ConfProtocol Enthällt die Configuration des Protocol objektes.
	 */
	function Protocol(Zend_Config $ConfProtocol) {
		$this->ConfigProdocole = $ConfProtocol->get ( 'protocollist', NULL );
	
	}
	
	
	/**
	 * Giebt alle Angefragten Protocoll Namen zurück
	 * 
	 * @return array 
	 */
	public function getAllProtocolName(){
		if($this->ProtocolTree === NULL)return array();
		$prolNameArray = $this->ProtocolTree->getAllProtocolName();
		return $prolNameArray;
	}
	
	/**
	 * Giebt den Protokoll Baum zurück
	 * falls beim setzen der Request probleme gab wird hier Null zurückgegeben
	 * 
	 * @return AProtocol NULL
	 */
	public function getProtocollTree() {
		return $this->ProtocolTree;
	}
	
	

	


	/**
	 * Filtriert ein Protokoll aus einen Sendestring
	 * also wenn einen Sendestring ein Protokollstring davor gesetzt wurde liest
	 * dies das aus
	 *
	 * @param $SendString mixed Ein sendestring
	 * @return mixed Der Reststring ohne das Prodokoll falls kein prodokoll mehr davor ist dann null
	 *        
	 */
	public function setRequest($SendString) {

		
	
		if (! is_string ( $SendString )) {
			return NULL; // fehler da der Raw sende string null übergebenwurde
		} else {
			
			// definiert die Warscheinliche Länge des Protokolltypen
			$ProtTypeLeng = stripos ( $SendString, self::$ProtokollTrenner );
			
			// Prüfen der Protokollänge
			if ($ProtTypeLeng === FALSE || $ProtTypeLeng > self::$MaxProtokollLang) {
				
				return NULL;
				
			}
			
			// testet auf die warscheinlich das eine protokoll übergeben wurde
			$ConfProt = $this->ConfigProdocole->get ( substr ( $SendString, 0, $ProtTypeLeng ), NULL );

			// hollt fals vorhanden die ConfigDaten ansonsten falls hier nichts mit den namen gefunden wurde wird "null" zurückgegeben
			if ($ConfProt === NULL) {

				return NULL; // Abbruch da es nicht in der Configuration gefunden wurde
			} else {
			
				try {
					$ProtObjekt = $this->setProtocolInlcude ( $ConfProt );
					// Abarbeiten des auszuwertenten Protokolles und rückgabe
					// des ProtocollObjektes
					
					if ($ProtObjekt != NULL && is_subclass_of ( $ProtObjekt, "AProtocol" )) {
						
						if ($this->ProtocolTree !== NULL) {
							$ProtObjekt->setProtocollTree ( $this->ProtocolTree );
						}
						$this->ProtocolTree = $ProtObjekt;
						
						
						// aufruf der responce also auslesen des Protocolles
						$initPro = $ProtObjekt->init ( $ConfProt, $SendString );
					
						if ($initPro !== NULL) {
							 $this->setRequest ( $initPro);
						}
					
					}
					
					return NULL;
				
				} catch ( Exception $error ) {
					require_once 'citro/error/LogException.php';
					require_once 'citro/error/ErrorCodes.php';
					throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "PRO", 101 ), "Beim Setzen der Protocolle ist ein Fehler aufgetreten. - " . $error->getMessage (), E_ERROR, $error );
				}
			
			}
		}
	}
	
	private function setProtocolInlcude($Config) {
		
		$FileName = $Config->get ( Protocol::CONFIG_TEXT_PROTOCOLL_FILENAME, NULL );
		$ClassName = $Config->get ( Protocol::CONFIG_TEXT_PROTOCOLL_CLASSNAME, NULL );
		
		$FileAdress = "citro/protocol/" . $FileName;
		
		// pr�fen ob die datei wirklich exestiert und dan erst includen
		try {
			require_once $FileAdress;
		} catch ( Exception $e ) {
			require_once 'citro/error/LogException.php';
			require_once 'citro/error/ErrorCodes.php';
			throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "PRO", 201 ), "Das Protocol file was in der config definiert wurde, wurde nicht gefunden.", E_ERROR, $e );
		
		}
		
		if (class_exists ( $ClassName, false )) {
			
			// erstellen der Webservice Klasse
			$PClass = new $ClassName ();
			
			if (is_object ( $PClass )) {
				
				// Rückgabe der Klasse
				return $PClass;
			
			} else {
				require_once 'citro/error/LogException.php';
				require_once 'citro/error/ErrorCodes.php';
				throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "PRO", 203 ), "Die Protocollclasse konnte nicht inizialisert werden.", E_ERROR );
			}
		
		} else {
			require_once 'citro/error/LogException.php';
			require_once 'citro/error/ErrorCodes.php';
			throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "PRO", 202 ), "In dem Protocoll wurde die definierte classe aus der config nicht gefunden.", E_ERROR );
		}
	
	}

}

?>