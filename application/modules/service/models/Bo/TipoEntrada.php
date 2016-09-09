<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/05/2013
 */
class Service_Model_Bo_TipoEntrada extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_TipoEntrada
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_TipoEntrada();
        parent::__construct();
    }
}