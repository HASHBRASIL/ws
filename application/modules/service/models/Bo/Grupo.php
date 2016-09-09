<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  01/04/2013
 */
class Service_Model_Bo_Grupo extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_Grupo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_Grupo();
    }

}