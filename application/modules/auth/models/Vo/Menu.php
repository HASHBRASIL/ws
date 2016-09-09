<?php
class Auth_Model_Vo_Menu extends App_Model_Vo_Row
{
	
	public function getListMenu()
	{
		$menuDao = new Auth_Model_Dao_Menu();
		$select = $menuDao->select()->where("ativo = 1");
		return $this->findDependentRowset("Auth_Model_Dao_Menu", 'Menu',$select);
	}
	
	public function getResource()
	{
		return $this->findParentRow('Resource_Model_Dao_Resource', 'Resource');
	}
	
	public function getListChildrenPerUser()
	{
		$idUser = Zend_Auth::getInstance()->getIdentity()->usu_id;
		
		$authBo		= new Auth_Model_Bo_Usuario();
		$usuario = $authBo->find(array())->current();;
		
    	$profileUserBo		= new Profile_Model_Bo_ProfileUser();
    	$resourceProfileBo		= new Profile_Model_Bo_ResourceProfile();
    	
    	if (Zend_Auth::getInstance()->getStorage()->read()->root == true){
    		$profileBo			= new Profile_Model_Bo_Profile();
    		$profileUserObj		= $profileBo->find();
    	}else{
    		$profileUserObj		= $profileUserBo->profileUserByIdUser($idUser);
    	}
    	$resourceIds = array();
    	foreach ($profileUserObj as $keyProfile => $profile) {
    		$resourceProfileObj		= $resourceProfileBo->resourceByProfile($profile->id_au_profile);
    	
    		foreach ($resourceProfileObj as $keyResource => $resource) {
    			 
    			$resourceIds[] = $resource->id_au_resource;
    		}
    	}
		
		$menuDao = new Auth_Model_Dao_Menu();
		 if (count($resourceIds)>0){
		 	$select = $menuDao->select()->where("ativo = 1")->where('id_au_resource IN (?) or id_au_resource is null', $resourceIds)->where('id_au_parent_menu = ?', $this->id_au_menu);
		 }else{
		 	return;
		 }
		
		return $menuDao->fetchAll($select);
	}
	
}