<?php
/**
 * @author: Ellyson de Jesus Silva
 * @since: 02/07/2013
 */
class Service_Model_Bo_TipoComponente extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_TipoComponente
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_TipoComponente();
        parent::__construct();
    }
}