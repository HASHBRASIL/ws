<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Service_Model_Vo_SubGrupo extends Zend_Db_Table_Row_Abstract
{
    protected $_classe;

    public function getListClasse()
    {
        $classeDao = new Service_Model_Dao_Classe();
        $select = $classeDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findDependentRowset('Service_Model_Dao_Classe', 'SubGrupo', $select);
    }

    public function getGrupo()
    {
        $grupoDao = new Service_Model_Dao_Grupo();
        $select = $grupoDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findParentRow('Service_Model_Dao_Grupo', 'Grupo', $select);
    }
    
    public function getListService()
    {
    	$serviceDao = new Service_Model_Dao_Servico();
    	$select = $serviceDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
    	return $this->findDependentRowset("Service_Model_Dao_Servico", 'subGrupo', $select);
    }
}