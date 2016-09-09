<?php
class Profile_Model_Dao_ProfileUser extends App_Model_Dao_Abstract
{
    protected $_name         = "ta_profile_x_user";

    public function physicallyDelete($idUser){
    	return $this->_db->delete($this->_name, array("usu_id = ?" => $idUser));

    }
    
    public function profileUserByIdUser($idUser){
    
    	$select = $this->_db->select();
    	$select->from(array('pu' => $this->_name))
    	->joinInner(array('p'=>'tb_au_profile'), 'pu.id_au_profile = p.id_au_profile')
    	->where('pu.usu_id = ?', $idUser)
    	->where('p.ativo = ?', parent::ATIVO);
    	
    	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);
    
    }
    
    
    
}