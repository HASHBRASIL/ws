<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/12/2013
 */
class Processo_PrioridadeController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_Prioridade
     */
    protected $_bo;

    public function init()
    {
    	$this->_bo = new Processo_Model_Bo_Prioridade();
        parent::init();
    }

}