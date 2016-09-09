<?php
/**
 * @author Vinicius Leônidas
 * @since 03/12/2013
 */
class Rh_Model_Bo_ModeloSintetico extends App_Model_Bo_Abstract
{
	protected $_dao;
	
	public function __construct(){
		$this->_dao = new Rh_Model_Dao_ModeloSintetico();
		parent::__construct();
	}
	
	protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		if (!empty($object->codigo)) {
			$criteria = array('codigo = ?' => $object->codigo, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_rh_modelo_sintetico <> ?' => $object->id_rh_modelo_sintetico);
		} else {
			$criteria = array('codigo = ?' => $object->codigo, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);
		}
		
		$modelo = $this->find($criteria)->current();
		if(count($modelo) > 0){
			App_Validate_MessageBroker::addErrorMessage("Este modelo já se encontra cadastrado.");
			return false;
		}
		
		if(empty($object->descricao)){
			App_Validate_MessageBroker::addErrorMessage('Campo nome é obrígatorio.');
			return false;
		}
		if(empty($object->codigo)){
			App_Validate_MessageBroker::addErrorMessage('Campo codígo é obrígatorio.');
			return false;
		}
		if(empty($object->id_rh_entrada_sintetico)){
			App_Validate_MessageBroker::addErrorMessage('Campo entrada é obrígatorio.');
			return false;
		}
		if(empty($object->id_rh_natureza_sintetico)){
			App_Validate_MessageBroker::addErrorMessage('Campo natureza é obrígatorio.');
			return false;
		}
		return true;
	}
	
	public function verificarModelo($data){
	
		$resposta = array();
		$modelo = $this->find(array('id_rh_modelo_sintetico = ?' => $data, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO))->current();
		
		if ($modelo == null) {
			return $resposta = array("success" => true, "resposta" => false, "message" => "Modelo Sitentico Invalido");
		}
		
		return $resposta = array("success" => true, "resposta" => true);
	}
	
	public function getAutocompleteModelo($term, $tipo, $ativo = true, $chave = null, $valor = null,
			$ordem = null, $limit = null )
	{
		$where = null;
		if($ativo){
			$where = array("ms.ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_rh_natureza_sintetico IN ('3','{$tipo}')" => "");
		}
		$teste = $this->_dao->getAutocompleteModelo($term, $chave, $valor, $where, $ordem, $limit);
		$autoCompletes = array();
		foreach ($teste as $key => $autoComplete) {
			$autoCompletes[] = array('value' => $autoComplete['codigo'].' '.$autoComplete['value'], 'id' => $autoComplete['id'], 'label' => $autoComplete['codigo'].' '.$autoComplete['label'], 'referencia' => $autoComplete['nome']);
		}
		return $autoCompletes;
	}
}