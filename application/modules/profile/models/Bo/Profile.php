<?php
class Profile_Model_Bo_Profile extends App_Model_Bo_Abstract
{
    /**
     * @var Profile_Model_Dao_Profile
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Profile_Model_Dao_Profile();
        parent::__construct();
    }
    
    
    protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null) 
    {
    	
    	$resourceProfile = new Profile_Model_Bo_ResourceProfile();
    	$result = $resourceProfile->physicallyDelete($object->id_au_profile);
    	
    	if(count($request['resourceList'])>0){
    		
    		foreach ($request['resourceList'] as $key => $resource) {
    			
    			$profileResourceObj = $resourceProfile->get();
    			$profileResourceObj->id_au_resource = $resource;
    			$profileResourceObj->id_au_profile = $object->id_au_profile;
    			$profileResourceObj->save();
    			
    		}
	    		
    	}
    }
    
    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null){
    	 
    	if ($object->name_profile == ""){
    
    		App_Validate_MessageBroker::addErrorMessage('O campo de nome está vazio.');
    		return false;
    	}
    	if ($object->module_profile == ""){
    
    		App_Validate_MessageBroker::addErrorMessage('Selecione o módulo desejado.');
    		return false;
    	}
    	return true;
    	 
    }

}