<?php
class Relatorio_ProtocoloController extends App_Controller_Action_AbstractCrud{
    private $usuario;
    private $protocoloBo;


    public function init(){

         $this->_helper->layout()->setLayout('metronic');
         parent::init();

         $this->usuario = Zend_Auth::getInstance()->getIdentity()->pes_nome;
         $this->protocoloBo = new Relatorio_Model_Bo_Protocolo();

    }
    public function visualizarAction(){
        $idProtocolo = 11;
        $registros = $this->protocoloBo->getRegistros($idProtocolo);

        if (!isset($registros))
            $this->_redirect("empresa/empresa/index");
        $this->view->usuario = $this->usuario;
        $this->view->protocoloRegistros = $registros;
    }





}