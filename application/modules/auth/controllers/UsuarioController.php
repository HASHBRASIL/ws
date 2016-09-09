<?php

class Auth_UsuarioController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Auth_Model_Bo_Usuario
     */
    protected $_bo;
    protected $_aclActionAnonymous = array("get-vinculo-usuario-exist-ajax", "change-password");
	
    public function init()
    {
    	parent::init();
    	$this->_redirectDelete = ("/auth/usuario/grid");
        $this->_bo = new Auth_Model_Bo_Usuario();
        $this->_helper->layout()->setLayout('metronic');
        $this->_id = $this->getRequest()->getParam('usu_id');
        
    }
    
    public function _initForm(){
    	
    	$registry = App_Module_Registry::getInstance();
    	$modulesList = $registry->getModuleList();
    	
    	$profileBo = new Profile_Model_Bo_Profile();
    	$workspaceBo = new Auth_Model_Bo_Workspace();
    	
    	$modulesListProfile =  array();
    	
    	foreach ($modulesList as $key => $module) {
    		$modulesListProfile[$key]["name"] = $module;
    		$modulesListProfile[$key]["values"] = $profileBo->find(array("module_profile = ?" => $key, "ativo = ?" => App_Model_Dao_Abstract::ATIVO))->toArray();
    	}
    	
    	$this->view->workspaceList = $workspaceBo->getPairs();
    	$this->view->modulesWithProfile = $modulesListProfile;
    	
    	if (is_numeric($this->getParam("usu_id"))){
    	
    		$empresaBo		= new Empresa_Model_Bo_Empresa();
    		$profileUserBo 	= new Profile_Model_Bo_ProfileUser();
    		$workspaceUserBo= new Auth_Model_Bo_WorkspaceUser();
    		
			$usuario		= $this->_bo->get($this->getParam("usu_id"));    		
    		
			$empresa = $empresaBo->find(array("id = ?" => $usuario->id_empresa, "ativo = ?" => App_Model_Dao_Abstract::ATIVO))->current();
			$this->view->nome_razao = $empresa->nome_razao;
			
			$this->view->profileUserSaved = $profileUserBo->find(array("usu_id = ?" => $this->getParam("usu_id")));
			
			$this->view->workspaceUserSaved = $workspaceUserBo->find(array("usu_id = ?" => $this->getParam("usu_id")));
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
    
    			$this->_redirect("/auth/usuario/form/usu_id/{$object->usu_id}");
    		}
    		catch (Exception $e){
    			App_Validate_MessageBroker::addErrorMessage("Ocorreu um erro inesperado. Entre em contato com o administrador.");
    		}
    	}
    
    	$this->view->vo = $object;
    }
    
    public function gridAction(){
    	
    	$this->view->userList = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO,"id_empresa IS NOT NULL"));
    }
    
    public function getVinculoUsuarioExistAjaxAction(){
    	 
    	$idEmpresa = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_empresa = ?"=> $this->getParam("idEmpresa")))->current();
    	
    	if($idEmpresa == null){
    		$this->_helper->json(array("success" => true, "exist" => false));
    	}else{
    		$this->_helper->json(array("success" => true, "exist" => true));
    	}
    	
    }
    
    public function changePasswordAction(){
    	
    	if($this->getRequest()->isPost()){
    		
    		$usuarioId = Zend_Auth::getInstance()->getIdentity()->usu_id;
    		$usuarioBo = new Auth_Model_Bo_Usuario();
    		$usuarioObj = $usuarioBo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "usu_id = ?" => $usuarioId))->current();
    		
    		$request = $this->getAllParams();
    		$request['notExcludeProfile'] = true;
    		
    		$this->_bo->saveFromRequest($request, $usuarioObj);
    		
    		if(empty($this->_messageFormSuccess)){
    			if(empty($this->_id)){
    				App_Validate_MessageBroker::addSuccessMessage("Dado inserido com sucesso");
    			}else{
    				App_Validate_MessageBroker::addSuccessMessage("Dado atualizado com sucesso");
    			}
    		}else{
    			App_Validate_MessageBroker::addSuccessMessage($this->_messageFormSuccess);
    		}
    		
    		
    	}
    }
    /**
     * @desc Dá a usuários os ID de perfis passadas por parametro. Para executar esta ação o usuário necessita ser root
     * @example /auth/usuario/allow-access-for-all-users/profiles/11;12;13;14;15;16;17;19
     * @author Carlos Vinicius Bonfim da Silva
     * @since 12/09/2013
     */
    
    public function allowAccessForAllUsersAction (){
    	
    	if (Zend_Auth::getInstance()->getStorage()->read()->root == true){
    		
    		$perfis = $this->getParam("profiles");
    		$perfis = explode(";", $perfis);
    		
    		$profileUserBo 			= new Profile_Model_Bo_ProfileUser();
    		$usersBo 				= new Auth_Model_Bo_Usuario();
    		$profileBo				= new Profile_Model_Bo_Profile();
    		
    		$usersListObj = $usersBo->find(array("ativo = ?"=> App_Model_Dao_Abstract::ATIVO));
    		
    		echo"*** START ACTION ***</br>";
    		$count = 0;
    		
    		foreach ($usersListObj as $key => $user) {
    			
    			$profileUserByUserId = $profileUserBo->find(array("usu_id = ?" => $user->usu_id));
    			
    			if (count($profileUserByUserId) > 0 ){
    				continue;
    			}
    			
    			echo "FOR USER {$user->usu_id} WAS NOT FOUND PROFILE(S)</br>";
    			
    			foreach ($perfis as $perfil){
    				
    				$profileObj = $profileBo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_au_profile = ?" => $perfil ));
    				
    				if (count($profileObj) == 0 || count($profileObj) == null){
    					
    					echo "ERROR: PROFILE {$perfil} NOT FOUND</br>";
    					continue;
    				}
    				
    				$profileUserObj = $profileUserBo->get();
    				
    				$profileUserObj->id_au_profile = $perfil;
    				$profileUserObj->usu_id = $user->usu_id;
    				
    				try{
    					$profileUserObj->save();
    					$count++;
    					echo "ROW: id_au_profile {$profileUserObj->id_au_profile} - usu_id {$profileUserObj->usu_id} SAVED n: {$count} </br>";
    					
    				}catch (Exception $e){
    					echo "ERROR: ".$e->getMessage();exit();
    				}
    			}
    			
    		}
    		exit("*** END ACTION ***");
    	}
    	else{
    		exit("Access Denied. Need to be root");
    	}
    }

}

