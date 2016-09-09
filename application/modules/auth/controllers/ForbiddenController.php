<?php

class Auth_ForbiddenController extends App_Controller_Action_AbstractCrud
{

    public function preDispatch()
    {
    	$this->_authAnonymous = true;
    	parent::preDispatch();
    	
    }
    
	public function indexAction()
    {
    	
    	if ($this->getRequest()->isXmlHttpRequest()){
    		$this->_helper->json(array("success" => false, "mensagem" => "Não foi encontrado sua autorização de acesso para utilizar este recurso"));
    		//$this->view->xmlHttpRequest = true;
    	}
    	
    	$this->view->error = $this->getParam("error");
    	$this->_helper->layout->disableLayout();
        $user = Zend_Auth::getInstance()->getIdentity();
        
        if ($user->tps_id == 2/*fisica*/){
        	
        	$user = explode(" ", $user->nome_razao);
        	$this->view->user = $user[0];
        }
        
    }
    
    public function sessionAction()
    {
    	 
    	if ($this->getRequest()->isXmlHttpRequest()){
    		$this->_helper->json(array("success" => false, "mensagem" => "A sua sessão expirou devido a tempo ocioso"));
    	}
    	 
    	$this->_helper->layout->disableLayout();
    	$user = Zend_Auth::getInstance()->getIdentity();
    	
    	$timeSession = new Zend_Session_Namespace( 'timeSessionExpire' );
    	
    	$url = $timeSession->redirect;
    	$this->view->url = str_replace("/", ";", $url);
    	
    	$this->view->cpf_cnpj = Zend_Auth::getInstance()->getStorage()->read()->cnpj_cpf;
    	$this->view->idUser = $timeSession->idUser;
    	
    	if ($this->getParam("block")){
    		
    		$this->view->block = true;
    		$timeSession = new Zend_Session_Namespace( 'timeSessionExpire' );
    		$timeSession->limiteTime = 0;
    	}
    	
    	if ($user->tps_id == 2/*fisica*/){
    		 
    		$user = explode(" ", $user->nome_razao);
    		$this->view->user = $user[0];
    	}
    
    }


}

