<?php

require_once 'citro/service-class/AService.php';

/**
 * die Rechte management klasse
 * Ist der Standart Service für das Management der Services
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceTrash extends AService {
	
	/**
	 * Der Rechemanagement construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}
	
	/**
	 * Giebt den Papierkorb des Users zurück
	 * 
	 * @param bool $UpSorting        	
	 */
	public function ActionGetUserTrash($UpSorting = TRUE) {
		
		$Sorting = "DESC";
		if ($UpSorting !== TRUE) {
			$Sorting = "ASC";
		}
		
		require_once 'citro/db/sys/sys_trash.php';
		
		$Trash = new sys_trash ();
		
		$select = $Trash->select ();
		
		$select->where ( sys_trash::SP_USER_CREATE, $this->_MainUser->getId () );
		$select->order ( sys_trash::SP_DATA_CREATE . " DESC" );
		
		$allTrash = $Trash->fetchAll ( $select );
		
		return $allTrash->toArray ();
	
	}
	
	/**
	 * Stellt die ausgewählten Daten aus dem Papierkorb wieder her
	 * 
	 * @param integer $TrashId        	
	 */
	public function ActionRecycling($TrashId) {
		
		require_once 'citro/Trash.php';
		
		$Trash = new Trash ( $this->_MainUser, "GRUPPE" );
		$backData = $Trash->Recycling ( $TrashId );
		
		return $backData;
	}

}

?>