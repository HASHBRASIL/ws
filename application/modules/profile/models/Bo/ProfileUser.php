<?php
class Profile_Model_Bo_ProfileUser extends App_Model_Bo_Abstract
{
    /**
     * @var Profile_Model_Dao_ProfileUser
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Profile_Model_Dao_ProfileUser();
    }
    
    public function physicallyDelete($idUser){
    	
    	return $this->_dao->physicallyDelete($idUser);
    	
    }
    
    public function profileUserByIdUser($idUser){
    	
    	return $this->_dao->profileUserByIdUser($idUser);
    	 
    }
    
}