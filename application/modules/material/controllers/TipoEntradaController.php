<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/05/2013
 */
class Material_TipoEntradaController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Material_Model_Bo_TipoEntrada
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Material_Model_Bo_TipoEntrada();
        parent::init();
        $this->_helper->layout->setLayout('metronic');
        $this->_redirectDelete = "/material/tipo-entrada/grid";
        $this->_id = $this->getParam('id_tp_protocolo');
    }

    public function _initForm()
    {
        $tpMovimentoBo = new Material_Model_Bo_TipoMovimento();
        $this->view->tpMovimentoCombo = array(null => '---- Selecione ----')+$tpMovimentoBo->getPairs(false);
    }

    public function gridAction()
    {
        $this->view->tpProtocoloList = $this->_bo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO));
    }

}