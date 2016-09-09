<?php
class Auth_Model_Bo_Pessoal extends App_Model_Bo_Abstract
{
    /**
     * @var Auth_Model_Dao_Pessoal
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Auth_Model_Dao_Pessoal();
    }

    /**
     * @desc busca todos os dados da pessoa pelo cpf
     * @param int $cpf
     * @return array assoc
     */
    public function findPessoaByCpf($cpf)
    {
        return $this->_dao->findPessoaByCpf($cpf);
    }

}