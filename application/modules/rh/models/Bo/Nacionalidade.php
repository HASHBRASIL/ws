<?php
/**
 * @author Vinicius Leônidas
 * @since 02/01/2014
 */
class Rh_Model_Bo_Nacionalidade extends App_Model_Bo_Abstract
{
	protected $_dao;
	
	public function __construct(){
		$this->_dao = new Rh_Model_Dao_Nacionalidade();
		parent::__construct();
	}
	
}
