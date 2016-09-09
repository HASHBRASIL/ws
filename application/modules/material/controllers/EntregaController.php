<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  13/05/2013
 */
class Material_EntregaController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Material_Model_Bo_Entrega
     */
    protected $_bo;
    protected $_isCompany = true;
    protected $_aclActionAnonymous = array('grid');

    public function init()
    {
        $this->_helper->layout->setLayout('metronic');
        $this->_bo = new Material_Model_Bo_Entrega();

        $this->_messageBroker = App_Validate_MessageBroker::getInstance();
        if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('auth/index/login');
        }
        $this->_id = $this->getRequest()->getParam('id');

        $this->_redirectFormSuccess = 'material/entrega/empresa';
        parent::init();
    }

    public function empresaAction()
    {
        $estadoBo                 = new Sis_Model_Bo_Estado();

        $this->view->comboEstado  = array(null => "Estado")+$estadoBo->getPairs(false);
    }

    public function gridAction()
    {

        $this->_helper->layout->disableLayout();
        $list                   = $this->_bo->getItemEntrega();
        $this->view->listEstoque  = $list;
    }

}