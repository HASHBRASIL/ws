<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 02/07/2013
 */
class Service_TipoComponenteController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Service_Model_Bo_TipoComponente
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Service_Model_Bo_TipoComponente();
        parent::init();
    }
}