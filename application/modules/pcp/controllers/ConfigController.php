<?php

class Pcp_ConfigController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Pcp_Model_Bo_Pcp
     */
    protected $_bo;
	
    public function init()
    {
        parent::init();
        $this->_bo = new Pcp_Model_Bo_Config();
        $this->_helper->layout()->setLayout('metronic');
        $this->_id = $this->getParam("id_config_empresa");
    }
    
    public function _initForm(){
    	
    	$empresaBo = new Empresa_Model_Bo_Empresa();
    	$this->view->funcionarioList = $empresaBo->getFuncionarioPairs();
    	
    }
    
    public function gridAction(){
    	
    	$this->view->configList = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO));
    	
    }

}

