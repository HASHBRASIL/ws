<?php
class Auth_Model_Dao_WorkspaceUser extends App_Model_Dao_Abstract
{

    protected $_name         = "ta_workspace_x_usuario";

    public function physicallyDelete($idUsuario){
    	return $this->_db->delete($this->_name, array("usu_id = ?" => $idUsuario));
    
    }
    
}