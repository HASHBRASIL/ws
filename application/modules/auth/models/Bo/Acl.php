<?php
class Auth_Model_Bo_Acl extends App_Model_Bo_Abstract
{

    public function __construct()
    {
    }
    
    public function registerAcl($idUser)
    {
    	$profileUserBo		= new Profile_Model_Bo_ProfileUser();
    	$resourceProfileBo		= new Profile_Model_Bo_ResourceProfile();
    	
    	$profileUserObj		= $profileUserBo->profileUserByIdUser($idUser);
    	
    	$acl = new Zend_Acl();
    	foreach ($profileUserObj as $keyProfile => $profile) {
    		
    		$acl->addRole(new Zend_Acl_Role($profile->name_profile));
    		
    		$resourceProfileObj		= $resourceProfileBo->resourceByProfile($profile->id_au_profile);
    		
			$actionsFromResource = array();
    		$moduleAndController = "";

    		foreach ($resourceProfileObj as $keyResource => $resource) {
    			
    			$moduleAndController = $resource->module_resource."-".$resource->controller_resource;
    			
    			if (!in_array($moduleAndController, $acl->getResources())) {
    				$acl->addResource(new Zend_Acl_Resource($moduleAndController));
    			}
    			$acl->allow($profile->name_profile, $moduleAndController, $resource->action_resource);
    			
    		}
    		
    	}
		return $acl;   	
    	
    }

}