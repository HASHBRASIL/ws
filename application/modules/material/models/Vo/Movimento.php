<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  28/05/2013
 */
class Material_Model_Vo_Movimento extends Zend_Db_Table_Row_Abstract
{
    public function getListMovimento()
    {
        return $this->findDependentRowset('Material_Model_Dao_EstoqueMovimento', 'Movimento');
    }

    public function getMaterialProcesso()
    {
        return $this->findParentRow('Processo_Model_Dao_MaterialProcesso', 'MaterialProcesso');
    }
}