<?php

class Financial_TransacaoContaController extends App_Controller_Action_TwigCrud
{
    /**
     * @var Financial_Model_Bo_TransacaoConta
     */
    protected $_bo;

    public function init()
    {
        parent::init();

        // @todo ajustar isso.
        $this->_redirectDelete = "home.php?servico=" . $this->servico['id_pai'];
        $this->_bo = new Financial_Model_Bo_TransacaoConta();
        $this->_id = $this->getParam("id");
    }

    public function _initForm()
    {
//        $boContas = new Financial_Model_Bo_Contas();
        $moedaBo  = new Financial_Model_Bo_Moeda();
//        $this->view->contasCombo = $boContas->getPairs();
        $this->view->comboMoedas			= $moedaBo->getPairs(false);

        if (isset($this->servico['metadata']['erp_tipomov']) && $this->servico['metadata']['erp_tipomov']) {
            $this->getRequest()->setParam('tp_transacao_conta', $this->servico['metadata']['erp_tipomov']);
        }

//        $this->getRequest()->setParam('tp_transacao_conta', $this->servico['metadata']['erp_tipomov']);

    }

//    public function autocompleteAction()
//    {
//        $term = $this->getRequest()->getParam('term');
//        $list = $this->_bo->getAutocomplete($term, false);
//        $this->_helper->json($list);
//    }

    public function creditoAction () {
        parent::formAction();
    }
}
