<?php
/**
 * Definieren einer Resorce
 * @author Max Plank
 *
 */
class Resource 
{
    /**
     * Unique id of Resource
     *
     * @var string
     */
    protected $_resourceId;

    protected $_partent = NULL;
    
    protected $_childs = NULL;
    
    /**
     * Inizialiseren der Resource
     *
     * @param  string $resourceId
     * @return void
     */
    public function __construct($resourceId)
    {
         $this->_resourceId = (string) $resourceId;
       
    }

    /**
     * Defined by Zend_Acl_Resource_Interface; returns the Resource identifier
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->_resourceId;
    }
    
    
    
    
    public function setChild($child){
    	if($this->_childs === NULL)
    		$this->_childs = new ArrayObject(array());
    	
    	if(is_string($child))
    		$child = new Resource($child);
    	
    	
    	if($child instanceof Resource)
    		$this->_childs->offsetSet($child->getResourceId(),$child);
    		
    }
    
    
    
    
    public function setParent(&$parent){
    	if($parent instanceof Resource)
    		$this->_partent = $parent;	
    }

    
    
    
    /**
     * Defined by Zend_Acl_Resource_Interface; returns the Resource identifier
     * Proxies to getResourceId()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getResourceId();
    }
    
    
    
    
    
}





