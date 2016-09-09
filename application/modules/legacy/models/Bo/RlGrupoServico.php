<?php

    /**
     * Created by PhpStorm.
     * User: solbisio
     * Date: 19/12/15
     * Time: 10:26
     */
    class Legacy_Model_Bo_RlGrupoServico extends App_Model_Bo_Abstract
    {
        /**
         * @var Legacy_Model_Dao_RlGrupoServico
         */
        protected $_dao;

        public function __construct()
        {
            $this->_dao = new Legacy_Model_Dao_RlGrupoServico();
            parent::__construct();
        }

        public function getModulosByTime($idTime)
        {
            return $this->_dao->getModulosByTime($idTime);
        }

        public function salvarModulos($idTime, $modulos)
        {
            return $this->_dao->salvarModulos($idTime, $modulos);

        }

//        /**
//         * @param uuid $idPessoa
//         * @return Zend_Db_Table_Rowset_Abstract
//         */
//        public function getByIdPessoa($idPessoa)
//        {
//            $rs = $this->find(array("id_pessoa = ?" => $idPessoa, 'dt_expiracao > now()'));
//
//            return $rs;
//        }
    }