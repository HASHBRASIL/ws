<?php

    /**
     * Created by PhpStorm.
     * User: solbisio
     * Date: 17/12/15
     * Time: 23:52
     */
    class Legacy_Model_Bo_Grupo extends App_Model_Bo_Abstract
    {
        /**
         * @var Legacy_Model_Dao_Grupo
         */
        protected $_dao;

        public function __construct()
        {
            $this->_dao = new Legacy_Model_Dao_Grupo();
            parent::__construct();
        }

        public function getTimesId($idPessoa)
        {
            return $this->_dao->getTimesId($idPessoa);
        }

        public function getGruposId($idPessoa, $idGrupoTime)
        {
            return $this->_dao->getGruposId($idPessoa, $idGrupoTime);
        }

        public function getGroupList($idTime)
        {
            return $this->_dao->getGroupList($idTime);
        }

        public function getGroups($idTimes)
        {
            return $this->_dao->getGroups($idTimes);
        }

        public function getSiteByCriador($idCriador)
        {
            return $this->_dao->getSiteByCriador($idCriador);
        }

        public function getCrachaBySite($idSite)
        {
            return $this->_dao->getCrachaBySite($idSite);
        }


        public function getGrupoByMetanome($metanome, $idCriador)
        {
            return $this->_dao->getGrupoByMetanome($metanome, $idCriador);
        }

        public function getTimeByCriador($idCriador)
        {
            return $this->_dao->getTimeByCriador($idCriador);
        }

        public function getAlcada($time) {
            $ret = array();

            $timeCima = $this->_dao->getAlcadaAcima($time);
            $arralcada = array();
            foreach($timeCima as $itmCima) {
                $arralcada[] = $itmCima['id'];
            }
            $timeBaixo = $this->_dao->getAlcadaAbaixo($time);
            foreach($timeBaixo as $itmBxo) {
                $arralcada[] = $itmBxo['id'];
            }
            $timeLado = $this->_dao->getAlcadaLado($time);
            foreach($timeLado as $itmLado) {
                $arralcada[] = $itmLado['id'];
            }

            $timeAlcadaInicial = array_unique();

            $ret = $this->_dao->getAlcadaInsc($timeAlcadaInicial);
        }

        public function getLicense()
        {

            $ret = $this->_dao->getLicense();
            return $ret;
        }

    }
