<?php

require_once 'Zend/Acl/Role/Interface.php';

class Role implements Zend_Acl_Role_Interface{


	/**
	 * Unique id of Role
	 *
	 * @var string
	 */
	protected $_roleId;
	
	/**
	 * Sets the Role identifier
	 *
	 * @param  string $roleId
	 * @return void
	 */
	public function __construct($roleId)
	{
		$this->_roleId = (string) $roleId;
	}
	
	/**
	 * Defined by Zend_Acl_Role_Interface; returns the Role identifier
	 *
	 * @return string
	 */
	public function getRoleId()
	{
		return $this->_roleId;
	}
	
	/**
	 * Defined by Zend_Acl_Role_Interface; returns the Role identifier
	 * Proxies to getRoleId()
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getRoleId();
	}
	
		
	
}

?>