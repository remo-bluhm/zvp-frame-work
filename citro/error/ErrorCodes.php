<?php

class ErrorCodes {
	
	/**
	 * Stellt den Applications Key f�r den Systembereich dar
	 *
	 * @var string
	 */
	const APP_SYS = "SYS";
	/**
	 * Stellt den Applications Key f�r den Servicebereich dar
	 *
	 * @var string
	 */
	const APP_SER = "SER";
	/**
	 * Stellt den Applications Key f�r die PHP Fehler dar
	 *
	 * @var string
	 */
	const APP_PHP = "PHP";
	/**
	 * Stellt den Applications Key f�r den Datenbankbereich dar
	 *
	 * @var string
	 */
	const APP_ADB = "ADB";
	
	/**
	 * Stellt den Rechtebereich dar
	 *
	 * @var string
	 */
	const APP_ACL = "ACL";
	
	private $application = "NOT";
	private $area = "SET";
	private $errorcode = 101;
	
	/**
	 * Kann zum schnellen Inizialisieren des ErrorCodes genutzt werden bei
	 * Fehlern wirft diese aber eine Exception
	 *
	 * @param string $application  Markiert den Wirkungsbereich der Application. Es sollten die constanten APP_... der Klasse genutzt werden.
	 * @param string $area  Markiert ein Frei gewählten bereich innerhalb des Applichations bereiches. Kann frei gewählt werden.
	 * @param string $errorcode Bezeichnet den konkreten Fehler innerhalb der Area. kann frei gewählt werden. Der Inhalt mus zwichen 0 und 999 sein
	 * @throws Exception
	 */
	function ErrorCodes($application, $area, $errorcode) {
		
		if (! $this->setErrorCodes ( $application, $area, $errorcode )) {
			
			// throw new Exception("Dem Construcktor der Klasse ErrorCodes
			// wurden nicht valiede werte �bergeben", E_NOTICE);
		}
	
	}
	
	/**
	 * Setzen der Werte des ErrorCodes bei Falschen werten wird False
	 * zur�ckgegeben und die werte werden nicht gesetzt.
	 *
	 * @param $application string
	 *       	 Markiert den Wirkungsbereich der Application. Es sollten die
	 *       	 constanten APP_... der Klasse genutzt werden.
	 * @param $area string
	 *       	 Markiert ein Frei gew�hlten bereich innerhalb des
	 *       	 Applichations bereiches. Kann frei gew�hlt werden.
	 * @param $errorcode int
	 *       	 Bezeichnet den konkreten Fehler innerhalb der Area. kann frei
	 *       	 gew�hlt werden. Der Inhalt mus zwichen 0 und 999 sein
	 * @return boolean Ob Falsche werte �bergeben wurden.
	 */
	public function setErrorCodes($application, $area, $errorcode) {
		
		$App = $this->getClean ( $application );
		if ($this->isApplicatio ( $application ) === FALSE || $App === FALSE) {
			
			return FALSE;
		}
		
		$Are = $this->getClean ( $area );
		if ($this->isArea ( $area ) === FALSE || $Are === FALSE) {
			
			return FALSE;
		}
		
		$EC = $this->getClean ( $errorcode );
		if ($this->isErrorCode ( $errorcode ) === FALSE || $EC === FALSE) {
			
			return FALSE;
		}
		
		$this->application = $App;
		$this->area = $Are;
		$this->errorcode = str_pad ( ( string ) $EC, 3, "0", STR_PAD_LEFT );
		
		return TRUE;
	
	}
	
	/**
	 * Ist f�r das Pr�fen auf Alfanumeriche zeichen und umwandeln in
	 * Gro�buchstaben verantwortlich
	 *
	 * @param $Value unknown_type
	 *       	 Der zu pr�fende Wert
	 * @return string boolean bereinigten Wert falls keine alfanumerichen
	 *         Zeichen vorhanden waren wird FALSE zur�ckgegeben
	 */
	private function getClean($Value) {
		
		// pr�fen auf alfanumeriche zeichen(a-z und A-Z und 0-9)
		if (ctype_alnum ( ( string ) $Value )) {
			
			$Value = strtoupper ( $Value );
			return $Value;
		}
		return FALSE;
	
	}
	
	/**
	 * Giebt den generierten ErrorCode zur�ck zb( SYS-E10-101 )
	 *
	 * @return string
	 */
	public function getErrorCodeStr() {
		return $this->getApplication () . "-" . $this->getArea () . "-" . $this->getErrorCode ();
	}
	
	/**
	 * Testet auf die Applications shortcuts.
	 *
	 * @param $shortcut string       	
	 * @return boolean
	 */
	public function isApplicatio($shortcut) {
		if (is_string ( $shortcut ) && (strlen ( $shortcut ) == 3)) {
			return TRUE;
		} else {
			return FALSE;
		}
	
	}
	
	/**
	 * Testet auf die Area shortcuts
	 *
	 * @param $shortcut string       	
	 * @return boolean
	 */
	public function isArea($shortcut) {
		if (is_string ( $shortcut ) && (strlen ( $shortcut ) == 3)) {
			return TRUE;
		} else {
			return FALSE;
		}
	
	}
	
	/**
	 * Testet auf die ErrorCode werte
	 *
	 * @param $number integer       	
	 * @return boolean
	 */
	public function isErrorCode($number) {
		if (is_numeric ( $number ) && ($number >= 0) && ($number < 1000)) {
			return TRUE;
		} else {
			
			return FALSE;
		}
	
	}
	
	/**
	 * Giebt den Applications Key zur�ck
	 *
	 * @return string
	 */
	public function getApplication() {
		return $this->application;
	}
	
	/**
	 * Giebt den Area Key zur�ck
	 *
	 * @return string
	 */
	public function getArea() {
		return $this->area;
	}
	
	/**
	 * Giebt der ErrorCode Key zur�ck
	 *
	 * @return number
	 */
	public function getErrorCode() {
		return $this->errorcode;
	}

}

?>