<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 11/08/2014
 */
class Rh_Model_Bo_ConfiguracaoUsuario extends App_Model_Bo_Abstract
{
	/**
	 * @var Rh_Model_Dao_ConfiguracaoUsuario
	 */
	protected $_dao;
	
	const NIVEL1 = 1;
	const NIVEL2 = 2;
	
	public function __construct()
	{
		$this->_dao = new Rh_Model_Dao_ConfiguracaoUsuario();
		parent::__construct();
	}

	public function deleteByConfiguracao($idConfiguracao)
	{
		return $this->_dao->deleteByConfiguracao($idConfiguracao);
	}
	
}
