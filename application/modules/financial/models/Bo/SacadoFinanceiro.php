<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  17/06/2013
 */
class Financial_Model_Bo_SacadoFinanceiro extends App_Model_Bo_Abstract
{
    /**
     * @var Financial_Model_Dao_SacadoFinanceiro
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Financial_Model_Dao_SacadoFinanceiro();
        parent::__construct();
    }
    public function getFinanceiroRh($idEmpresa, $data){
    	$fin_vencimento = new Zend_Date($data);
    	$data = $fin_vencimento->toString('yyyy-MM-dd');
    	return $this->_dao->getFinanceiroRh($idEmpresa, $data);
    }
    
}