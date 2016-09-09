<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  15/07/2013
 */
class Sis_Model_Bo_Cargo extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_Cargo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_Cargo();
        parent::__construct();
    }

    /**
     * @param boolean $ativo
     * @return array ($key => $value)
     */
    public function getPairs($ativo = true, $chave = null, $valor = null,
            $ordem = null, $limit = null )
    {
        $where = null;
        if($ativo){
            $where = array("car_ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }
        return $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
    }

}