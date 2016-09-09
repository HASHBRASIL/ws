<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Empresa_Model_Bo_EmpresaGrupo extends App_Model_Bo_Abstract
{
    /**
     * @var Empresa_Model_Dao_EmpresaGrupo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Empresa_Model_Dao_EmpresaGrupo();
        parent::__construct();
    }

    public function getSelect(){
        return $this->_dao->fetchAll();

    }

}