<?php
class Auth_Model_Bo_Menu extends App_Model_Bo_Abstract
{
    /**
     * @var Auth_Model_Dao_Menu
     */
    protected $_dao;
    protected $numberOfChildrenMenu;
    
    public function __construct()
    {
        $this->_dao = new Auth_Model_Dao_Menu();
        parent::__construct();
    }
    
    /**
     * @todo em breve colocar isso pra funcionar
     * @author Carlos Vinicius Bonfim da Silva
     * @desc Menu Recursivo com possibilidade de infinitos submenus, hoje é limitado a 3 submenus em um menu  pois o template não permite mais
     * @since 30/07/2013
     */
    /*public function buildMenu(){
    	
    	$menuPaiList = $this->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_au_parent_menu IS NULL"));
    	$menu = array();
    	
    	foreach ($menuPaiList as $keyMenu => $menuPai) {
    		
			$result = $this->searchMenuChild($menuPai);
    		
    	}
    	Zend_Debug::dump($menu);exit;
    	exit ("parei") ; return;
    }
    
    protected function searchMenuChild($menuPai){
    	
    	
    	$menuFilhosList =  $this->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_au_parent_menu = ?" => $menuPai->id_au_menu));
    	
    	if (count($menuFilhosList)>0){
    	
    		foreach ($menuFilhosList as $keyMenuFiho => $menu) {
    			
    			$result = $this->searchMenuChild($menu);
    			
    		}
    	
    	}
    	
    }*/
    /**
     * @author Carlos Vinicius Bonfim da Silva
     * @desc Conta quantos ancestrais em níveis de menu existe
     * @return int
     * @since 30/07/2013
     */
    protected function searchMenuPai($menuPai){
    	$count = 1/*pois ja existe um menu*/;
    	
    	while ($menuPai->id_au_parent_menu != ""){
    		$count = $count + 1;
    		
    		$menuPai = $this->find(array("id_au_menu = ?" => $menuPai->id_au_parent_menu, "ativo = ?"=> App_Model_Dao_Abstract::ATIVO))->current();
    		
    	}
    	
    	return $count;
    	 
    }
    
    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null){
    	
    	if ($object->name_menu == ""){
    		
    		App_Validate_MessageBroker::addErrorMessage('O campo de nome está vazio.');
    		return false;
    	}
    	if ($object->title_menu == ""){
    		
    		App_Validate_MessageBroker::addErrorMessage('O campo de título está vazio.');
    		return false;
    	}
    	
    	
    	if ($object->id_au_parent_menu){
    		
    		$menuParent = $this->find(array("id_au_menu = ?" => $object->id_au_parent_menu, "ativo = ?"=> App_Model_Dao_Abstract::ATIVO))->current();
    		
    		if($menuParent->id_au_parent_menu != ""){
    			
    			$result = $this->searchMenuPai($menuParent);
    			
    			if ($result  >= 4){
    				App_Validate_MessageBroker::addErrorMessage('A arquitetura do sistema não permite mais de 3 submenus.');
    				return false;
    				
    			}
    		}
    	}
    	return true;
    }
    
    
    public function getListMenuByProfileUser($idUser)
    {
    	//capturando profiles do usuario do banco
    	$profileUserBo		= new Profile_Model_Bo_ProfileUser();
    	$resourceProfileBo		= new Profile_Model_Bo_ResourceProfile();
    	
    	$profileUserObj		= $profileUserBo->profileUserByIdUser($idUser);
    	
    	$resourceIds = array();
    	 
    	foreach ($profileUserObj as $keyProfile => $profile) {
    		$resourceProfileObj		= $resourceProfileBo->resourceByProfile($profile->id_au_profile);
    		
    		 
    		foreach ($resourceProfileObj as $keyResource => $resource) {
    
    			$resourceIds[] = $resource->id_au_resource;
    		}
    	}
    	
    	return $this->_dao->getListMenuByProfileUser($resourceIds) ;
    }
    
    
}