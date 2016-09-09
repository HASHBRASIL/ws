<?php
/**
 * @author Vinicius Leônidas
 * @since 17/02/2014
 */
class Rh_Model_Bo_Feriado extends App_Model_Bo_Abstract
{
	protected $_dao;
	
	public function __construct(){
		$this->_dao = new Rh_Model_Dao_Feriado();
		parent::__construct();
	}
	
	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		$dt_admissao = new Zend_Date($request['data']);		$dt_admissao = new Zend_Date($request['data']);
		$object->data = $dt_admissao->toString('yyyy-MM-dd');
	}
	
	public function hasFeriado($data)
	{
		$feriado = $this->find(array("data = ?" => $data, "ativo = ?" => App_Model_Dao_Abstract::ATIVO))->current();
		if(!empty($feriado)){
			return true;
		}
		return false;
	}
}
