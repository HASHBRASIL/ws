<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  13/05/2013
 */
class Material_Model_Bo_ItemEntrega extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_ItemEntrega
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_ItemEntrega();
        parent::__construct();
    }

    public function getItemPedido($id_empresa, $id_item)
    {
        return $this->_dao->getItemPedido($id_empresa, $id_item);
    }

    public function getPedido()
    {
        $idEmpresa = Zend_Auth::getInstance()->getIdentity()->id;
        return $this->_dao->getPedido($idEmpresa);
    }

    public function sumQtdItem($id_item)
    {
        $idEmpresa = Zend_Auth::getInstance()->getIdentity()->id;
        return $this->_dao->sumQtdItem($id_item, $idEmpresa);
    }

}