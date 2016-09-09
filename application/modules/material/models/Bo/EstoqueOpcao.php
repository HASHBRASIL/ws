<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  14/01/2014
 */
class Material_Model_Bo_EstoqueOpcao extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_EstoqueOpcao
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_EstoqueOpcao();
        parent::__construct();
    }

    public function deleteByEstoque($idEstoque)
    {
        return $this->_dao->delete(array('id_estoque = ?' => $idEstoque));
    }

    public function getListAll($idEstoque = null, $idOpcao = null)
    {
        return $this->_dao->getListAll($idEstoque, $idOpcao);
    }
}