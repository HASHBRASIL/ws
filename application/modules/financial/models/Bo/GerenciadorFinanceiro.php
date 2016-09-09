<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  04/12/2013
 */
class Financial_Model_Bo_GerenciadorFinanceiro extends App_Model_Bo_Abstract
{
    /**
     * @var Financial_Model_Dao_GerenciadorFinanceiro
     */
	public function __construct()
	{
		$this->_dao = new Financial_Model_Dao_GerenciadorFinanceiro();
		parent::__construct();
	}
	
}