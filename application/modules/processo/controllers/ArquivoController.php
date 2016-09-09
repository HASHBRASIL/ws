<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  08/07/2013
 */
class Processo_ArquivoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_Arquivo
     */
    protected $_bo;

    protected $_isCompany = true;

    public function init()
    {
    	$this->_bo = new Processo_Model_Bo_Arquivo();
        parent::init();
    }

    public function getArquivoAction(){

    	$this->_bo->getArquivo($this->getParam("id"));

    }

    public function gridProcessoAction()
    {
        $this->_helper->layout()->disableLayout();
        $idProcesso = $this->getParam('id_processo');
        $this->view->listArquivo = $this->_bo->find(array('pro_id = ?' => $idProcesso));
    }

}