<?php
class Profile_Model_Bo_ResourceProfile extends App_Model_Bo_Abstract
{
    /**
     * @var Profile_Model_Dao_ResourceProfile
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Profile_Model_Dao_ResourceProfile();
    }
    
    public function physicallyDelete($idProfile){
    	
    	return $this->_dao->physicallyDelete($idProfile);
    	
    }
    
    public function resourceByProfile($idProfile){
    	 
    	return $this->_dao->resourceByProfile($idProfile);
    
    }
    
}