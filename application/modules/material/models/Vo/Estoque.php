<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  06/05/2013
 */
class Material_Model_Vo_Estoque extends App_Model_Vo_Row
{

    public function getItem()
    {
        return $this->findParentRow('Material_Model_Dao_Item', 'Item');
    }

    public function getUnidade()
    {
        return $this->findParentRow('Sis_Model_Dao_TipoUnidade', 'Unidade');
    }

    public function getOpcaoList()
    {
        return $this->findManyToManyRowset('Material_Model_Dao_Opcao', 'Material_Model_Dao_EstoqueOpcao');
    }

    public function getWorkspace()
    {
        return $this->findParentRow('Auth_Model_Dao_Workspace', 'Workspace');
    }
}