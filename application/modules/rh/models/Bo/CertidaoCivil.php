<?php
/**
 * @author Vinicius Leônidas
* @since 06/01/2014
*/
class Rh_Model_Bo_CertidaoCivil extends App_Model_Bo_Abstract
{
	protected $_dao;

	public function __construct(){
		$this->_dao = new Rh_Model_Dao_CertidaoCivil();
		parent::__construct();
	}

	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		if (!empty($request['dt_emissao'])) {
			$object['dt_emissao'] = $this->date($request['dt_emissao'], 'yyyy-MM-dd');
		}
	}
	
}
