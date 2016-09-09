<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  13/05/2013
 */
class Material_PedidoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Material_Model_Bo_ItemEntrega
     */
    protected $_bo;

    protected $_isCompany = true;

    public function init()
    {
        $this->_helper->layout->setLayout('metronic');
    	$this->_aclActionAnonymous = array('ver-produto');
        $this->_bo = new Material_Model_Bo_ItemEntrega();
        parent::init();
    }

    public function indexAction()
    {
        $entregaBo                = new Material_Model_Bo_Entrega();
        $idUsuario                = Zend_Auth::getInstance()->getIdentity()->usu_id;
        $criteria                 = array('id_criacao_usuario = ?' => $idUsuario , 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);
        $this->view->listPedido   = $entregaBo->find($criteria);
    }

    public function verProdutoAction()
    {
        $this->_helper->layout->disableLayout();
        $idEntrega            = $this->getRequest()->getParam('id_entrega');
        $criteria             = array('id_entrega = ?' => $idEntrega);
        $this->view->listItem = $this->_bo->find($criteria);
    }

    public function entregaAction()
    {
        $entregaBo       = new Material_Model_Bo_Entrega();
        $statusBo        = new Material_Model_Bo_Status();

        $idUsuario                = Zend_Auth::getInstance()->getIdentity()->usu_id;

        $this->view->listPedido     = $entregaBo->getList();
        $this->view->comboStatus    = $statusBo->getPairs();

    }
}