<?php

class Relatorio_PlanoContasController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Relatorio_Model_Bo_PlanoContas
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Relatorio_Model_Bo_PlanoContas();
        parent::init();
    }

    public function autocompleteAction()
    {
        $term = $this->getRequest()->getParam('term');
        $list = $this->_bo->getAutocompletePlanoContas($term);
        $this->_helper->json($list);
    }
}