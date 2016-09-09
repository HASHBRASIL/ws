<?php
class Service_Model_Bo_TipoServico extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_TipoServico
     */
    protected $_dao;

    const INTERNO = 1;
    const EXTERNO = 2;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_TipoServico();
        parent::__construct();
    }
}