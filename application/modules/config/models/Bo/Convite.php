<?php

class Config_Model_Bo_Convite extends App_Model_Bo_Abstract
{
    protected $_dao;
    
    public function __construct()
    {
        $this->_dao = new Config_Model_Dao_Convite();
        $this->_daoRgl = new Config_Model_Dao_RlGrupoPessoa();
        parent::__construct();
    }

    public function getConvitesAprovacaoTime($time) {
        return $this->_dao->getConvitesAprovacaoTime($time);
    }
    
    public function getConvitesAprovacaoTimeGrid($time) {
        return $this->_dao->getConvitesAprovacaoTimeGrid($time);
    }
    
    public function mudaStatusConvite($id, $status) {
        return $this->_dao->mudaStatusConvite($id, $status);
    }

}