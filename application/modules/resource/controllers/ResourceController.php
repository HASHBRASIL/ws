<?php

class Resource_ResourceController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Resource_Model_Bo_Resource
     */
    protected $_bo;
    protected $_aclActionAnonymous = array("get-resource-by-module-ajax");
    
    public function init()
    {
    	$this->_redirectDelete = ("/resource/resource/grid");
        $this->_bo = new Resource_Model_Bo_Resource();
        $this->_helper->layout()->setLayout('metronic');
        parent::init();
    	$this->_id = $this->getParam("id_au_resource");
    	
    }
    
    public function _initForm(){
    	
    	$registry = App_Module_Registry::getInstance();
    	$this->view->modules = $registry->getModuleList();
    }
    
    public function gridAction(){
    	
    	$this->view->resource = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO));
    	
    }
    
    public function getResourceByModuleAjaxAction(){
    	
    	$module = $this->getParam("moduleProfile");
    	$resourceList = $this->_bo->find(array("module_resource = ?" => $module, "ativo = ?" => App_Model_Dao_Abstract::ATIVO));
    	$response = array("success" => true, "data" => $resourceList->toArray());
    	$this->_helper->json($response);
    	
    }

}

