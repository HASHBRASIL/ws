<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Sis_Model_Bo_Indicacao extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_Indicacao
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_Indicacao();
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
            $where = array("ind_ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }
        return $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
    }

}