<?php

class Sis_IndexController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Sis_Model_Bo_Sis
     */
    protected $_bo;
    
    public function init()
    {
        $this->_bo = new Sis_Model_Bo_Sis();
        $this->_messageBroker = App_Validate_MessageBroker::getInstance();
        parent::init();
        $this->_helper->layout()->setLayout('metronic');
        $this->_id = $this->getParam("id_proprietario");
        $this->_redirectDelete = "/sis/index/grid";
    }
    
    protected function deleteImageAjaxAction()
    {
    
    	$request = $this->getAllParams();
    	$proprietarioObj = $this->_bo->find(array("ativo = ?"=> App_Model_Dao_Abstract::ATIVO, "id_proprietario = ?"=> $request['id_proprietario'] ))->current();
    	$result = $this->_bo->deleteImageAjaxAction($request, $proprietarioObj);
    	if($result){
    		$response = array('success' => true);
    		$this->_helper->json($response);
    	}
    }
    
    protected function gridAction()
    {
    
    	$workspaceSession = new Zend_Session_Namespace('workspace');
    	
    	if ($workspaceSession->free_access){
    	
    		$return = $this->_bo->find(array("ativo = ?" =>App_Model_Dao_Abstract::ATIVO));
    	
    	}else{
    	
    		$return = $this->_bo->find("id_workspace = {$workspaceSession->id_workspace} or id_workspace IS NULL and ativo =".App_Model_Dao_Abstract::ATIVO);
    	}
    	
    	$this->view->proprietarioList = $return;
    	
    }

}

