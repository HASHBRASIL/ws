<?php

    /**
     * Created by PhpStorm.
     * User: solbisio
     * Date: 17/12/15
     * Time: 23:52
     */
    class Legacy_Model_Bo_RlPermissaoPessoa extends App_Model_Bo_Abstract
    {
        /**
         * @var Legacy_Model_Dao_RlPermissaoPessoa
         */
        protected $_dao;

        public function __construct()
        {
            $this->_dao = new Legacy_Model_Dao_RlPermissaoPessoa();
            parent::__construct();
        }

        /**
         * @param uuid $idPessoa
         * @return Zend_Db_Table_Rowset_Abstract
         */
        public function getByIdPessoa($idPessoa)
        {
            $rs = $this->find(array("id_pessoa = ?" => $idPessoa, 'dt_expiracao > now()'));

            return $rs;
        }

        public function getServicosByUsuarioByTime($idUsuario, $idTime)
        {
            $rs = $this->_dao->getServicosByUsuarioByTime($idUsuario, $idTime);

            return $rs;
        }

        public function salvarPermissao($idUsuario, $idTime, $servicos, $dtExpiracao)
        {
           $this->_dao->salvarPermissao($idUsuario, $idTime, $servicos, $dtExpiracao);
        }

    }