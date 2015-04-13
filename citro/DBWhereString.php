<?php

class DBWhereString
{
	const DB_SEQUENCER_AND = "AND";
	const DB_SEQUENCER_OR = "OR";
	private $_dbSequencer = DB_SEQUENCER_AND;
	
	public function __construct(){
		
	}
	
	private $_linkerList = array();
	
	public function getString(array $linkerList, $sequencer = DB_SEQUENCER_AND){
		$this->_linkerList = $linkerList;
		
	}
	
	private function cleaning(){
		
	}
}

?>