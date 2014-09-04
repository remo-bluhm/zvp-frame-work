<?php

class Trash {
	
	const DB_TABLE_COLUMN_DELETED = "deleted";
	
	private $MinTypeValue = 3;
	private $MaxTypeValue = 20;
	
	private $_userId = NULL;
	private $_type = "no_set";
	
	/**
	 *
	 * @var TrashAbstract
	 */
	protected $_trash = NULL;
	
	function __construct(User $user, $type) {
		$this->_userId = $user->getId ();
		$this->_type = $type;
	
	}
	
	public function setDelete($Name, $data, $objektAdress, $objektName, $functionName) {
		
		$adress = array ();
		$adress ["adress"] = $objektAdress;
		$adress ["objname"] = $objektName;
		$adress ["objfunc"] = $functionName;
		
		require_once 'citro/db/sys/sys_trash.php';
		$trashTab = new sys_trash ();
		
		$tabData = array ();
		
		$tabData [sys_trash::SP_DATA_CREATE] = DBTable::DateTime ();
		$tabData [sys_trash::SP_USER_CREATE] = $this->_userId;
		$tabData [sys_trash::SP_TYPE] = $this->_type;
		$tabData [sys_trash::SP_ADRESS] = serialize ( $adress );
		$tabData [sys_trash::SP_DATA] = serialize ( $data );
		$tabData [sys_trash::SP_NAME] = $Name;
		
		$newTrash = $trashTab->createRow ( $tabData );
		
		$newTrash->save ();
	
	}
	
	public function Recycling($trashId) {
		
		$trashRow = $this->_getTrash ( $trashId );
		
		if ($trashRow !== NULL) {
			
			$data = unserialize ( $trashRow->offsetGet ( sys_trash::SP_DATA ) );
			$adress = unserialize ( $trashRow->offsetGet ( sys_trash::SP_ADRESS ) );
			
			$objAdr = $adress ["adress"];
			$objNam = $adress ["objname"];
			$objFun = $adress ["objfunc"];
			
			try {
				
				require_once $objAdr;
				$backData = forward_static_call_array ( array ($objNam, $objFun ), array ($data ) );
				
				if ($backData !== FALSE) {
					$trashRow->delete ();
				}
			
			} catch ( Exception $e ) {
				return FALSE;
			}
			
			return $backData;
		
		}
	
	}
	
	protected function _getTrash($TrashId) {
		
		require_once 'citro/db/sys/sys_trash.php';
		
		$trashTab = new sys_trash ();
		
		$select = $trashTab->select ();
		$select->where ( sys_trash::SP_ID . " = ?", $TrashId );
		
		$trash = $trashTab->fetchRow ( $select );
		
		return $trash;
	
	}
	
	protected function testType() {
		if (is_string ( $this->getType () ) && count ( $this->getType () ) >= $this->MinTypeValue && count ( $this->getType () ) <= $this->MaxTypeValue) {
			return TRUE;
		}
		return FALSE;
	}

}

?>