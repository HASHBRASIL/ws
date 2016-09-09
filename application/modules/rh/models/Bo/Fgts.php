<?php
/**
 * @author Vinicius Leônidas
* @since 06/01/2014
*/
class Rh_Model_Bo_Fgts extends App_Model_Bo_Abstract
{
	protected $_dao;

	public function __construct(){
		$this->_dao = new Rh_Model_Dao_Fgts();
		parent::__construct();
	}
	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		if (!empty($request['dt_opcao'])) {
			$object['dt_opcao'] = $this->date($request['dt_opcao'], 'yyyy-MM-dd');
		}
		if (!empty($request['dt_retratacao'])) {
			$object['dt_retratacao'] = $this->date($request['dt_retratacao'], 'yyyy-MM-dd');
		}
		
	}
	
}
