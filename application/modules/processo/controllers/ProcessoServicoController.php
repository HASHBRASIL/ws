<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  14/08/2013
 */
class Processo_ProcessoServicoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_ProcessoServico
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Processo_Model_Bo_ProcessoServico();
        $this->_aclActionAnonymous = array('get');
        parent::init();
    }

    public function gridAction()
    {
        $this->_helper->layout()->disableLayout();
        $id_processo = $this->getParam('pro_id');
        $this->view->processoServicoList = $this->_bo->find(array('id_processo = ?' => $id_processo, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO));
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $processoServico = $this->_bo->get($id);

        $processoServicoArr = $processoServico->toArray();
        $processoServicoArr['nome_servico'] = $processoServico->getServico()->nome;

        $this->_helper->json($processoServicoArr);
    }

}