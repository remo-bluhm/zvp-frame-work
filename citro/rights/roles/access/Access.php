<?php
require_once 'citro/rights/roles/Role.php';

/**
 * class User
 *
 * Dieses Objekt Representirt einen User 
 * mit diesen Objekt kann der SystemUser nicht verändert ober bearbeitet werden
 *
 * @author : Remo Bluhm
 *        
 */
//class User extends sys_user {
class Access  extends Role{
	
	/**
	 * @var Zend_Db_Table_Row Beinhaltet den Zeileneintrag des Users
	 */
	private $_userRow = NULL; // Zend_Db_Table_Row
	
	
	protected $_data_Id = NULL;
	protected $_data_GuId = NULL;
	protected $_data_GroupId = NULL;
	protected $_data_ContactId = NULL;
	protected $_data_IsAdmin = FALSE;
	
	protected $_data_LoginName = NULL;
	protected $_data_Password = NULL;
	protected $_data_AesKey = NULL;


	private $conf = NULL;
	
	const LOGINNAME = "loginname";
	const LOGINPASSW = "loginpassword";
	
	
	function __construct(Zend_Db_Table_Row_Abstract $dataRow, Zend_Config $config = NULL) {

		require_once 'db/sys/access/sys_access.php'; 
		$this->_userRow = $dataRow;
		
		
		$this->_data_Id = $dataRow->offsetGet(sys_access::SP_ID,NULL);
		$this->_data_GuId = $dataRow->offsetGet(sys_access::SP_GUID,NULL);
		$this->_data_GroupId = $dataRow->offsetGet(sys_access::SP_GROUPID,NULL);
		$this->_data_ContactId = $dataRow->offsetGet(sys_access::SP_CONTACT_ID,NULL);
		
		if($dataRow->offsetGet(sys_access::SP_ADMIN,NULL) == "1")$this->_data_IsAdmin = TRUE;
			
		$this->_data_LoginName = $dataRow->offsetGet(sys_access::SP_LOGINNAME,NULL);
		$this->_data_Password = $dataRow->offsetGet(sys_access::SP_PASSWORD,NULL);
		$this->_data_AesKey = $dataRow->offsetGet(sys_access::SP_AESKEY,NULL);
		
		
		// Setzen der Rollen Id
		if($this->_data_GuId !== NULL)
			parent::__construct("U_".$dataRow->offsetGet(sys_access::SP_ID));
			
		
	}
	
	
	
	/**
	 * Test Ob der User Valide ist also ob er in der DB gefunden wurde und ob er
	 * On geschaltet wurde
	 *
	 * @return bool User hat berechtigungen un wurde im system gefunden
	 *
	 */
	public function isUserValide() {
	
		if ($this->_userRow !== NULL) {
			return TRUE;
	
		}
		return FALSE;
	
	}
	
	
	/**
	 * Hirmit könen configurations einstellungen für alle UserObjekte gemacht
	 * werden
	 * 
	 * @param $conf Config       	
	 * @throws Exception
	 */
	public static function setConfig(Config $conf) {
		
		$config = $conf->get ( "user", NULL );
		
		if ($config === NULL) {
			
			throw new Exception ( 'In dem Configurations Objekt wurde kein "user" element gefunden ' );
		}
		self::$conf = $config;
	}
	

	/**
	 * Giebt die Id des Accesses zurück
	 * @return integer|NULL
	 */
	public function getId(){
		return (integer)$this->_data_Id;
	}
	
	
	
	/**
	 * Giebt die GuId des Users zurück 
	 * @return string|NULL
	 */
	public function getGuId(){
		return $this->_data_GuId;		
	}

	/**
	 * Giebt den Loginnamen zurück
	 * @return string
	 */
	public function getLoginName() {
		return $this->_data_LoginName;
	}
	
	
	/**
	 * Giebt das UserPassword zurück
	 * @return string
	 */
	public function getPassword() {
		return $this->_data_Password;
	}
	
	/**
	 * Der AesKey
	 * @return string:
	 */
	public function getAesKey() {
		return $this->_data_AesKey;
	}
	
	/**
	 * Gibt die Gruppen Id des Users zurück die er unterliegt
	 * @return mixed Die GrupppenId des Users als INT oder false falls es probleme gab     
	 */
	public function getGroupId() {
		return ( integer ) $this->_data_GroupId;
	}
	
	/**
	 * Giebt die Id des Contactes zurück der In der Datenbank als Primary Schlüssel definiert ist
	 * @return number|NULL
	 */
	public function getContactId(){
		return ( integer ) $this->_data_ContactId;
	}

	/**
	 * Prüfft ob der User ein Admin ist
	 * 
	 * @return boolean
	 */
	public function isAdmin() {

		return $this->_data_IsAdmin;
	}
	
	
	
	
	
	private $_myGroupRows = NULL;
	
	public function getMyGroupsIds($idAsArray = TRUE){
		
		if($this->_myGroupRows === NULL){
			$this->getMyGroups();
		}
		
		$idList = array ();
		
		/* @var $groupRow Zend_Db_Table_Row */
		foreach ( $this->_myGroupRows as $groupRow ) {
		
			require_once 'db/sys/access/groups/sys_access_groups.php';
			$idList [] = $groupRow->offsetGet ( sys_access_groups::SP_ID );
		}
		
		if ($idAsArray !== TRUE) {
			return implode(",", $idList);
		}
		
		return $idList;
			
	}
	
	
	
	/**
	 * Giebt alle meine Gruppen die ich besitze (Alle Ebenen)
	 * @return array
	 */
	public function getMyGroups() {
	
	
		require_once 'db/sys/access/groups/sys_access_groups.php';
		$group = new sys_access_groups ();
		$myGroup = $group->getTree($this->getGroupId());
		return $myGroup;
	
	}
	
	/**
	 * Giebt alle meine Gruppen die ich besitze als namensArray (Alle Ebenen)
	 * @return array
	 */
	public function getMyGroupsAsArray() {
		require_once 'db/sys/access/groups/sys_access_groups.php';
		$group = new sys_access_groups ();
		$myGroup = $group->getTreeName($this->getGroupId());
		return $myGroup;
	}
	
	
	
	/**
	 * Giebt alle meine Gruppen die ich besitze als namensArray (Alle Ebenen)
	 * @return array
	 */
	public function getMyParentGroups() {
		require_once 'db/sys/access/groups/sys_access_groups.php';
		$group = new sys_access_groups ();
		$myGroup = $group->getParent($this->getGroupId());
		return $myGroup;
	}
	
	


	
	


}

?>
