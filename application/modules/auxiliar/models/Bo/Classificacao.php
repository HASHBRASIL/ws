<?php
class Auxiliar_Model_Bo_Classificacao extends App_Model_Bo_Abstract
{
	protected $_dao;
	public function __construct()
	{
		$this->_dao = new Auxiliar_Model_Dao_Classificacao();
		parent::__construct();
	}
	
}