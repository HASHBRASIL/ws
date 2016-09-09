<?php

class Config_Model_Bo_RlPerfilInformacao extends App_Model_Bo_Abstract
{
    /**
     * @var Config_Model_Dao_RlPerfilInformacao
     */
    protected $_dao;
    
    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Config_Model_Dao_RlPerfilInformacao();
        parent::__construct();
    }

    public function getByInformacao($idtinf) {
        return $this->_dao->getByInformacao($idtinf);
    }

    public function getByInformacaoMultiplo($idtinf) {
        return $this->_dao->getByInformacaoMultiplo($idtinf);
    }
    
    public function getByPerfil($idPerfil) {
        return $this->_dao->getByPerfil($idPerfil);
    }

    public function getByPerfisMultiplo($strPerfis) {
        return $this->_dao->getByPerfisMultiplo($strPerfis);
    }
}