<?php
class Service_Model_Bo_ServicoEmpresa extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_ServicoEmpresa
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_ServicoEmpresa();
        parent::__construct();
    }

    public function deleteByServico($idServico){
        return $this->_dao->deleteByServico($idServico);
    }

    public function getFornecedorByServico($idServico)
    {
        return $this->_dao->getFornecedorByServico($idServico);
    }
}