<?php

require_once 'citro/protocol/AProtocol.php';

/**
 * class Protocol_md5hasch
 *
 * Description for class Protocol_md5hasch
 *
 * @author :
 *        
 */
class Protocol_md5hasch extends AProtocol {
	
	private $RequestHaschValue;
	private $RequestUserCode;
	private $User = null;
	
	private static $haschLang = 32;
	/**
	 * Protocol_md5hasch constructor
	 *
	 * @param       	
	 *
	 *
	 */
	function Protocol_md5hasch() {
	
	}
	
	public function getProtLang() {
		
		require_once 'citro/GuidCreate.php';
		
		$protTrenner = 1;
		$TrennHaschUserGuid = 1;
		$GesLang = strlen ( $this->getProtocolName () ) + $protTrenner;
		$GesLang += self::$haschLang + $TrennHaschUserGuid + GuidCreate::GUID_LANG;
		
		return $GesLang;
	}
	
	public function init($Conf, $SendString) {
		
		$this->ProtokollName = "md5hasch";
		
		if ($Conf != NULL)
			$this->Config = $Conf;
		
		$ProtLang = $this->getProtLang ();
		$ProtokollString = substr ( $SendString, strlen ( $this->getProtocolName () ) + 1, $ProtLang - strlen ( $this->getProtocolName () ) - 1 );
		
		$this->RequestHaschValue = substr ( $ProtokollString, 0, self::$haschLang );
		require_once 'citro/GuidCreate.php';
		$this->RequestUserCode = substr ( $ProtokollString, self::$haschLang + 1, GuidCreate::GUID_LANG );
		
		$Nutzdaten = substr ( $SendString, $ProtLang );
		$this->Data = $Nutzdaten;
		
		require_once 'citro/UserInstanceMain.php';
		$User = UserInstanceMain::getMainUser ( $this->RequestUserCode );
		
		if ($User->isUserValide ()) {
			
			// todo: Bei Haschfehler also bei nicht gleichen haschwerten script
			// abbrechen
			$md5Nuztdaten = md5 ( $User->getPassword () . $Nutzdaten );
			
			// Testen ob der übergebene Haschwert gleich der Nutzerdaten plus
			// Passwort im md5() ergibt
			if ($this->RequestHaschValue == $md5Nuztdaten) {
				
				return $Nutzdaten;
			
			} else {
				require_once 'citro/error/LogException.php';
				require_once 'citro/error/ErrorCodes.php';
				throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "PRO", 501 ), "Anfrag Abbruch da Der HachTest im Protocol md5Hach Fehlgeschlagen ist", E_ERROR );
			
			}
		} else {
			require_once 'citro/error/LogException.php';
			require_once 'citro/error/ErrorCodes.php';
			throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "PRO", 502 ), "Anfrag Abbruch da User im Md5Hach Protocoll ermittelt werden konnte", E_ERROR );
		}
		
		return $Nutzdaten;
	
	}
	
	protected function beforeRequest($Data) {
		$User = User::getInstance ( $this->RequestUserCode );
		$SemdMd5Value = md5 ( $User->getPassword () . $Data );
		
		// md5hasch:fad5183cb130c580b3bce07aa6ca72d0-asdfghjklp1234567890yxcvbnmklo1234567890wertzuiophjson:
		// "md5hasch:".$SemdMd5Value."-".$UserCode.
		$UserCode = $User->getUserCode ();
		$sendString = "md5hasch:" . $SemdMd5Value . "-" . $UserCode . $Data;
		// $sendString =
		// "md5hasch:fad5183cb130c580b3bce07aa6ca72d0-".$UserCode.$ResponceDaten;
		
		return $sendString;
	}

}

?>