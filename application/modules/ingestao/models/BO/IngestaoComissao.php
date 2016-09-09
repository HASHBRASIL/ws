<?php
/**
 * @author Fernando Augusto
 * @since  18/05/2016
 */
class Ingestao_Model_Bo_IngestaoComissao extends App_Model_Bo_Abstract
{
    /**
     * @var Content_Model_Dao_ItemBiblioteca
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Ingestao_Model_Dao_IngestaoComissao();
        parent::__construct();
    }

    public function todos() {
        return $this->_dao->fetch();
    }
}