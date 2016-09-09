<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  11/05/2013
 */
class Material_Model_Vo_Protocolo extends App_Model_Vo_Row
{

    protected $_hasMovimento;

    public function getReceptora()
    {
        return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Receptora');
    }

    public function getFornecedor()
    {
        return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Fornecedor');
    }

    public function getTransportador()
    {
        return $this->findParentRow('Material_Model_Dao_Transportador', 'Transportador');
    }

    public function getTpProtocolo()
    {
        return $this->findParentRow('Material_Model_Dao_TipoEntrada', 'Tipo Protocolo');
    }


    public function getItemList()
    {
        $itemDao = new Material_Model_Dao_Estoque();
        $select = $itemDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->findDependentRowset('Material_Model_Dao_Estoque', 'Protocolo', $select);
    }

    public function getMovimentoList()
    {
        return $this->findDependentRowset('Material_Model_Dao_Movimento', 'Protocolo');
    }

    public function hasMovimento()
    {
        if(!$this->_hasMovimento && !empty($this->id_protocolo)){
            $this->_hasMovimento = $this->findDependentRowset('Material_Model_Dao_Movimento', 'Protocolo')->count();
        }

        if($this->_hasMovimento)
            return true;

        return false;
    }
}