<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 28/6/2013
 */
class Service_ValorServicoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Service_Model_Bo_ValorServico
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Service_Model_Bo_ValorServico();
        $this->_aclActionAnonymous = array("get");
        parent::init();
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $vlServico = $this->_bo->get($id);
        $this->_helper->json($vlServico);
    }


    public function gridValorAction()
    {
        $this->_helper->layout->disableLayout();
        $id_servico = $this->getParam('id_servico');
        $id_tp_servico = $this->getParam('id_tp_servico');
        if($id_tp_servico == Service_Model_Bo_TipoServico::INTERNO){
            $criteriaTpServico = 'id_empresa is null';
        }else if($id_tp_servico == Service_Model_Bo_TipoServico::EXTERNO){
            $criteriaTpServico = 'id_empresa is not null';
        }
        $criteria = array('id_servico = ?' => $id_servico, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO );
        if(isset($criteriaTpServico)){
            $criteria = array($criteriaTpServico)+$criteria;
        }
        $this->view->valorList = $this->_bo->find($criteria);
    }
}