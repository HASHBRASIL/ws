<?php
class Relatorio_CentroCustoController extends App_Controller_Action_AbstractCrud{
    private $_usuario;

    public function init(){
        parent::init();
        $this->_helper->layout()->setLayout('metronic');

        $this->_usuario = Zend_Auth::getInstance()->getIdentity()->nome_razao;
        $this->view->usuario = $this->_usuario;

        $this->_bo = new Relatorio_Model_Bo_CentroCusto();

    }
    public function indexAction(){
        $caminhoXml = APPLICATION_PATH."/../data/jxml/relatorioCentroCusto.jrxml";
        $jasper = new App_Util_Jasper($caminhoXml, array('usuario'=>$this->_usuario));
        $jasper->abrir();
    }

}