<?php
class Service_Model_Bo_Tarefa extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_Tarefa
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_Tarefa();
        parent::__construct();
    }

    public function getListTarefa($idServico)
    {
        return $this->_dao->getListTarefa($idServico);
    }

    public function inativarAll($idServico)
    {
        return $this->_dao->inativarAll($idServico);
    }
}