<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Material_Model_Vo_Grupo extends Zend_Db_Table_Row_Abstract
{
    protected $_subGrupo;

    public function getListSubGrupo()
    {
        $subGrupoDao = new Material_Model_Dao_Subgrupo();
        $select = $subGrupoDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findDependentRowset('Material_Model_Dao_SubGrupo', 'Grupo', $select);
    }

    public function getListItem()
    {
        $itemDao = new Material_Model_Dao_Item();
        $select = $itemDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findDependentRowset("Material_Model_Dao_Item", 'Grupo', $select);
    }
}