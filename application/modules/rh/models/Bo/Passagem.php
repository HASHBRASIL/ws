<?php
/**
 * @author Vinicius Leônidas
 * @since 06/01/2014
 */
class Rh_Model_Bo_Passagem extends App_Model_Bo_Abstract
{
	protected $_dao;
	
	public function __construct(){
		$this->_dao = new Rh_Model_Dao_Passagem();
		parent::__construct();
	}
	
}
