<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 24/07/2014
 */
class Rh_Model_Bo_Horario extends App_Model_Bo_Abstract
{
	/**
	 * @var Rh_Model_Dao_Horario
	 */
	protected $_dao;
	
	public function __construct(){
		$this->_hasWorkspace = true;
		
		$this->_dao = new Rh_Model_Dao_Horario();
		parent::__construct();
	}

	protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		if(empty($object->nome)){
			App_Validate_MessageBroker::addErrorMessage('O campo de nome está vazio.');
			return false;
		}
		$criteria = array('nome = ?' => $object->nome, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);
		if(!empty($object->id_horario)){
			$criteria['id_horario <> ?'] = $object->id_horario;
		}
		if(count($this->find($criteria)) > 0){
			App_Validate_MessageBroker::addErrorMessage('Este nome já foi cadastrado.');
			return false;
		}
		return true;
	}
	
}
