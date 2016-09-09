<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Material_Model_Vo_Subgrupo extends Zend_Db_Table_Row_Abstract
{
    protected $_classe;

    public function getListClasse()
    {
        $classeDao = new Material_Model_Dao_Classe();
        $select = $classeDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findDependentRowset('Material_Model_Dao_Classe', 'Subgrupo', $select);
    }

    public function getGrupo()
    {
        $grupoDao = new Material_Model_Dao_Grupo();
        $select = $grupoDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findParentRow('Material_Model_Dao_Grupo', 'Grupo', $select);
    }

    public function getListItem()
    {
        $itemDao = new Material_Model_Dao_Item();
        $select = $itemDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findDependentRowset("Material_Model_Dao_Item", 'Sub Grupo', $select);
    }
}