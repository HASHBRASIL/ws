<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/05/2013
 */
//desabilitando o limite de memória de processamento do servidor
ini_set( "memory_limit", -1 );
//desabilitando o limite do tempo de execução para geração de PDF`s grandes
ini_set( "max_execution_time", 0 );
class Material_ProtocoloController extends App_Controller_Action_AbstractCrud
{
    protected $_bo;

    public function init()
    {
        $this->_bo = new Material_Model_Bo_Protocolo();
        parent::init();
        $this->_redirectDelete = 'material/protocolo/grid';
        $this->_helper->layout->setLayout('metronic');

        $this->_hasWorkspace = true;
        $this->_getRegistersWithoutWorkspace = true;
    }

    public function gridAction()
    {
        $allParams = $this->getRequest()->getParams();
        if(isset($allParams['searchString'])){
            $allParams['searchString'] = str_replace(array('.', '-', '/'), '', $allParams['searchString']);
        }
        $paginator              = $this->_bo->paginator($allParams);
        $this->view->paginator  = $paginator;
    }

    public function _initForm()
    {
        $id = $this->getParam('id_protocolo');
        if(!empty($id)){
            $this->_id = $id;
        }
        $tpEntradaBo     = new Material_Model_Bo_TipoEntrada();
        $tpUnidadeBo     = new Sis_Model_Bo_TipoUnidade();
        $centroCustoBo   = new Service_Model_Bo_CentroCusto();
        $processoBo      = new Processo_Model_Bo_Processo();
        $pessoaBo        = new Auth_Model_Bo_Pessoal();
        $controleBo      = new Sis_Model_Bo_Controle();
        $marcaBo         = new Material_Model_Bo_Marca();
        $tpTransportBo   = new Material_Model_Bo_TipoTransportador();
        $movBo           = new Material_Model_Bo_TipoMovimento();
        $operacaoBo      = new Empresa_Model_Bo_GrupoOperacoes();
        $empresaBo       = new Empresa_Model_Bo_Empresa();


        $controleReceptora = $this->getParam('controle_receptora');
        $controleFornecedor = $this->getParam('controle_fornecedor');

        $this->view->comboTpTransport  = array(null => '---- Selecione ----')+$tpTransportBo->getPairs();
        $this->view->comboCentroCusto  = array(null => '---- Selecione ----')+$centroCustoBo->getPairs();
        $this->view->comboProcesso     = array(null => '---- Selecione ----')+$processoBo->getPairs(false);
        $this->view->tpEntradaCombo    = array(null => '---- Selecione ----')+$tpEntradaBo->getPairs();
        $this->view->tpUnidadeCombo    = array(null => '---- Selecione ----')+$tpUnidadeBo->getPairs(false);
        $this->view->comboPessoa       = array(null => '---- Selecione ----')+$empresaBo->getFuncionarioPairs();
        $this->view->marcaCombo        = array(null => '---- Selecione ----')+$marcaBo->getPairs();
        $this->view->comboMovimentacao = array(null => '---- Selecione ----')+$movBo->getPairs(false);
        $this->view->comboOperacao     = array(null => '---- Selecione ----')+$operacaoBo->getPairs();
        $this->view->comboEmpresaGrupo = array(null => '---- Selecione ----')+$empresaBo->getGrupoPairs();

        if(empty($controleReceptora) && !empty($this->_id)){
            $criteria = array(
                    'id_gm_protocolo = ?'   => $this->_id,
                    'id_tp_controle = ?'    => Sis_Model_Bo_TipoControle::RECEPTOR,
                    'ativo = ?'             => App_Model_Dao_Abstract::ATIVO
            );
            $this->view->controleReceptora = $controleBo->find($criteria);
        }else{
            $this->view->controleReceptora = !empty($controleReceptora) ? $controleReceptora : null;
        }

        if(empty($controleFornecedor) && !empty($this->_id)){
            $criteria = array(
                    'id_gm_protocolo = ?'   => $this->_id,
                    'id_tp_controle = ?'    => Sis_Model_Bo_TipoControle::FORNECEDOR,
                    'ativo = ?'             => App_Model_Dao_Abstract::ATIVO
            );
            $this->view->controleFornecedor = $controleBo->find($criteria);
        }else{
            $this->view->controleFornecedor = !empty($controleFornecedor) ? $controleFornecedor : null;
        }
    }

    public function gridItemAction()
    {
        $this->_helper->layout->disableLayout();
        $id_protocolo       = $this->getParam('id_protocolo');
        $movBo              = new Material_Model_Bo_Movimento();
        $listItemMov        = array();

        if(!empty($id_protocolo)){
            $criteria = array(
                    'ativo = ?'          => App_Model_Dao_Abstract::ATIVO,
                    'id_protocolo = ?'   => $id_protocolo
            );
            $listItemMov = $movBo->find(array('id_protocolo = ?'   => $id_protocolo));
        }

        $this->view->listItemMov  = $listItemMov;
    }

    public function relatorioAction()
    {
        $params = array ();
        $params['usuario'] =Zend_Auth::getInstance()->getIdentity()->nome_razao;
        $params['idprotocolo'] =  $this->getParam('id_protocolo');
        $trel =  $this->getParam('trel');
        $saldo =  $this->getParam('saldo');

        if($trel == '0'){
            if($saldo == '1'){
                $caminhoXml = APPLICATION_PATH."/../data/jxml/relatorioProtocoloAnaliticoSaldo.jrxml";
            }
            else{
                $caminhoXml = APPLICATION_PATH."/../data/jxml/relatorioProtocoloAnalitico.jrxml";
            }
        }
        else{
            if($saldo == '1'){
                $caminhoXml = APPLICATION_PATH."/../data/jxml/relatorioProtocoloSimplesSaldo.jrxml";
            }
            else{
                $caminhoXml = APPLICATION_PATH."/../data/jxml/relatorioProtocoloSimples.jrxml";
            }
        }

        $jasper = new App_Util_Jasper($caminhoXml, $params);
        $jasper->abrir();
    }

    public function reciboEntregaAction()
    {

    	{
    		$params = array ();
    		$params['usuario'] =Zend_Auth::getInstance()->getIdentity()->pes_nome;
    		$params['idprotocolo'] =  $this->getParam('id_protocolo');

    		$caminhoXml = APPLICATION_PATH."/../data/jxml/entregaClienteProtocolo.jrxml";

    		$jasper = new App_Util_Jasper($caminhoXml, $params);
    		$jasper->abrir();
    	}

    }
}