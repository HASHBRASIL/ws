<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Service_Model_Vo_Grupo extends Zend_Db_Table_Row_Abstract
{
    protected $_subGrupo;

    public function getListSubGrupo()
    {
        $subGrupoDao = new Service_Model_Dao_SubGrupo();
        $select = $subGrupoDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findDependentRowset('Service_Model_Dao_SubGrupo', 'Grupo', $select);
    }
    
    public function getListService()
    {
    	$serviceDao = new Service_Model_Dao_Servico();
    	$select = $serviceDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
    	return $this->findDependentRowset("Service_Model_Dao_Servico", 'Grupo', $select);
    }
}