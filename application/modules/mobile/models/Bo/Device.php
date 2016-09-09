<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  21/03/2014
 */
class Mobile_Model_Bo_Device extends App_Model_Bo_Abstract
{
    /**
     * @var Mobile_Model_Dao_Device
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Mobile_Model_Dao_Device();
        parent::__construct();
    }
    
    public function checkValidDevice($uuid){
    	
    	$deviceList = $this->find(array("ativo = ?" =>App_Model_Dao_Abstract::ATIVO , "uuid = ?" => $uuid))->current();
    	 
    	if(count($deviceList)>0){
    		
    		return true;

    	}else{
    		
    		return false;
    	}
    	
    }
    
    
}