<?php

class Auth_WorkspaceController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Auth_Model_Bo_Workspace
     */
    protected $_bo;
    protected $_aclActionAnonymous = array("register-workspace", 'autocomplete', 'get');

    public function init()
    {
    	parent::init();
    	$this->_redirectDelete = ("/auth/workspace/grid");
        $this->_bo = new Auth_Model_Bo_Workspace();
        $this->_helper->layout()->setLayout('metronic');
        $this->_id = $this->getRequest()->getParam('id_workspace');

    }

    public function gridAction(){

    	$workspaceSession = new Zend_Session_Namespace('workspace');

    	if ($workspaceSession->free_access){

    		$return = $this->_bo->find(array("ativo = ?" =>App_Model_Dao_Abstract::ATIVO));

    	}else{

    		$return = $this->_bo->find("id_workspace = {$workspaceSession->id_workspace} or id_workspace IS NULL and ativo =".App_Model_Dao_Abstract::ATIVO);
    	}

    	$this->view->workspaceList = $return;
    }

    public function registerWorkspaceAction(){

    	$this->noRenderAndNoLayout();

    	$workspace = $this->getAllParams();

    	$workspaceSession = new Zend_Session_Namespace('workspace');
    	$proprietarioSession = new Zend_Session_Namespace('proprietario');

    	$workspaceSession->unsetAll();
    	$proprietarioSession->unsetAll();

    	$workspaceObj = $this->_bo->get($workspace["id"]);

    	$workspaceSession->id_workspace = $workspaceObj->id_workspace;
    	$workspaceSession->name_workspace = $workspaceObj->nome;
    	$workspaceSession->free_access = $workspaceObj->free_access;

    	$proprietarioBo = new Sis_Model_Bo_Sis();
    	$proprietario = $proprietarioBo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, 'id_workspace = ?' => $workspaceSession->id_workspace))->current();
    	$proprietarioSession->proprietario = $proprietario;

    	$this->_helper->json(array("success" => true));

    }

    public function getAction()
    {
        $workspace = $this->_bo->get($this->getParam('id'));
        $this->_helper->json($workspace);
    }

}

