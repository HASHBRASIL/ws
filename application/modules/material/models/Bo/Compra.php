<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 10/04/2013
 */
class Material_Model_Bo_Compra extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Compra
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Compra();
        parent::__construct();
    }

}
