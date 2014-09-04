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
class Protocol_sha1hasch extends AProtocol {
	
	private $RequestHaschValue;
	private $RequestUserCode;
	private $User = null;
	
	private static $haschLang = 40;
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
		
		$this->ProtokollName = "sha1hasch";
		
		if ($Conf != NULL)
			$this->Config = $Conf;
		
		$ProtLang = $this->getProtLang ();
		$ProtokollString = substr ( $SendString, strlen ( $this->getProtocolName () ) + 1, $ProtLang - strlen ( $this->getProtocolName () ) - 1 );
		
		$this->RequestHaschValue = substr ( $ProtokollString, 0, self::$haschLang );
		
		require_once 'citro/GuidCreate.php';
		$this->RequestUserCode = substr ( $ProtokollString, self::$haschLang + 1, GuidCreate::GUID_LANG );
		
		$Nutzdaten = substr ( $SendString, $ProtLang );
		$this->Data = $Nutzdaten;
		

		
		$access = self::getAccess($this->RequestUserCode);
		
		if ($access->isUserValide ()) {
			
			// todo: Bei Haschfehler also bei nicht gleichen haschwerten script
			// abbrechen
			$sha1Nuztdaten = sha1 ( $access->getPassword () . $Nutzdaten );
			
			// Testen ob der übergebene Haschwert gleich der Nutzerdaten plus
			// Passwort im md5() ergibt
			if ($this->RequestHaschValue == $sha1Nuztdaten) {
				
				return $Nutzdaten;
			
			} else {
				require_once 'citro/error/LogException.php';
				require_once 'citro/error/ErrorCodes.php';
				throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "PRO", 501 ), "Anfrag Abbruch da Der HachTest im Protocol SHA1Hasch Fehlgeschlagen ist", E_ERROR );
			
			}
		} else {
			require_once 'citro/error/LogException.php';
			require_once 'citro/error/ErrorCodes.php';
			throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "PRO", 502 ), "Anfrag Abbruch da User im Sha1hasch Protocoll nicht ermittelt werden konnte", E_ERROR );
		}
		
		return $Nutzdaten;
	
	}
	
	protected function beforeResponce($Data) {
			
		$access = $this->getAccess($this->RequestUserCode);
		
		$SemdMd5Value = sha1( $access->getPassword () . $Data );		
		$sendString = "sha1hasch:" . $SemdMd5Value . "-" . $access->getGuId() . $Data;
		// $sendString =
		// "md5hasch:fad5183cb130c580b3bce07aa6ca72d0-".$UserCode.$ResponceDaten;
		
		return $sendString;
	}

}

?>