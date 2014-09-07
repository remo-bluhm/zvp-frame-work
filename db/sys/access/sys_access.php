<?php

require_once 'citro/DBTable.php';
/**
 * class sys_access
 *
 * Description for class sys_access
 *
 * @author :
 *        
 */
class sys_access extends DBTable {
	

	protected $_primary = 'id';
	

	const SP_ID = "id";
	const SP_GUID = "guid";
	
	const SP_CONTACT_ID = "contacts_id";
	const SP_GROUPID = "sys_access_groups_id";
	
	const SP_ACCESS_CREATE = "access_create";
	const SP_ACCESS_EDIT = "access_edit";
	
	const SP_DATA_EDIT = "date_edit";
	const SP_DATA_CREATE = "date_create";
		
	const SP_EMAIL = "email";

	const SP_LOGINNAME = "loginname";
	const SP_PASSWORD = "password";
	
	const SP_ADMIN = "admin";
	
	const SP_DELETE = "deleted";
	const SP_VISIBIL = "visibil";
	
	const SP_AESKEY = "aeskey";
	const SP_PASSWORDBLANK = "passwordblank";
	


	const VAR_AESKEY_LANG = 16;
	const VAR_PASS_LANG = 8;
	
	const LOGINNAME_MIN = 5;
	const LOGINNAME_MAX = 100;
	
	const PASSWORD_MIN = 8;
	const PASSWORD_MAX = 45;
	
	function __construct($config = array()) {
		parent::__construct ( $config );
	}
	
	
	
	
	/**
	 * Giebt ein Accessrowobjekt zurück
	 *
	 * @param string $guid die GuId des Accesses
	 * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
	 */
	public function getAccessWithGuid($guid){
	
		$guid = trim($guid);
		
		// Test des Guid strings auf deren syntax
		require_once 'citro/GuidCreate.php';
		if (!GuidCreate::isProbablyGUID ( $guid )) {
			return NULL;
// 			// Das Sicherheitsmerkmal (Usercode test auf min und max wert) hat Angeschlagen
// 			// Wird nur gelogt bricht aber nicht ab
// 			require_once 'citro/error/LogException.php';
// 			require_once 'citro/error/ErrorCodes.php';
			
// 			throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "USR", 314 ), "Der Usercode hat nicht die korekte länge!", E_ERROR );
		}
		
		$Select = $this->select ();
		$Select->where ( self::SP_GUID . '= ?', $guid );
		$Select->where ( self::SP_DELETE . '= ?', 0 );
		$Select->where ( self::SP_VISIBIL . '= ?', 1 );
			
		$row = $this->fetchRow ( $Select );
	
		return $row;
	}
	
	/**
	 * Giebt ein Accessrowobjekt zurück
	 * 
	 * @param $Username string       	
	 * @param $Userpass string       	
	 * @param $PassConvertSha1 bool falls die verschlüsslung nicht angewendet werden soll
	 * @return Zend_Db_Table_Row_Abstract NULL
	 */
	public function getAccessWithLoginData($Username, $Userpass, $PassConvertSha1 = TRUE) {
		
		
	
		$Username = $this->mysql_prep ( $Username );
		$Userpass = $this->mysql_prep ( $Userpass );

		
		// wurde am 27.6.12 eingeführt da in der Datenbank das Password nur verschlüsselt vorliegt
		if ($PassConvertSha1 === TRUE) {
		
			$Userpass = self::passwordCreateForDB ( $Userpass );
		}

		$select = $this->select();

		$select->where ( self::SP_LOGINNAME . " = ?", $Username );
	
		$select->where ( self::SP_PASSWORD . " = ?", $Userpass );
		
		$select->where ( self::SP_VISIBIL . " = ?", 1 );
		$select->where ( self::SP_DELETE . " = ?", 0 );
	
		$row = $this->fetchRow ( $select );
		
		return $row;
	
	}
	
	public function testExistLoginName($loginName) {
		
		$select = $this->select ();
		$select->where ( self::SP_LOGINNAME . " = ?", $this->mysql_prep ( $loginName ) );
		
		$rowSet = $this->fetchAll ( $select );
		
		if ($rowSet->count () > 0) {
			return TRUE;
		}
		return FALSE;
	
	}
	

	
	public function insertAdmin($loginname,$password,$data = array()){
		
		require_once 'citro/GuidCreate.php';
		$data["guid"] = GuidCreate::generateGUID();
		$data["aeskey"] = sys_access::aesKeyCreate();
		$data["date_create"] = self::DateTime();
		$data["date_edit"] = self::DateTime();
		
		$data["loginname"] = $loginname;
		$data["password"] = $password;
		$data["admin"] = 1;
		if(!isset($data["visibil"]))$data["visibil"] = 1;

		
		$primaryId = parent::insert($data);
		return $primaryId;
		
	}
	

	/**
	 * Schreibt einen Access ein
	 * @param integer 	$accessId Der Besitzer(Owner)
	 * @param integer 	$contactid 
	 * @param integer 	$groupid 
	 * @param string	$loginname 
	 * @param string	$password_blank 
	 * @param array		$data 
	 * @return Zend_Db_Table_Row_Abstract 
	 */
	public function createAccessRow($accessId, $contactid, $groupid, $loginname, $password_blank, $data){
	

		$data[self::SP_CONTACT_ID] = $contactid;
		$data[self::SP_GROUPID] = $groupid;
		$data[self::SP_ACCESS_CREATE] = $accessId;
		$data[self::SP_ACCESS_EDIT] = $accessId;
		$data[self::SP_EMAIL] = $loginname;
		$data[self::SP_LOGINNAME] = $loginname;
		$data[self::SP_PASSWORD] = self::passwordCreateForDB($password_blank);
		if(!isset($data[self::SP_VISIBIL]))$data[self::SP_VISIBIL] = 1;
		
		require_once 'citro/GuidCreate.php';
		$data[self::SP_GUID] = GuidCreate::generateGUID();
		$data[self::SP_AESKEY] = self::createAesKey();
		$data[self::SP_DATA_CREATE] = self::DateTime();
		$data[self::SP_DATA_EDIT] = self::DateTime();
		$data[self::SP_ADMIN] = 0;
		$data[self::SP_DELETE] = 0;
		
		$accessRow = $this->createRow($data);
		return $accessRow;
	}
	
// 	public function testLoginNameMinMax($loginname) {
		
// 		if (strlen ( $loginname ) > self::LOGINNAME_MIN && strlen ( $loginname ) < self::LOGINNAME_MAX)
// 			return TRUE;
// 		return FALSE;
	
// 	}
	
	/**
	 * Testet den Loginnamen
	 * @param string $value Der Loginname
	 * @return string|Boolen Im Fehlerfall FALSE 
	 */
	public static function testLoginname($value){
	
		if(is_string($value) && strlen($value) < 100 &&  strlen($value) > 3){
			return $value;
		}
		return FALSE;
	}
	
	/**
	 * Testen auf das Password
	 * @param string $value Das Password
	 * @param bool $isBlank falls schon verschlüsselt dann False
	 * @return string|boolean
	 */
	public static function testPassword($value,$isBlank = TRUE){
		
	
		
		if($isBlank){
			if(is_string($value) && strlen($value) < self::PASSWORD_MAX &&  strlen($value) >= self::PASSWORD_MIN){
				return TRUE;
			}
			return FALSE;
		}else{
			//prüfen der verschlüsselten länge
			if(is_string($value) && strlen($value) == 40 ){
				return TRUE;
			}
			return FALSE;
		}


	}
	
	
	/**
	 * Verschlüsseln des blanken Passwortes für die Speicherung in der Datenbank
	 * @param string $passwordBlank
	 * @return string Das Verschlüsselte password mit 40 Zeichen
	 */
	public static function passwordCreateForDB($passwordBlank) {
		
		$passwordBlank = self::mysql_prep ( $passwordBlank );
		
		$PasswordSha1 = sha1 ( $passwordBlank );
		
		return $PasswordSha1;
	}
	
	/**
	 * Erstellt einen AesKey für die Datenbank
	 * @return string Der AesKey mit 16 Zeichen länge 
	 */
	public static function createAesKey() {
		
		srand ( ( double ) microtime () * 1000000 );
		$zufall = rand ();
		$zufallsstring = substr ( md5 ( $zufall ), 0, self::VAR_AESKEY_LANG );
		return $zufallsstring;
	}

}

?>