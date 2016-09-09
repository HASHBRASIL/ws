<?php

class Auth_MenuController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Auth_Model_Bo_Menu
     */
    protected $_bo;
    
    public function init()
    {
        parent::init();
    	$this->_helper->layout()->setLayout('metronic');
        $this->_bo = new Auth_Model_Bo_Menu();
        $this->_messageBroker = App_Validate_MessageBroker::getInstance();
        $this->_redirectDelete = ("/auth/menu/grid");
        $this->_id = $this->getRequest()->getParam('id_au_menu');
    }
    
    public function _initForm(){
    	
    	$resourceBo = new Resource_Model_Bo_Resource();
    	$resourcePairsList = $resourceBo->getPairs();
    	
    	$this->view->resourceList = $resourcePairsList;
    	
    	if ($this->getParam("id_au_parent_menu")){
    		$this->view->id_au_parent_menu = $this->getParam("id_au_parent_menu");
    	}
    	
    }
    
    public function gridAction(){
    	$this->view->menuListPai = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_au_parent_menu IS NULL"));
    }


}

