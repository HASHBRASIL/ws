<?php
/**
 * @author Ellyson de Jesus
 * @since 14/07/2014
 */
class Rh_Model_Bo_TipoPagamento extends App_Model_Bo_Abstract
{
	/**
	 * @var Rh_Model_Dao_TipoPagamento
	 */
	protected $_dao;
	
	const PRINCIPAL 	= 1;
	const COMPLEMENTAR  = 2;
	
	public function __construct(){
		$this->_dao = new Rh_Model_Dao_TipoPagamento();
		parent::__construct();
	}
	
}
