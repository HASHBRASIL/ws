<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  09/07/2013
 */
class Processo_AberturaController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_Abertura
     */
    protected $_bo;

    public function init()
    {
    	$this->_bo = new Processo_Model_Bo_Abertura();
        parent::init();
    }

}