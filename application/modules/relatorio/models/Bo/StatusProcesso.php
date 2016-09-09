<?php

class Relatorio_Model_Bo_StatusProcesso extends App_Model_Bo_Abstract
{
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Relatorio_Model_Dao_StatusProcesso();
        parent::__construct();
    }

    public function getPairsOrdemDesc()
    {
        return $this->_dao->getPairsOrdemDesc();
    }
}