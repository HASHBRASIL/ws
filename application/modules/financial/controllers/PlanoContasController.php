<?php

    class Financial_PlanoContasController extends App_Controller_Action_TwigCrud
    {
        /**
         * @var Financial_Model_Bo_PlanoContas
         */
        protected $_bo;

        public function init()
        {
            $this->_bo = new Financial_Model_Bo_PlanoContas();
            $this->_aclActionAnonymous = array('get-pairs-per-type', 'quick-search-ajax');
            parent::init();
            $this->_helper->layout()->setLayout('novo_hash');
            $this->_redirectDelete = "home.php?servico=" . $this->servico['id_pai'];
            $this->_id = $this->getParam("id");
        }


        public function  getPairsPerTypeAction()
        {
            $type = $this->getRequest()->getParam('type');

            if (isset($type)) {

                $planoContas = $this->_bo->getPairsPerType($type);

            } else {

                $planoContas = $this->_bo->getPairsPerType();
            }

            $this->_helper->json(array("success" => "true", "data" => $planoContas));

        }

        public function _initForm()
        {

            $this->_id = $this->getParam("id");

            $grupoContasBo = new Financial_Model_Bo_GrupoContas();

            $this->view->grupoContasCombo = $grupoContasBo->getPairs(true);
            $this->view->planoContasCombo = $this->_bo->getPairs(false);
        }

//	public function gridAction(){
//
//        $identity = Zend_Auth::getInstance()->getIdentity();
//
//        $return = $this->_bo->find(array("id_grupo = ?" => $identity->time['id']));
////        ,
////            'ativo = ?' => App_Model_Dao_Abstract::ATIVO));
//
//
////
////        $workspaceSession = new Zend_Session_Namespace('workspace');
////
////		if ($workspaceSession->free_access){
////
////			$return = $this->_bo->find(null);
////
////		}else{
////
////			$return = $this->_bo->find("id_workspace = {$workspaceSession->id_workspace} or id_workspace IS NULL");
////		}
//
//		$this->view->planoContasList = $return;
//
//	}

        public function quickSearchAjaxAction()
        {

            $id = $this->_bo->get($this->getParam('id'));
            if ($id->plc_id != "") {
                $this->_helper->json(array("success" => "true"));
            } else {
                $this->_helper->json(array("success" => "false"));
            }
        }

    }