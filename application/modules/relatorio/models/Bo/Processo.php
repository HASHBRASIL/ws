<?php

class Relatorio_Model_Bo_Processo extends App_Model_Bo_Abstract
{
    /**
     * @var Relatorio_Model_Dao_Processo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Relatorio_Model_Dao_Processo();
        parent::__construct();
    }

    public function getProcessoRelatorio($cond,$resSql = false)
    {
        return $this->_dao->getProcessoRelatorio($cond,$resSql);
    }


}