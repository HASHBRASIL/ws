<?php
/**
 * @author: Ellyson de Jesus Silva
 * @since: 01/07/2013
 */
class Service_OrcamentoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Service_Model_Bo_Orcamento
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Service_Model_Bo_Orcamento();
        $this->_aclActionAnonymous = array("modelo");
        $this->_helper->layout()->setLayout('metronic');
        parent::init();
    }

    public function _initForm()
    {
        $this->_id = $this->getParam('id_orcamento');
        $tpOrcamentoBo     = new Service_Model_Bo_TipoOrcamento();
        $tpComponenteBo    = new Service_Model_Bo_TipoComponente();
        $tpServico         = new Service_Model_Bo_TipoServico();

        $this->view->comboTpServico    = array(null => '-- Selecione --')+$tpServico->getPairs(false);
        $this->view->comboTpOrcamento  = array(null => '---- Selecione ----')+$tpOrcamentoBo->getPairs();
        $this->view->comboComponente   = array(null => '---- Selecione ----')+$tpComponenteBo->getPairs();
    }

    public function modeloAction()
    {
        $id_tp_orcamento = $this->getParam('id_tp_orcamento');
        $criteria = array(
                'id_tp_orcamento = ?' => $id_tp_orcamento,
                'modelo = ?' => Service_Model_Bo_Orcamento::MODELO,
                'ativo = ?'=> App_Model_Dao_Abstract::ATIVO
        );
        $orcamento = $this->_bo->find($criteria);
        if(count($orcamento)){
            $response = array('success' => true);
        }else{
            $response = array('success' => false);
        }

        $this->_helper->json($response);
    }

    public function gridAction()
    {
        $criteria = array('ativo = ?' => App_Model_Dao_Abstract::ATIVO);
        $this->view->orcamentoList = $this->_bo->find($criteria);
    }
}