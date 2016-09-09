<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  23/07/2013
 */
class Empresa_Model_Bo_CaracteristicaEmpresa extends App_Model_Bo_Abstract
{
    /**
     * @var Empresa_Model_Dao_CaracteristicaEmpresa
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Empresa_Model_Dao_CaracteristicaEmpresa();
        parent::__construct();
    }

    public function deleteByEmpresa($idEmpresa)
    {
        return $this->_dao->deleteByEmpresa($idEmpresa);
    }

    public function getIdPerfilByEmpresa($idEmpresa)
    {
        if($idEmpresa){
            return $this->_dao->getIdPerfilByEmpresa($idEmpresa);
        }
        return null;
    }
    
    public function delete($idEmpresa, $idCaracteristica)
    {
    	return $this->_dao->deleteCaracteristica($idEmpresa, $idCaracteristica);
    }


}