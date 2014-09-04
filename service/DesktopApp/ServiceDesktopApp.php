<?php

require_once 'citro/service-class/AService.php';

/**
 * Ist für die verwaltung der eigenen Daten
 *
 * @author Max Plank
 * @version 1.0
 *          @citro_isOn true
 *         
 */
class ServiceDesktopApp extends AService {
	
	/**
	 * Der User construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}
	

	/**
	 * Giebt alle Hasches zurück
	 * @return list
	 */
	public function ActionGetAllHasch() {
		
		require_once 'db/desktop_app/desktop_app_cach.php';
		$cachTab = new desktop_app_cach();
		
		require_once 'db/zimmer/zimmer.php';
		
		
		require_once 'citro/DBConnect.php';
		$DBCon = DBConnect::getConnect ();
	
	
		$select = $DBCon->select();
		// Hinzufügen einer FROM Bedingung
		$select->FROM(	array('z' => 'bt_zimmer'),
				array(	'zimmerid'			=> 'z.'.zimmer::SP_ID,
						'obj_nr'			=> 'z.'.zimmer::SP_OBJ_NR ,
						'zim_nr'			=> 'z.'.zimmer::SP_ZIM_NR ,
						'zim_visibil'		=> 'z.'.zimmer::SP_VISIBIL,
						'zim_sperre'		=> 'z.'.zimmer::SP_SPERR
				)
		);
		
		
		$select->joinLeft(	array(	'c' => $cachTab->getTableName()) ,
				'z.id = c.bt_zimmer_id',
				array(	'cach_id'		=>'c.'.desktop_app_cach::SP_ID,
						'cach_ges'		=>'c.'.desktop_app_cach::SP_GESAMT_CACH,
						'cach_data'		=>'c.'.desktop_app_cach::SP_ZIMMER_CACH,
						'cach_beleg'	=>'c.'.desktop_app_cach::SP_BELEGUNG_CACH,
						'cach_preise'	=>'c.'.desktop_app_cach::SP_PREISE_CACH
							
				)
		);
		$select->order(array('z.obj_nr'));
		$back = $select->query();
		
		return $back->fetchAll();
	
	}
	
	/**
	 * Ist für das Update der eigenen Daten
	 * 
	 * @param $groupId integer       	
	 * @param $userGuid integer       	
	 */
	public function ActionUpdateMyData($groupId, $userGuid) {
		
		$MyId = $this->_MainUser->getId ();
		
		$idList = $this->_MainUser->getMyGroups ( TRUE );
		
		if (in_array ( $groupId, $idList )) {
			
			require_once 'citro/db/sys/sys_user.php';
			$userTab = new sys_user ();
			$userSelect = $userTab->select ();
			$userSelect->where ( sys_user::SP_ID . " = ?", $userGuid );
			$userRow = $userTab->fetchRow ();
			$userRow->offsetSet ( sys_user::SP_GROUPID, $groupId );
		
		}
		
		return FALSE;
	}
	
	/**
	 * Giebt einen einzelnen User zurück
	 *
	 * @param $guid string Die Guid des Users als Strüng
	 * @return array boolean der rückgabe wert
	 * @citro_isOn true
	 */
	public function ActionGetToUpdate() {
		
		$MyId = $this->_MainUser->getId ();
		
		$userTab = new sys_user ();
		$userSelect = $userTab->select ();
		$userSelect->where ( sys_user::SP_ID . " = ?", $MyId );
		$userRow = $userTab->fetchRow ( $userSelect );
		$userId = $userRow->offsetGet ( "id" );
		
		$user = $userRow->toArray ();
		
		require_once 'citro/db/sys/sys_user.php';
		require_once 'citro/DBupdateRepository.php';
		
		$repos = new DBupdateRepository ( $this->_MainUser, new sys_user (), $userId );
		
		$hashKeyArray = $repos->getUpdateHashKey ();
		
		$result = array_merge ( ( array ) $user, ( array ) $hashKeyArray );
		
		return $result;
	
	}

}

?>