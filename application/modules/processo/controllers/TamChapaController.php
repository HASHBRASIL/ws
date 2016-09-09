<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  09/07/2013
 */
class Processo_TamChapaController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_TamChapa
     */
    protected $_bo;

    public function init()
    {
    	$this->_bo = new Processo_Model_Bo_TamChapa();
        parent::init();
    }

}