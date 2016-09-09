<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/05/2013
 */
class Service_ProtocoloController extends App_Controller_Action_AbstractCrud
{
    protected $_bo;

    public function init()
    {
        $this->_bo = new Service_Model_Bo_Protocolo();
        $this->_helper->layout()->setLayout('metronic');
        parent::init();
        $this->_redirectDelete = 'service/protocolo/grid';
    }

    public function gridAction()
    {
        $this->view->listaProtocolo  = $this->_bo->buscaProtocolo();
    }

    public function _initForm()
    {
        $id = $this->getParam('id_protocolo');
        if(!empty($id)){
            $this->_id = $id;
        }
        $tpEntradaBo     = new Service_Model_Bo_TipoEntrada();
        $tpUnidadeBo     = new Sis_Model_Bo_TipoUnidade();
        $centroCustoBo   = new Service_Model_Bo_CentroCusto();
        $processoBo      = new Processo_Model_Bo_Processo();
        $controleBo      = new Sis_Model_Bo_Controle();
        $marcaBo         = new Material_Model_Bo_Marca();
        $operacaoBo      = new Empresa_Model_Bo_GrupoOperacoes();
        $empresaBo       = new Empresa_Model_Bo_Empresa();
        $controleReceptora = $this->getParam('controle_receptora');
        $controleFornecedor = $this->getParam('controle_fornecedor');

        $this->view->comboCentroCusto  = array(null => '---- Selecione ----')+$centroCustoBo->getPairs();
        $this->view->comboProcesso     = array(null => '---- Selecione ----')+$processoBo->getPairs(false);
        $this->view->tpEntradaCombo    = array(null => '---- Selecione ----')+$tpEntradaBo->getPairs();
        $this->view->tpUnidadeCombo    = array(null => '---- Selecione ----')+$tpUnidadeBo->getPairs(false);
        $this->view->marcaCombo        = array(null => '---- Selecione ----')+$marcaBo->getPairs();
        $this->view->comboOperacao     = array(null => '---- Selecione ----')+$operacaoBo->getPairs();
        $this->view->comboEmpresaGrupo = array(null => '---- Selecione ----')+$empresaBo->getGrupoPairs();

        if(empty($controleReceptora) && !empty($this->_id)){
            $criteria = array(
                    'id_gs_protocolo = ?'   => $this->_id,
                    'id_tp_controle = ?' => Sis_Model_Bo_TipoControle::RECEPTOR,
                    'ativo = ?'          => App_Model_Dao_Abstract::ATIVO
            );
            $this->view->controleReceptora = $controleBo->find($criteria);
        }else{
            $this->view->controleReceptora = !empty($controleReceptora) ? $controleReceptora : null;
        }

        if(empty($controleFornecedor) && !empty($this->_id)){
            $criteria = array(
                    'id_gs_protocolo = ?'   => $this->_id,
                    'id_tp_controle = ?' => Sis_Model_Bo_TipoControle::FORNECEDOR,
                    'ativo = ?'          => App_Model_Dao_Abstract::ATIVO
            );
            $this->view->controleFornecedor = $controleBo->find($criteria);
        }else{
            $this->view->controleFornecedor = !empty($controleFornecedor) ? $controleFornecedor : null;
        }
    }

}