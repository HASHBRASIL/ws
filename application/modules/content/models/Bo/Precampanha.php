<?php

class Content_Model_Bo_Precampanha extends App_Model_Bo_Abstract
{
    /**
     * @var Content_Model_Bo_Precampanha
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Content_Model_Dao_Precampanha();
        parent::__construct();
    }

    public function getSelectGrid($time,$campo) {
        return $this->_dao->getSelectGrid($time,$campo);
    }

    public function getCountSelectGrid($time,$campo) {
        return $this->_dao->getCountSelectGrid($time,$campo);
    }

    public function getParCandidatoColigacao ($ibPai, $cargo) {
        return $this->_dao->getParCandidatoColigacao($ibPai,$cargo);
    }
    
    public function getParCandidatoSemColigacao ($ibPai, $cargo) {
        return $this->_dao->getParCandidatoSemColigacao($ibPai,$cargo);
    }
}