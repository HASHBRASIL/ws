<?php

class Relatorio_Model_Bo_Funcionario extends App_Model_Bo_Abstract
{
    /**
     * @var Relatorio_Model_Dao_Pessoa
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Relatorio_Model_Dao_Funcionario();
        parent::__construct();
    }

    public function getFuncionario()
    {
        $registrosFuncionario =  $this->_dao->getFuncionario();
        if (isset($registrosFuncionario[0])){
            return $registrosFuncionario;
        }
        else {
            return null;
        }
    }

    public function getFuncionariosCondicao(){

        $retorno = $this->_dao->getListFuncionarios();

        return $retorno;

    }


}