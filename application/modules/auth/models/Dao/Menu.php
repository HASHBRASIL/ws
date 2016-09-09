<?php
class Auth_Model_Dao_Menu extends App_Model_Dao_Abstract
{

    protected $_name         = "tb_au_menu";
    protected $_primary      = "id_au_menu";

    protected $_rowClass = 'Auth_Model_Vo_Menu';
    protected $_dependentTables = array('Auth_Model_Dao_Menu');
    
    protected $_referenceMap    = array(
    
    		'Menu' => array(
    				'columns'           => 'id_au_parent_menu',
    				'refTableClass'     => 'Auth_Model_Dao_Menu',
    				'refColumns'        => 'id_au_menu'
    		),
    		'Resource' => array(
    				'columns'           => 'id_au_resource',
    				'refTableClass'     => 'Resource_Model_Dao_Resource',
    				'refColumns'        => 'id_au_resource'
    		)
   );
    
    public function getListMenuByProfileUser($resourcesId){
    
    	$select = $this->_db->select();
    	$select->from(array('m' => $this->_name))
    	->where("ativo = ?", parent::ATIVO)
    	->where('id_au_resource IN (?)', $resourcesId )
    	->orWhere("id_au_resource is null");
    	
    	return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);
    }
    
}