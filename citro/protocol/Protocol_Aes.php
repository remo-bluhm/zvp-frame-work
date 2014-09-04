<?php

require_once 'citro/protocol/AProtocol.php';

/**
 * class Protocol_Aes
 *
 * Description for class Protocol_Aes
 *
 * @author :
 *        
 */
class Protocol_Aes extends AProtocol {
	
	private $RequestIvKey;
	private $RequestUserCode;
	private $User = null;
	
	// const PROT_LANG = 74;
	private static $keyLang = 24;
	private static $aesLang = 16;
	
	/**
	 * Protocol_Aes constructor
	 *
	 * @param       	
	 *
	 *
	 */
	function Protocol_Aes() {
	
	}
	public function getProtLang() {
		
		require_once 'citro/GuidCreate.php';
		
		$protTrenner = 1;
		$TrennHaschUserGuid = 1;
		
		$GesLang = strlen ( $this->getProtocolName () ) + $protTrenner;
		$GesLang += self::$aesLang + $TrennHaschUserGuid + GuidCreate::GUID_LANG;
		
		
		return $GesLang;
	}
	
	public function init($Conf, $SendString) {
		$this->ProtokollName = "aeskey";
		
		if ($Conf != NULL)
			$this->Config = $Conf;
		
		$ProtLang = $this->getProtLang ();
	
		$ProtokollString = substr ( $SendString, strlen ( $this->getProtocolName () ) + 1, $ProtLang - strlen ( $this->getProtocolName () ) - 1 );

		$this->RequestIvKey = substr ( $ProtokollString, 0, self::$aesLang );
		require_once 'citro/GuidCreate.php';
		$this->RequestUserCode = substr ( $ProtokollString, self::$aesLang + 1, GuidCreate::GUID_LANG );
		
		//$sendText = substr ( $SendString, $ProtLang );
		$Nutzdaten = substr ( $SendString, $ProtLang );

		
// 		require_once 'citro/rightsmanagement/AccessInstances.php';
// 		$access = AccessInstances::getInstance($this->RequestUserCode);
		
	
		$access = self::getAccess($this->RequestUserCode);
		
		
		//if ($User->isUserValide ()) {
		if ($access->isUserValide ()) {
			
			$Text = Protocol_Aes::aesDecrypt ( $Nutzdaten, $access->getAesKey (), $this->RequestIvKey, 2 );
		
			if ($Text === FALSE) {
				require_once 'citro/error/LogException.php';
				require_once 'citro/error/ErrorCodes.php';
				throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "PRO", 401 ), "Der AES Key ist Fehlerhaft", E_ERROR );
			}
			$this->Data = $Text;
			return $Text;
		
		} else {
			
			require_once 'citro/error/LogException.php';
			require_once 'citro/error/ErrorCodes.php';
			throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "PRO", 402 ), "Anfrag Abbruch da User im AES Protocoll nicht ermittelt werden konnte", E_ERROR );
		}
		
		
		return $Nutzdaten;
	
	}
	
	protected function beforeResponce($Data) {
		
// 		require_once 'citro/rightsmanagement/AccessInstances.php';
// 		$access = AccessInstances::getInstance($this->RequestUserCode);
		
		$access = $this->getAccess($this->RequestUserCode);
		
		$UserCode = $access->getGuId();
		$zv = self::newAesKey ();
		$text = Protocol_Aes::aesEncrypt ( $Data, $access->getAesKey (), $zv, 2 );
		
		$sendString = "aeskey:" . $zv . "-" . $UserCode . $text;
		
		return $sendString;
	}
	
	public static function newAesKey() {
		srand ( ( double ) microtime () * 1000000 );
		$zufall = rand ();
		$zufallsstring = substr ( md5 ( $zufall ), 0, 16 );
		return $zufallsstring;
	}
	
	// ####################################################
	// ######### AES Abarbeitung ##########################
	// ####################################################
	
	// Data representation
	public static $DATA_AS_IS = 0;
	public static $DATA_AS_BASE64 = 1;
	public static $DATA_AS_HEX = 2;
	
	/**
	 * Adds pkcs5 padding
	 *
	 * @return Given text with pkcs5 padding
	 * @param $data string
	 *       	 String to pad
	 * @param $blocksize integer
	 *       	 Blocksize used by encryption
	 */
	private static function pkcs5Pad($data, $blocksize) {
		
		$pad = $blocksize - (strlen ( $data ) % $blocksize);
		$returnValue = $data . str_repeat ( chr ( $pad ), $pad );
		
		return $returnValue;
	}
	
	/**
	 * Removes padding
	 *
	 * @return Given text with removed padding characters
	 * @param $data string
	 *       	 String to unpad
	 */
	private static function pkcs5Unpad($data) {
		
		$pad = ord ( $data {strlen ( $data ) - 1} );
		if ($pad > strlen ( $data ))
			return false;
		if (strspn ( $data, chr ( $pad ), strlen ( $data ) - $pad ) != $pad)
			return false;
		
		return substr ( $data, 0, - 1 * $pad );
	}
	
	/**
	 * Encrypts a string with the Advanced Encryption Standard.
	 *
	 * The used algorythm (cipher) is MCRYPT_RIJNDAEL_128 and the mode is 'cbc'
	 * (cipher block chaining).
	 *
	 * @return Encrypted text as hexadecimal representation
	 * @param $data string
	 *       	 String to encrypt
	 * @param $key string
	 *       	 Key
	 * @param $iv string
	 *       	 Initialization vector (IV) - 16 char
	 * @param $dataAs integer
	 *       	 [optional]
	 *       	 Encode data after encryption as (CryptUtility::$DATA_AS_*) -
	 *       	 Default CryptUtility::$DATA_AS_IS
	 */
	private static function aesEncrypt($data, $key, $iv, $dataAs = 0) {
		
		$size = mcrypt_get_block_size ( MCRYPT_RIJNDAEL_128, 'cbc' );
		$cipher = mcrypt_module_open ( MCRYPT_RIJNDAEL_128, '', 'cbc', '' );
		
		// Add padding to String
		$data = Protocol_Aes::pkcs5Pad ( $data, $size );
		$length = strlen ( $data );
		
		mcrypt_generic_init ( $cipher, $key, $iv );
		
		$data = mcrypt_generic ( $cipher, $data );
		
		if ($dataAs == Protocol_Aes::$DATA_AS_HEX) {
			$data = bin2hex ( $data );
		} else if ($dataAs == Protocol_Aes::$DATA_AS_BASE64) {
			$data = base64_encode ( $data );
		}
		
		mcrypt_generic_deinit ( $cipher );
		
		return $data;
	}
	
	/**
	 * Decrypts a string with the Advanced Encryption Standard.
	 *
	 * The used algorythm (cipher) is MCRYPT_RIJNDAEL_128 and the mode is 'cbc'
	 * (cipher block chaining).
	 *
	 * @return Decrypted text
	 * @param $data string
	 *       	 String to decrypt as hexadecimal representation
	 * @param $key string
	 *       	 Key
	 * @param $iv string
	 *       	 Initialization vector (IV) - 16 char
	 * @param $dataAs integer
	 *       	 [optional]
	 *       	 Decode data before decryption as (CryptUtility::$DATA_AS_*) -
	 *       	 Default CryptUtility::$DATA_AS_IS
	 */
	private static function aesDecrypt($data, $key, $iv, $dataAs = 0) {
		
		$size = mcrypt_get_block_size ( MCRYPT_RIJNDAEL_128, 'cbc' );
		$cipher = mcrypt_module_open ( MCRYPT_RIJNDAEL_128, '', 'cbc', '' );
		
		mcrypt_generic_init ( $cipher, $key, $iv );
		
		if ($dataAs == Protocol_Aes::$DATA_AS_HEX) {
			// pack() is used to convert hex string to binary
			$data = pack ( 'H*', $data );
		} else if ($dataAs == Protocol_Aes::$DATA_AS_BASE64) {
			$data = base64_decode ( $data );
		}
		
		$data = mdecrypt_generic ( $cipher, $data );
		mcrypt_generic_deinit ( $cipher );
		
		return Protocol_Aes::pkcs5Unpad ( $data );
	}

}

?>