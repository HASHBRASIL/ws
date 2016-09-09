<?php
class Empresa_CaracteristicaController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Empresa_Model_Bo_Caracteristica
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Empresa_Model_Bo_Caracteristica();
        $this->_helper->layout()->setLayout('metronic');
        parent::init();
    }

}