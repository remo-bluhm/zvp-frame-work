<?php

require_once 'citro/service-class/AService.php';

/**
 * Ist für die verwaltung der update Daten die ins Repository
 * eingeschriebenwurden
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceUpdateRepository extends AService {
	
	/**
	 * Der Rechemanagement construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}
	
	/**
	 * Giebt einen einzelnen User zurück
	 * es kann verändert werden Loginname , Password, Email, Name
	 *
	 * @param $guid string Die Guid des Users als Strüng
	 * @param $userDataArray array Die UserDaten der update hashKey kann auch hier mit enthalten und mus nicht gesonder angegeben werden
	 * @return boolean ob der Updatevorgan erfolgreich war
	 * @citro_isOn true
	 */
	public function ActionGetList() {
		
		require_once 'citro/db/sys/sys_updatereposetory.php';
		
		require_once 'citro/DBupdateRepository.php';
		
		$repos = new sys_updatereposetory ();
		$reposSelect = $repos->select ();
		
		$allRepos = $repos->fetchAll ( $reposSelect );
		
		return $allRepos->toArray ();
	}

}

?>