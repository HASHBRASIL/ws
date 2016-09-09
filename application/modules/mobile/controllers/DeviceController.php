<?php

class Mobile_DeviceController extends App_Controller_Action_AbstractCrud
{

    public function init()
    {
    	parent::init();
    	$this->_messageBroker = App_Validate_MessageBroker::getInstance();
    	header('Access-Control-Allow-Origin: *');
    	$this->_bo = new Mobile_Model_Bo_Device();
    	$this->_helper->layout()->setLayout('metronic');
    	$this->_id = $this->getParam("id_device");
    	$this->_redirectDelete = "mobile/device/grid";
    }
    
    public function gridAction(){
    	
    	$this->view->deviceList = $this->_bo->find(array('ativo'=>App_Model_Dao_Abstract::ATIVO));
    }
    
}

