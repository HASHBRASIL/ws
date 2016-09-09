<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  03/05/2013
 */
class Material_Model_Vo_Nfe extends App_Model_Vo_Row
{

    public function getImposto()
    {
        return $this->findParentRow('Material_Model_Dao_Imposto', 'Imposto');
    }

    public function getTransportador()
    {
        return $this->findParentRow('Material_Model_Dao_Transportador', 'Transportador');
    }

    public function getEnderecoTransp()
    {
        return $this->findParentRow('Sis_Model_Dao_Endereco', 'Endereco transportador');
    }

    public function getEmpresaDestinatario()
    {
        return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Empresa Destinatario');
    }

    public function getEnderecoDestinatario()
    {
        return $this->findParentRow('Sis_Model_Dao_Endereco', 'Endereco Destinatario');
    }

    public function getFornecedor()
    {
        return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Fornecedor');
    }

    public function getEnderecoFornecedor()
    {
        return $this->findParentRow('Sis_Model_Dao_Endereco', 'Endereco fornecedor');
    }

    public function getItemList()
    {
        $itemDao = new Material_Model_Dao_Estoque();
        $select = $itemDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findDependentRowset('Material_Model_Dao_Estoque', 'Nota Fiscal', $select);
    }

}