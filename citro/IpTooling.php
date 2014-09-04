<?php

// require_once 'citro/db/sys_request.php';
/**
 * class IpSperre
 *
 * Description for class IpSperre
 *
 * @author :
 *        
 */
class IpTooling {
	
	private static $MaxRequest = 5;
	private static $SperrTime = 3600;
	private static $NewIpSperreId = NULL;
	private static $SperrDaten = NULL;
	
	/**
	 * Abarbeitung ob Ip sperre besteht
	 *
	 * @param $ReqIp mixed
	 *       	 This is a description
	 * @param $UserValid mixed
	 *       	 This is a description
	 *       	
	 */
	public static function IPSperreCheck($IP = NULL, $UserValid = TRUE) {
		
		$MaxRequest = self::$MaxRequest;
		$SperrTime = self::$SperrTime;
		
		if ($IP == NULL) {
			$IP = self::getIp ();
		}
		if (! self::isIP ( $IP )) {
			require_once 'citro/error/LogException.php';
			require_once 'citro/error/ErrorCodes.php';
			
			throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "IPS", 105 ), "IP ist keine g�ltige Ip!", E_ERROR );
		}
		

		require_once 'db/sys/sys_ipsperre.php';
		$Tab = new sys_ipsperre ();
		$SperData = $Tab->getIpSperre ( $IP );
		
		// pr�fen ob es einen Eintrag mit der Datenbank vorhanden ist
		if ($SperData === false) {
			
			// falls user nicht valiede ist wird hier eine Ip Sperre f�r die ip
			// geschrieben
			if (! $UserValid) {
				
				$NewIpSperreId = $Tab->setNewIpSperre ( $IP );
				
				self::$NewIpSperreId = $NewIpSperreId;
				require_once 'citro/error/LogException.php';
				require_once 'citro/error/ErrorCodes.php';
				throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "IPS", 101 ), "User Ist Nicht Valiede deshalb ist die IpSperre ausgl�st wurden!", E_ERROR );
			
			}
		
		} else {
			
			self::$SperrDaten = $SperData;
			// es wurde ein eintrag in der Tabelle ip_sperre gefunden
			self::$NewIpSperreId = self::$SperrDaten [sys_ipsperre::SP_ID];
			// ist Z�hler1 gr��er als 20
			if ($SperData [sys_ipsperre::SP_COUNTER1] >= $MaxRequest) { // ab hier
			                                                            // ist
			                                                            // die Ip
			                                                            // gesperrt
				
				$IpSperre = true; // Der Counter ist x oder �ber x also gespert
				
				if (($SperData [sys_ipsperre::SP_TIME] + $SperrTime) < time ()) { // Pr�fen
				                                                                  // ob
				                                                                  // sperre
				                                                                  // abgelaufen
				                                                                  // ist
					
					$IpSperre = false; // Sperre ist Abgelaufen
					
					if ($UserValid) {
						// User ist verifiziert worden dadurch kann die IPLogg
						// gel�scht werden
						$Tab->setDeleteIpSperre ( $SperData [sys_ipsperre::SP_ID] );
					
					} else {
						// hier muss die ip hochgez�hlt werden den ip war schon
						// gelogt und User immer noch nicht verifiziert wurde
						$Tab->setCounterAdd ( $SperData [sys_ipsperre::SP_ID], 1, $SperData [sys_ipsperre::SP_COUNTER2] + 1 );
						require_once 'citro/error/LogException.php';
						require_once 'citro/error/ErrorCodes.php';
						throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "IPS", 102 ), "User Ist Nicht Valiede deshalb ist die IpSperre ausgl�st wurden!", E_ERROR );
					}
				} else {
					require_once 'citro/error/LogException.php';
					require_once 'citro/error/ErrorCodes.php';
					throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "IPS", 103 ), "F�r diese Ip Existiert eine IP sperre!", E_ERROR );
				}
			
			} else {
				
				// Die ip sperre ist noch nicht vorhanden da es noch uner X
				// Anfragen war
				
				// setze den User
				if ($UserValid) {
					$IpSperre = false;
					$Tab->setDeleteIpSperre ( $SperData [sys_ipsperre::SP_ID] );
				} else {
					// hier muss die ip hochgez�hlt werden den ip war schon
					// gelogt und User immer noch nicht verifiziert wurde
					
					$Tab->setCounterAdd ( $SperData [sys_ipsperre::SP_ID], $SperData [sys_ipsperre::SP_COUNTER1] + 1, $SperData [sys_ipsperre::SP_COUNTER2] + 1 );
					$IpSperre = true;
					throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "IPS", 104 ), "User Ist Nicht Valiede deshalb ist die IpSperre ausgl�st wurden!", E_ERROR );
				}
			}
		}
	
	}
	
	public static function getIp() {
		
		$IP = $_SERVER ['REMOTE_ADDR'];
		return ( integer ) $IP;
	}
	
	/**
	 * Testte nur darauf ob es syntax richtig ist der ip
	 *
	 * @param $ipnummer mixed
	 *       	 This is a description
	 * @return mixed This is the return value description
	 *        
	 */
	public static function isIP($ipnummer) {
		
		$ip = explode ( '.', $ipnummer );
		
		if (count ( $ip ) == 4) {
			
			if ($ip [0] <= 255 && $ip [1] <= 255 && $ip [2] <= 255 && $ip [3] <= 255) {
				
				return true;
			
			} else {
				
				return false;
			}
		
		} else {
			return false;
		}
	}

}

?>