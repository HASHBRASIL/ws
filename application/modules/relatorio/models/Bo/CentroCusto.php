<?php
class Relatorio_Model_Bo_CentroCusto extends App_Model_Bo_Abstract{
    protected $_Dao;

    public function __construct(){
        $this->_Dao = new Relatorio_Model_Dao_CentroCusto();
    }

    public function getRegistros(){
        return $this->_Dao->getRegistros();
    }

}