<?php
class Service_Model_Bo_TipoUnidade extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_TipoUnidade
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_TipoUnidade();
        parent::__construct();
    }
}