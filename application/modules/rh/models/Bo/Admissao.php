<?php
/**
 * @author Vinicius Leônidas
* @since 06/01/2014
*/
class Rh_Model_Bo_Admissao extends App_Model_Bo_Abstract
{
	protected $_dao;

	public function __construct(){
		$this->_dao = new Rh_Model_Dao_Admissao();
		parent::__construct();
	}
	
	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		if (!empty($request['dt_admissao'])) {
			$object['dt_admissao'] = $this->date($request['dt_admissao'], 'yyyy-MM-dd');
		}
		
		if (!empty($request['dt_vencto_experiencia'])) {
			$object['dt_vencto_experiencia'] = $this->date($request['dt_vencto_experiencia'], 'yyyy-MM-dd');
		}
		
		if (!empty($request['dt_prorrog_experiencia'])) {
			$object['dt_prorrog_experiencia'] = $this->date($request['dt_prorrog_experiencia'], 'yyyy-MM-dd');
		}
		
		if (!empty($request['dt_vencto_prazo'])) {
			$object['dt_vencto_prazo'] = $this->date($request['dt_vencto_prazo'], 'yyyy-MM-dd');
		}
		
		if (!empty($request['dt_formacao_prof'])) {
			$object['dt_formacao_prof'] = $this->date($request['dt_formacao_prof'], 'yyyy-MM-dd');
		}
		
		if (!empty($request['dt_transferencia'])) {
			$object['dt_transferencia'] = $this->date($request['dt_transferencia'], 'yyyy-MM-dd');
		}
		
		if (!empty($request['dt_ultima_reciclagem'])) {
			$object['dt_ultima_reciclagem'] = $this->date($request['dt_ultima_reciclagem'], 'yyyy-MM-dd');
		}
	}

}
