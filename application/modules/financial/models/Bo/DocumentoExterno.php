<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Bo_DocumentoExterno extends App_Model_Bo_Abstract
{
    /**
     * @var Financial_Model_Dao_DocumentoExterno
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Financial_Model_Dao_DocumentoExterno();
        parent::__construct();
    }
    
    public function getPairs($ativo = true, $chave = null, $valor = null,
    		$ordem = null, $limit = null )
    {
    	$where = null;
    	if($ativo){
    		$where = array("tie_ativo = ?" => App_Model_Dao_Abstract::ATIVO);
    	}
    	return $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
    }
    
    
}