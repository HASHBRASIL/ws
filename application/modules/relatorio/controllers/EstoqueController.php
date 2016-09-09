<?php
class Relatorio_EstoqueController extends App_Controller_Action_AbstractCrud{

    private $usuario;


    public function init(){
        parent::init();
        $this->_helper->layout()->setLayout('metronic');
        $this->usuario = Zend_Auth::getInstance()->getIdentity()->nome_razao;
    }

    public function indexAction(){}

    public function relatorioAction(){
        $params = array ();

        $this->_bo = new Relatorio_Model_Bo_Estoque();

        $dataInicial = $this->getParam('dataInicial');
        $dataFinal = $this->getParam('dataFinal');
        $data_condicao = $this->getParam('data_condicao');

        $retorno = $this->_bo->getQtdRegistros($dataInicial, $dataFinal, $data_condicao);

        $params['qtdRegistros'] = $retorno['qtd'];
        if ($params['qtdRegistros'] == 0){
            App_Validate_MessageBroker::addErrorMessage("Não há registros para esta consulta.");
            $this->_redirect('relatorio/estoque/index');
        }
        $params['usuario'] =$this->usuario;

         $params['sql'] = $retorno['sql'];
        $caminhoXml = APPLICATION_PATH."/../data/jxml/relatorioEstoque.jrxml";
        $jasper = new App_Util_Jasper($caminhoXml, $params);
        $jasper->abrir();
    }
}