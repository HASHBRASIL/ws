<?php
class Auxiliar_Model_Bo_TbInformacao extends App_Model_Bo_Abstract
{
	protected $_dao;
	public function __construct()
	{
		$this->_dao = new Auxiliar_Model_Dao_TbInformacao();
		parent::__construct();
	}
	
}