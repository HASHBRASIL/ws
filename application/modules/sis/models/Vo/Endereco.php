<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  29/04/2013
 */
class Sis_Model_Vo_Endereco extends App_Model_Vo_Row
{
    public function getCidade()
    {
        return $this->findParentRow('Sis_Model_Dao_Cidade', 'Cidade');
    }

    public function getEstado()
    {
        return $this->findParentRow('Sis_Model_Dao_Estado', 'Estado');
    }

    public function getListTipoEndereco()
    {
        return $this->findDependentRowset('Sis_Model_Dao_TipoEnderecoRef', 'Endereco');
    }
}