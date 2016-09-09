<?php
/**
 * @author Vinicius Leônidas
 * @since 17/12/2013
 */
class Rh_Model_Bo_JustificativaPonto extends App_Model_Bo_Abstract{
	
	protected $_dao;
	
	public function __construct(){
		$this->_dao = new Rh_Model_Dao_JustificativaPonto();
		parent::__construct();
	}

	public function getJustificativa($where){
	
		return $this->_dao->getJustificativa($where);
	
	}
}