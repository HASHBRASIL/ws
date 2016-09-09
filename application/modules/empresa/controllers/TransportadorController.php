<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Empresa_TransportadorController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Empresa_Model_Bo_Transportador
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Empresa_Model_Bo_Transportador();
        parent::init();
    }


    public function _initForm()
    {
        $estadoBo = new Sis_Model_Bo_Estado();
        $this->view->comboEstado = array('' => 'Selecione')+$estadoBo->getPairs(false);
    }
}