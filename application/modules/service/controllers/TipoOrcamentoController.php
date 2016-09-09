<?php
/**
 * @author: Ellyson de Jesus Silva
 * @since: 01/07/2013
 */
class Service_TipoOrcamentoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Service_Model_Bo_TipoOrcamento
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Service_Model_Bo_TipoOrcamento();
        parent::init();
    }
}