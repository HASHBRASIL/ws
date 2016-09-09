<?php

class Pcp_TimerController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Pcp_Model_Bo_Timer
     */
    protected $_bo;
	
    public function init()
    {
        parent::init();
        $this->_bo = new Pcp_Model_Bo_Timer();
        $this->_helper->layout()->setLayout('metronic');
        $this->_id = $this->getParam("id_timer");
    }
    
    public function _initForm(){
    	
    }
    
    public function gridAction(){
    	
    	$this->view->timerList = $this->_bo->getTimerList();
    	
    }

}

