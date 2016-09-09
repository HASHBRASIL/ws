<?php
/**
 * @author Vinicius Leônidas
* @since 14/01/2014
*/
class Rh_Model_Bo_Local extends App_Model_Bo_Abstract
{
	protected $_dao;

	public function __construct(){
		$this->_dao = new Rh_Model_Dao_Local();
		parent::__construct();
	}

}
