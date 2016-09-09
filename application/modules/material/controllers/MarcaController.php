<?php
/**
 * @author Ellyson de Jesus Silva
* @since 10/04/2013
*/
class Material_MarcaController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Material_Model_Bo_Marca
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Material_Model_Bo_Marca();
        parent::init();
    }

}