<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  30/09/2013
 */
class Material_Model_Vo_Atributo extends Zend_Db_Table_Row_Abstract
{

    public function getListOpcao()
    {
        $opcaoDao = new Material_Model_Dao_Opcao();
        $select = $opcaoDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findDependentRowset('Material_Model_Dao_Opcao', 'Atributo', $select);
    }
}