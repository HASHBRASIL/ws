<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  27/08/2014
 */
class Processo_PedidoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_Processo
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Processo_Model_Bo_Processo();
        parent::init();
        $this->_helper->layout->setLayout('novo_hash');
    }

    public function meusPedidosAction()
    {
        $criteria = array('empresas_id = ?' => Zend_Auth::getInstance()->getIdentity()->id);
        $this->view->pedidoList = $this->_bo->find($criteria);
    }
}