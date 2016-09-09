<?php
class Pcp_Model_Bo_Timer extends App_Model_Bo_Abstract
{
    /**
     * @var Pcp_Model_Dao_Timer
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Pcp_Model_Dao_Timer();
        parent::__construct();
    }
    
    public function getWorkedHours($idTimer)
    {
    	
    	return $this->_dao->getWorkedHours($idTimer);
    	
    }
    
    public function getTimerList()
    {
    	 
    	return $this->_dao->getTimerList();
    	 
    }
    

}