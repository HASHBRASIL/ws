<?php

class Relatorio_Model_Bo_Financeiro extends App_Model_Bo_Abstract
{
    /**
     * @var Relatorio_Model_Dao_Financeiro
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Relatorio_Model_Dao_Financeiro();
        parent::__construct();
    }

    public function getFinanceiroRelatorio($cond,$resSql = false)
    {
        return $this->_dao->getFinanceiroRelatorio($cond,$resSql);
    }

    public function getFinanceiroRecibo($cond,$resSql = false)
    {
        return $this->_dao->getFinanceiroRecibo($cond,$resSql);
    }

    public function processaTransacao ($rowAgrupador, $data, $tipo)
    {
        return $this->_dao->processaTransacao($rowAgrupador, $data, $tipo);
    }

}