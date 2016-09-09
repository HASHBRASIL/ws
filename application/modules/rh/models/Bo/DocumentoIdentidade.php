<?php
/**
 * @author Vinicius Leônidas
* @since 06/01/2014
*/
class Rh_Model_Bo_DocumentoIdentidade extends App_Model_Bo_Abstract
{
	protected $_dao;

	public function __construct(){
		$this->_dao = new Rh_Model_Dao_DocumentoIdentidade();
		parent::__construct();
	}
	
	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		
		if (!empty($request['expedida'])) {
			$object['expedida'] = $this->date($request['expedida'], 'yyyy-MM-dd');
		}
		if (!empty($request['ctps_expedida'])) {
			$object['ctps_expedida'] = $this->date($request['ctps_expedida'], 'yyyy-MM-dd');
		}
		if (!empty($request['dt_opta_pis'])) {
			$object['dt_opta_pis'] = $this->date($request['dt_opta_pis'], 'yyyy-MM-dd');
		}
	}
	
	protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		$dadosPonto = new Rh_Model_Bo_DadosPonto();
		$dadosPonto->updateIdFuncionario($request['pis'], $object->id_rh_funcionario);
	}

}
