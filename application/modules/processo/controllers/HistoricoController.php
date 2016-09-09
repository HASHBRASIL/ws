<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  25/07/2013
 */
class Processo_HistoricoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_Historico
     */
    protected $_bo;

    public function init()
    {
    	$this->_bo = new Processo_Model_Bo_Historico();
        $this->_messageBroker = App_Validate_MessageBroker::getInstance();
        $nameAction = $this->getRequest()->getActionName();

        if(!Zend_Auth::getInstance()->hasIdentity() && $nameAction != "email-history"){
                $this->_redirect('auth/index/login');exit($nameAction);
        }elseif(isset(Zend_Auth::getInstance()->getIdentity()->isEmpresa) && Zend_Auth::getInstance()->getIdentity()->isEmpresa && !$this->_isCompany && !($this->getRequest()->isXmlHttpRequest())){
            $this->_redirect('auth/index/login');
        }

        if(isset(Zend_Auth::getInstance()->getIdentity()->isEmpresa) && Zend_Auth::getInstance()->getIdentity()->isEmpresa){
            $this->_helper->layout->setLayout('empresa');
        }
        $this->_id = $this->getRequest()->getParam('id');

    }

    public function gridAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){
            $this->_helper->layout()->disableLayout();
        }
        $idProcesso = $this->getParam('pro_id');
        $limit      = $this->getParam('limit');
        $this->view->historicoList = $this->_bo->find(array('pro_id = ?' => $idProcesso), 'dt_criacao DESC', $limit);
        $this->view->historicoArray = $this->_bo->find(array('pro_id = ?' => $idProcesso), 'dt_criacao DESC', $limit);
        $this->view->limit = $limit;

    }

    public function emailHistoryAction()
    {
        exit('aki');
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_bo->sendEmailDay();
    }

}