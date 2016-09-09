<?php

class Profile_ProfileController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Profile_Model_Bo_Profile
     */
    protected $_bo;
	
    public function init()
    {
        parent::init();
    	$this->_redirectDelete = ("/profile/profile/grid");
        $this->_bo = new Profile_Model_Bo_Profile();
        $this->_helper->layout()->setLayout('metronic');
    	$this->_id = $this->getParam("id_au_profile");
    }
    
    public function _initForm(){
    	
    	$registry = App_Module_Registry::getInstance();
    	$this->view->modules = $registry->getModuleList();
    	
    	if (is_numeric($this->getParam("id_au_profile"))){
    		
    		$resourceProfileBo		= new Profile_Model_Bo_ResourceProfile();
    		$resourceBo 			= new Resource_Model_Bo_Resource();
    		
    		$profileObj = $this->_bo->get($this->getParam("id_au_profile"));
    		$resourceObj = $resourceBo->find(array("module_resource = ?"=> $profileObj->module_profile, "ativo = ?" => App_Model_Dao_Abstract::ATIVO));
			$this->view->resourceListSaved = $resourceProfileBo->find(array("id_au_profile = ?"=> $this->getParam("id_au_profile") ));
			$this->view->resourceListComplete = $resourceObj;
    		
    	}
    	
    }
    
    public function formAction()
    {
    	$this->_initForm();
    	$request         = $this->getAllParams();
    	$object          = $this->_bo->get($this->_id);
    	// verificar se vem via post
    	if($this->getRequest()->isPost()){
    		try {
    			$this->_bo->saveFromRequest($request, $object);
    
    			if(empty($this->_messageFormSuccess)){
    				if(empty($this->_id)){
    					App_Validate_MessageBroker::addSuccessMessage("Dado inserido com sucesso");
    				}else{
    					App_Validate_MessageBroker::addSuccessMessage("Dado atualizado com sucesso");
    				}
    			}else{
    				App_Validate_MessageBroker::addSuccessMessage($this->_messageFormSuccess);
    			}
    
    			$this->_redirect("/profile/profile/form/id_au_profile/{$object->id_au_profile}");
    		}
    		catch (Exception $e){
    			App_Validate_MessageBroker::addErrorMessage("Ocorreu um erro inesperado. Entre em contato com o administrador.");
    		}
    	}
    	
	    if (is_numeric($this->getParam("id_au_profile_duplicate"))){
    	
    		$profileObj =  $this->_bo->get($this->getParam("id_au_profile_duplicate"));
    		$profileObj->id_au_profile = null;
    		$this->view->vo = $profileObj;
    		
    		$resourceProfileBo		= new Profile_Model_Bo_ResourceProfile();
    		$resourceBo 			= new Resource_Model_Bo_Resource();
    		
    		$profileObj = $this->_bo->get($this->getParam("id_au_profile_duplicate"));
    		$resourceObj = $resourceBo->find(array("module_resource = ?"=> $profileObj->module_profile, "ativo = ?" => App_Model_Dao_Abstract::ATIVO));
    		$this->view->resourceListSaved = $resourceProfileBo->find(array("id_au_profile = ?"=> $this->getParam("id_au_profile_duplicate")));
    		$this->view->resourceListComplete = $resourceObj;
    	
	    }else{
    	
    		$this->view->vo = $object;
    	}
    }
    
    public function gridAction(){
    	
    	$this->view->profile = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO));
    	
    }

}

