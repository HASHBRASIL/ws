<?php
class Auth_Model_Bo_WorkspaceUser extends App_Model_Bo_Abstract
{
    /**
     * @var Auth_Model_Dao_WorkspaceUser
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Auth_Model_Dao_WorkspaceUser();
    }
    
    public function physicallyDelete($idUser){
    	
    	return $this->_dao->physicallyDelete($idUser);
    	
	}
}