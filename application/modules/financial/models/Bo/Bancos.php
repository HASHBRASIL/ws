<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  04/07/2013
 */
class Financial_Model_Bo_Bancos extends App_Model_Bo_Abstract
{
    /**
     * @var Financial_Model_Dao_Bancos
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Financial_Model_Dao_Bancos();
        parent::__construct();
    }

    public function getPairs($ativo = true, $chave = null, $valor = null,
    		$ordem = null, $limit = null )
    {
    	$where = null;
    	if($ativo){
    		$where = array("bco_ativo = ?" => App_Model_Dao_Abstract::ATIVO);
    	}
    	return $this->_dao->fetchPairs($chave, $valor, $where, 'bco_nome', $limit);
    }


}