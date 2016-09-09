<?php

class Service_IndexController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Auth_Model_Bo_Usuario
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Auth_Model_Bo_Usuario();
        parent::init();
    }

    public function indexAction()
    {
    }

}

