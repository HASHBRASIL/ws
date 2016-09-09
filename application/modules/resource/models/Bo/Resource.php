<?php
class Resource_Model_Bo_Resource extends App_Model_Bo_Abstract
{
    /**
     * @var Resource_Model_Dao_Resource
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Resource_Model_Dao_Resource();
        parent::__construct();
    }
    
    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null){
    
    	if ($object->name_resource == ""){
    
    		App_Validate_MessageBroker::addErrorMessage('O campo de nome está vazio.');
    		return false;
    	}
    	if ($object->module_resource == ""){
    	
    		App_Validate_MessageBroker::addErrorMessage('Selecione um módulo.');
    		return false;
    	}
    	if ($object->controller_resource == ""){
    		 
    		App_Validate_MessageBroker::addErrorMessage('O campo da controladora está vazio.');
    		return false;
    	}
    	if ($object->action_resource == ""){
    		 
    		App_Validate_MessageBroker::addErrorMessage('O campo da ação está vazio.');
    		return false;
    	}
    	$criteria = array(
    			'module_resource = ?' 		=> $object->module_resource,
    			'controller_resource = ?' 	=> $object->controller_resource,
    			'action_resource = ?'		=> $object->action_resource
    	);
    	if(!empty($object->id_au_resource)){
    		$criteria = $criteria+array('id_au_resource <> ?' 		=> $object->id_au_resource);
    	}
    	if(count($this->find($criteria))){
    		App_Validate_MessageBroker::addErrorMessage("Já existe essa ação de segurança com este módulo, controller e ação.");
    		return false;
    	}
    	
    	return true;
    
    }
}