<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Empresa_Model_Bo_Portal extends App_Model_Bo_Abstract
{
    /**
     * @var Empresa_Model_Dao_Portal
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Empresa_Model_Dao_Portal();
        parent::__construct();
    }

    /**
     * sobrescrevendo o metodo do pai pois o ativo da tabela foge do padrão pois foi construido
     * antes do novo padrão
     * @see App_Model_Bo_Abstract::getPairs()
     */
    public function getPairs($ativo = true, $chave = null, $valor = null,
            $ordem = null, $limit = null )
    {
        $where = null;
        if($ativo){
            $where = array("poc_ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }
        return $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
    }

}