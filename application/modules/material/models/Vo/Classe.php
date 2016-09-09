<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Material_Model_Vo_Classe extends Zend_Db_Table_Row_Abstract
{
    public function getSubgrupo()
    {
        $subgrupoDao = new Material_Model_Dao_Classe();
        $select = $subgrupoDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findParentRow('Material_Model_Dao_SubGrupo', 'Subgrupo', $select);
    }

    public function getListItem()
    {
        $itemDao = new Material_Model_Dao_Item();
        $select = $itemDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findDependentRowset("Material_Model_Dao_Item", 'Classe', $select);
    }
}