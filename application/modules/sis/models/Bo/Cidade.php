<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Sis_Model_Bo_Cidade extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_Cidade
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_Cidade();
        parent::__construct();
    }

    public function getPairsCidade( $where = null,  $chave = null,
             $valor = null, $ordem = null, $limit = null )
    {
        return $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
    }
}