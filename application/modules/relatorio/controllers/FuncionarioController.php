<?php

class Relatorio_FuncionarioController extends App_Controller_Action_AbstractCrud
{
    private $_usuario;

    public function init(){
       $this->_helper->layout()->setLayout("metronic");
        parent::init();
        $this->_usuario = Zend_Auth::getInstance()->getIdentity()->nome_razao;
        $this->view->usuario = $this->_usuario;

    }
    public function indexAction()
    {

    }
    public function relatorioAction(){

        $funcionarioBo = new Relatorio_Model_Bo_Funcionario();
        $retorno = $funcionarioBo->getFuncionariosCondicao();

        if ($retorno['total'] == 0){
            App_Validate_MessageBroker::addErrorMessage("Esta consulta não possui registros");
            $this->_redirect('relatorio/funcionario/index');
        }
        $caminhoXml = APPLICATION_PATH."/../data/jxml/relatorioFuncionario.jrxml";
        $jasper = new App_Util_Jasper($caminhoXml, array(
                    'usuario'=>$this->_usuario,
                    'sql'=>$retorno['sql'],
                    'total'=>$retorno['total']));
        $jasper->abrir();

    }

}