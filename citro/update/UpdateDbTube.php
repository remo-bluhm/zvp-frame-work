<?php

require_once 'citro/update/UpdateDb.php';

class UpdateDbTube extends UpdateDb {
	
	
	private $_colummArray = NULL;
	
	protected $_tubeData = NULL;
	protected $_updateDataSend = NULL;
	


	
	private $_tableName = NULL;
	private $_primaryColumn = NULL;
	private $_primaryKey = NULL;
	

	public function getToUpdateData(){
		return $this->_tubeData;
	}
	
	function __construct($tableName, $id, $primaryColumn = "id") {
		$this->_tableName = $tableName;
		$this->_primaryColumn = $primaryColumn;
		$this->_primaryKey = $id;
	
	}



	public function getTubeData(){
		return $this->_tubeData;
	}
	

	
	public function addColumn( $colummName){
		if($this->_colummArray === NULL){
			$this->_colummArray = array();
		}
		$this->_colummArray[] = $colummName;
		return $this;
	}
	public function clearColumn(){
		$this->_colummArray = NULL;
		return $this;
	}
	public function allColumn(){
		return $this->_colummArray;
	}
	
	
	public function getCleanData(){
		
		if (is_array ( $this->_tubeData  )) {
			$tube = array_intersect_key ( $tube, array_flip ( $this->_tubeData ) );
		}	
	}
	
	


	public function toUpdate() {
		
		if($this->_connect !== NULL){


			require_once 'Zend/Db/Select.php';
			
			$select = new Zend_Db_Select ( $this->_connect );
			$select->from ( $this->_tableName );
			$select->where ( $this->_primaryColumn . " = ?", $this->_primaryKey );
			
			$stmt = $this->_connect->query ( $select->__toString () );
			$tube = $stmt->fetch ();

			$this->_tubeData = $tube;
			
			return $tube;
		}
		
		return NULL;
	}
	
	public function update($updateData){
		$this->_updateDataSend = $updateData;
		
		$this->_connect->update($this->_tableName , $updateData, $this->_primaryColumn . " = ?", $this->_primaryKey);
	}

	public function allowUpdateKeys( $dataKeysArray){
		if(is_array($dataKeysArray) ){
			$this->_allowUpdateDataKeys = $dataKeysArray;
		}
	}

	

	

}

?>