<?php


/**
 * class zvp_zimmer
 *
 * Description for class zvp_zimmer
 *
 * @author:
*/
class amenities_version  extends DBTable {
	
	protected $_TableName = "amenities_version";
	

	
	
	public function deleteRootElement($rootKey){
		$db = $this->getAdapter();
	
		$delWhereA = array();
		$delWhereA[] = $db->quoteInto("rootkey=?", $rootKey);
	
		// Lösche Alle Elemente
		$zeilen = $db->delete($this->getTableName(), $delWhereA);
		return $zeilen;
	}
	
	
	


}

?>