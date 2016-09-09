<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 10/04/2013
 */
class Material_CompraController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Material_Model_Bo_Compra
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Material_Model_Bo_Compra();
        parent::init();
    }
}