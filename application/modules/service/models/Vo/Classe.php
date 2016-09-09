<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Service_Model_Vo_Classe extends Zend_Db_Table_Row_Abstract
{
    public function getSubgrupo()
    {
        $subgrupoDao = new Service_Model_Dao_Classe();
        $select = $subgrupoDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findParentRow('Service_Model_Dao_SubGrupo', 'SubGrupo', $select);
    }
    
    public function getListService()
    {
    	$serviceDao = new Service_Model_Dao_Servico();
    	$select = $serviceDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
    	return $this->findDependentRowset("Service_Model_Dao_Servico", 'Classe', $select);
    }
}