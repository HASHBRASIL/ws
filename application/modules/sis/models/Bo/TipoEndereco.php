<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  29/04/2013
 */
class Sis_Model_Bo_TipoEndereco extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_TipoEndereco
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_TipoEndereco();
    }
}