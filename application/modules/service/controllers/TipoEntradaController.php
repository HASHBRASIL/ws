<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/05/2013
 */
class Service_TipoEntradaController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Service_Model_Bo_TipoEntrada
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Service_Model_Bo_TipoEntrada();
        parent::init();
    }

}