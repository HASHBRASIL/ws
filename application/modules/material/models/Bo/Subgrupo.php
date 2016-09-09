<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Material_Model_Bo_Subgrupo extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Subgrupo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Subgrupo();
        parent::__construct();
    }

    public function getPairsGrupo( $where, $chave=null, $valor=null, $ordem=null, $limit=null)
    {
        return $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
    }

}