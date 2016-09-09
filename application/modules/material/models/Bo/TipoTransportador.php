<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  08/06/2013
 */
class Material_Model_Bo_TipoTransportador extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_TipoTransportador
     */
    protected $_dao;

    const EMPRESA     = 1;
    const FUNCIONARIO = 2;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_TipoTransportador();
        parent::__construct();
    }
}