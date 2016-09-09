<?php
class Profile_Model_Dao_ResourceProfile extends App_Model_Dao_Abstract
{
    protected $_name         = "ta_resource_x_profile";

    public function physicallyDelete($idProfile){
    	return $this->_db->delete($this->_name, array("id_au_profile = ?" => $idProfile));

    }
    
    public function resourceByProfile($idProfile){
    	
    	$select = $this->_db->select();
    	$select->from(array('rp' => $this->_name))
    	->joinInner(array('r'=>'tb_au_resource'), 'r.id_au_resource = rp.id_au_resource')
    	->where('rp.id_au_profile = ?', $idProfile)
    	->where('r.ativo = ?', parent::ATIVO);
    	
    	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);
    
    }
    
}