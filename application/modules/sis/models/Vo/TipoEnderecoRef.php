<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  30/04/2013
 */
class Sis_Model_Vo_TipoEnderecoRef extends App_Model_Vo_Row
{
    public function getTipoEndereco()
    {
        return $this->findParentRow('Sis_Model_Dao_TipoEndereco', 'Tipo Endereco');
    }

    public function getEndereco()
    {
        $enderecoDao = new Sis_Model_Dao_Endereco();
        $select = $enderecoDao->select()->where("ativo = ?", App_Model_Dao_Abstract::ATIVO);
        return $this->findParentRow('Sis_Model_Dao_Endereco', 'Endereco', $select);
    }
}