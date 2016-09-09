<?php

class Relatorio_Model_Bo_StatusFinanceiro extends App_Model_Bo_Abstract
{
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Relatorio_Model_Dao_StatusFinanceiro();
        parent::__construct();
    }

}