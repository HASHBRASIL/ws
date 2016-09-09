<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  30/09/2013
 */
class Material_Model_Vo_Opcao extends Zend_Db_Table_Row_Abstract
{

    public function getAtributo()
    {
        return $this->findParentRow('Material_Model_Dao_Atributo', 'Atributo');
    }
}