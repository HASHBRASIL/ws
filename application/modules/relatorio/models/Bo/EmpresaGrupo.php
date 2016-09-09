<?php
class Relatorio_Model_Bo_EmpresaGrupo extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_dao_EmpresaGrupo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_dao_EmpresaGrupo();
        parent::__construct();
    }

}