<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  01/08/2013
 */
class Processo_PcpTimerController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_PcpTimer
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Processo_Model_Bo_PcpTimer();
        parent::init();
    }

    public function gridProcessoAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){
            $this->_helper->layout()->disableLayout();
        }
        $idProcesso    = $this->getParam('id_processo');
        $this->view->timeList = $this->_bo->getTimeByProcesso($idProcesso);
    }
}