<?php
/**
 * Der Instalationsmechanismus der Application
 *
 * @author Max Plank
 * @version 1.0
 *
 */
class Install{

	
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	private $_db = NULL;
	
		

	
	/**
	 * Der Rechemanagement construktor
	 */
	function __construct($configPath) {


		
		
		//Inizialisieren der Config
		require_once 'citro/Config.php';
		Config::setConfigAsXml($configPath);

		
		// Inizialiseren des DBConnects
		require_once 'citro/DBConnect.php';
		DBConnect::Connect( Config::getInstanceMainKnot( DBConnect::CONF_MAIN_KNOT ) );
		
		

		$this->_db = DBConnect::getConnect();
		
	

	}
	
	

	
	public function getAdmins(){
		if($this->_db !== NULL){
		
			require_once 'db/sys/access/sys_access.php';
			$sel = $this->_db->select();
			$sel->from(array("a"=>"sys_access"), "*" );
			$sel->joinLeft(array("c"=>"contacts"),"a.contacts_id = c.id", array ( 'first_name'=>'c.first_name','last_name'=>'c.last_name'));
			

			
			$sel->where(	sys_access::SP_ADMIN."=?",		1 );
			$sel->where(	"'".sys_access::SP_DELETE."' = ?",	0 );
				
			$admins = $this->_db->fetchAll($sel);
				
			return $admins;
				
		}else{
			return array();
		}
		
	}
	
	
	public function setAdmin($data){
		
		require_once 'db/sys/access/sys_access.php';
		$loginname = sys_access::testLoginname($data["install_mail"]);
		$password = sys_access::testPassword($data["install_pass"]);

		require_once 'db/contact/contacts.php';
		$firstname = contacts::testFirstName($data["install_prename"]);
		$lastname = contacts::testLastName($data["install_name"]);
		
		require 'db/sys/access/groups/sys_access_groups.php';
		
		if ( $loginname !== FALSE && $password !== FALSE && $firstname !== FALSE && $lastname !== FALSE  ){

			// Löscht alle Tabellen und erstellt sie neu aus der Datei
			$sqlInstallPath = SERVICE_PATH.DIRECTORY_SEPARATOR."install".DIRECTORY_SEPARATOR."db_table.sql";
			$this->_setTables($sqlInstallPath,Config::getInstanceMainKnot( DBConnect::CONF_MAIN_KNOT ));
			
			
			
			$access = new sys_access();
			$accessId = $access->insertAdmin($loginname,$password);
						
			$group = new sys_access_groups();
			$adminGroupId = $group->setRoot("Administratoren","Die Administratoren gruppe auf oberster Ebene",$accessId);
		
			$dataCont = array();
			$dataCont[contacts::SP_FIRST_NAME] = $firstname;
			$dataCont[contacts::SP_LAST_NAME] = $lastname;
			
			$contacts = new contacts();
			$contactsId = $contacts->insert($dataCont,$accessId);
			
			$dataUpAccess = array();
			$dataUpAccess[sys_access::SP_GROUPID] = $adminGroupId;
			$dataUpAccess[sys_access::SP_CONTACT_ID] = $contactsId;
			$dataUpAccess[sys_access::SP_USER_CREATE] = $accessId;
			$dataUpAccess[sys_access::SP_USER_EDIT] = $accessId;
			$access->update($dataUpAccess, "id = '".$accessId."'");
			
			
			

		}
		return False;
	
	}
	
	
	private function _setTables($sqlInstallPath,Zend_Config $dbConf){
		
		$buffer = "";
		$handle = fopen ($sqlInstallPath , "r");
		while (!feof($handle)) {
		
		   $buffer = $buffer.fgets($handle);
		}
		fclose ($handle);

		$db = Zend_Db::factory ($dbConf );
		$db->getConnection ();
		$statmant = $db->query($buffer);

	}

	
	
	
	
	
	
	
	
	
	
	


}













