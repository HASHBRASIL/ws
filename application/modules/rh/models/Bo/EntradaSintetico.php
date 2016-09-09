<?php
/**
 * @author Vinicius Leônidas
 * @since 03/12/2013
 */
class Rh_Model_Bo_EntradaSintetico extends App_Model_Bo_Abstract
{
	protected $_dao;
	
	public function __construct(){
		$this->_dao = new Rh_Model_Dao_EntradaSintetico();
		parent::__construct();
	}
	
}
