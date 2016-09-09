<?php
class Service_Model_Bo_ServicoCentroCusto extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_ServicoCentroCusto
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_ServicoCentroCusto();
        parent::__construct();
    }

    public function deleteByServico($idServico){
        return $this->_dao->deleteByServico($idServico);
    }

    public function getCentroCustoByServico($idServico)
    {
        return $this->_dao->getCentroCustoByServico($idServico);
    }
}