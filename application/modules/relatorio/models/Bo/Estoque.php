<?php
class Relatorio_Model_Bo_Estoque extends App_Model_Bo_Abstract{
    protected  $_dao;

    public function __construct(){
        $this->_dao = new Relatorio_Model_Dao_Estoque();

    }

    public function getQtdRegistros($dataInicial, $dataFinal, $data_condicao){
        return $this->_dao->getQtdRegistros($dataInicial, $dataFinal, $data_condicao);
    }

}