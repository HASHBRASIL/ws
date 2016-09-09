<?php

    class Content_Model_Bo_RlVinculoItem extends App_Model_Bo_Abstract
    {
        /**
         * @var Content_Model_Dao_RlVinculoItem
         */
        protected $_dao;

        /**
         * @var integer
         */
        public function __construct()
        {
            $this->_dao = new Content_Model_Dao_RlVinculoItem();
            parent::__construct();
        }

        public function relacionaItem($idItemPrincipal, $idItemVinculado) {

            $uuid = UUID::v4();

            $row = $this->_dao->createRow();
            $row->id       = $uuid;
            $row->id_ib_principal = $idItemPrincipal;
            $row->id_ib_vinculado  = $idItemVinculado;
            $row->save();

            return $row;
        }

    }
