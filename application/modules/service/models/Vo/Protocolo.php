<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/05/2013
 */
class Service_Model_Vo_Protocolo extends App_Model_Vo_Row
{

    public function getReceptora()
    {
        return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Receptora');
    }

    public function getFornecedor()
    {
        return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Fornecedor');
    }

}