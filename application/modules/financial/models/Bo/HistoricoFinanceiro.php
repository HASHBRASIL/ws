<?php
/**
 * @author Vinicius Silva Pinto Leônidas
 * @since  18/03/2014
 */
class Financial_Model_Bo_HistoricoFinanceiro extends App_Model_Bo_Abstract
{
	public function __construct()
	{
		$this->_dao = new Financial_Model_Dao_HistoricoFinanceiro();
		parent::__construct();
	}
}