<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  24/05/2013
 */
class Material_Model_Bo_EstoqueMovimento extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_EstoqueMovimento
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_EstoqueMovimento();
        parent::__construct();
    }

    /**
     * @desc irá retorna a soma da quantidade existente no estoque daquele item naquela empresa
     * @param int $idEmpresa
     * @param int $idItem
     */
    public function sumQuantidadeItemEmpresa($idEmpresa, $idItem)
    {
        return $this->_dao->sumQuantidadeItemEmpresa($idEmpresa, $idItem);
    }

    public function getEstoqueByEntrega()
    {
        $idEmpresa = Zend_Auth::getInstance()->getIdentity()->id;
        $listIdItem = array();

        $listEstoqueNew = $this->_dao->getItemByEstoque($listIdItem, $idEmpresa);
        if(count($listEstoqueNew)){
            foreach ($listEstoqueNew as &$estoque){
                $estoque['quantidade'] = floatval($this->_dao->sumQuantidadeItemEmpresa($idEmpresa, $estoque['id_item']));
            }
        }
        return $listEstoqueNew;
    }

    public function getDetailByItem($idItem)
    {
        return $this->_dao->getDetailByItem($idItem);
    }

    public function delete($id_estoque = null, $id_movimento = null)
    {
        $where = array();
        if($id_estoque){
            $where['id_estoque = ?'] = $id_estoque;
        }
        if($id_movimento){
            $where['id_movimento = ?'] = $id_movimento;
        }
        return $this->_dao->delete($where);
    }

}