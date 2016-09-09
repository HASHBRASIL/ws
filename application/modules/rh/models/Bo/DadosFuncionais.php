<?php
/**
 * @author Vinicius Leônidas
* @since 06/01/2014
*/
class Rh_Model_Bo_DadosFuncionais extends App_Model_Bo_Abstract
{
	protected $_dao;

	public function __construct(){
		$this->_dao = new Rh_Model_Dao_DadosFuncionais();
		parent::__construct();
	}

	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		$object->salario = $this->_formatDecimal($request['salario']);

		if (!empty($request['vencto_atestado'])) {
			$request['vencto_atestado'] = $this->date($request['vencto_atestado'], 'yyyy-MM-dd');
		}

		if (!empty($request['dt_inicio_escala'])) {
			$request['dt_inicio_escala'] = $this->date($request['dt_inicio_escala'], 'yyyy-MM-dd');
		}

		if (!empty($request['dt_ultima_ferias'])) {
			$request['dt_ultima_ferias'] = $this->date($request['dt_ultima_ferias'], 'yyyy-MM-dd');
		}

		if (!empty($request['dt_previcao_ferias'])) {
			$request['dt_previcao_ferias'] = $this->date($request['dt_previcao_ferias'], 'yyyy-MM-dd');
		}
	}

}
