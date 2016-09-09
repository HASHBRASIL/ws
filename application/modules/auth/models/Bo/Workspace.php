<?php
class Auth_Model_Bo_Workspace extends App_Model_Bo_Abstract
{
    /**
     * @var Auth_Model_Dao_Workspace
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Auth_Model_Dao_Workspace();
        parent::__construct();
    }
    
    public function registerWorkspace(){
    	
    	$workspaceUserBo = new Auth_Model_Bo_WorkspaceUser();
    	$workspaceBo = new Auth_Model_Bo_Workspace();
    	
    	$userId = Zend_Auth::getInstance()->getIdentity();
    	
    	$workspaceUserList = $workspaceUserBo->find(array("usu_id = ?" => $userId));
    	
    	$workspace = Array();
    	
    	foreach ($workspaceUserList as $key => $workspaceUser) {
    		
    		$workspace[] = $workspaceBo->get($workspaceUser->id_workspace)->toArray();
    	}
    	
    	return $workspace;
    }
    
    public function validateRegisterWithWorkspace($idWorkspace){
		    	
    	$workspaceSession = new Zend_Session_Namespace('workspace');
    	
   		if ($workspaceSession->id_workspace != $idWorkspace && $workspaceSession->free_access != true){
    			
    		return false;
    	}else{
    		
    		return true;
    	}
    	
    }
    
    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
    	 
    	if($object->free_access == true){
    	
    		$workspaceFreeaccess = $this->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "free_access = ?" => App_Model_Dao_Abstract::ATIVO))->current();
    	
    		if (count($workspaceFreeaccess) >= 1 && $workspaceFreeaccess->id_workspace != $object->id_workspace){
    				
    			App_Validate_MessageBroker::addErrorMessage("Já existe um workspace com acessos privilegiados no sistema.");
    			return false;
    		}
    	}
    	
    	if($object->acronym == ""){
    		 
    		App_Validate_MessageBroker::addErrorMessage("Deve existir obrigatóriamente uma sigla para este workspace.");
    		return false;
    	}else{
    		
    		$workspaceAcronymaccess = $this->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "acronym = ?" => $object->acronym))->current();
    		 
    		if (count($workspaceAcronymaccess) >= 1 && $workspaceAcronymaccess->id_workspace != $object->id_workspace){
    		
    			
    			App_Validate_MessageBroker::addErrorMessage("Já existe um Workspace com esta sigla no sistema");
    			return false;
    		}
    		
    	}
    	return true;
    }
}