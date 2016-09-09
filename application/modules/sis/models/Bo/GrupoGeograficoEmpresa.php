<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  15/07/2013
 */
class Sis_Model_Bo_GrupoGeograficoEmpresa extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_GrupoGeograficoEmpresa
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_GrupoGeograficoEmpresa();
        parent::__construct();
    }


    public function deleteByEmpresa($idEmpresa)
    {
        return $this->_dao->deleteByEmpresa($idEmpresa);
    }

    public function getIdGrupoByGrupo($idEmpresa)
    {
        if($idEmpresa){
            return $this->_dao->getIdGrupoByEmpresa($idEmpresa);
        }
        return null;

    }

}