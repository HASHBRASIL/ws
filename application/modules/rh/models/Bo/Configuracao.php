<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 11/08/2014
 */
class Rh_Model_Bo_Configuracao extends App_Model_Bo_Abstract
{
	/**
	 * @var Rh_Model_Dao_Configuracao
	 */
	protected $_dao;
	
	public function __construct(){
		$this->_hasWorkspace = true;
		$this->_dao = new Rh_Model_Dao_Configuracao();
		parent::__construct();
	}

	protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		$request = new Zend_Controller_Request_Http();
		$params = $request->getParams();
		if(empty($object->dia_inicio_folha)){
			App_Validate_MessageBroker::addErrorMessage('Selecione o dia inicial do fechamento de folha.');
			return false;
		}
		if(empty($params['nivel1'][0])){
			App_Validate_MessageBroker::addErrorMessage('Selecione o nivel 1.');
			return false;
		}
		if(empty($params['nivel2'][0])){
			App_Validate_MessageBroker::addErrorMessage('Selecione o nivel 2.');
			return false;
		}
		return true;
	}
	
	protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		$configUSuarioBo = new Rh_Model_Bo_ConfiguracaoUsuario();
		$configUSuarioBo->deleteByConfiguracao($object->id_configuracao);
		if(count($request['nivel1'])){
			foreach ($request['nivel1'] as $usuarioId){
				if(empty($usuarioId))
					continue;
				
				$configUsuario = $configUSuarioBo->get();
				$configUsuario->nivel = Rh_Model_Bo_ConfiguracaoUsuario::NIVEL1;
				$configUsuario->id_configuracao = $object->id_configuracao;
				$configUsuario->id_usuario = $usuarioId;
				$configUsuario->save();
			}
		}
		
		if(count($request['nivel2'])){
			foreach ($request['nivel2'] as $usuarioId){
				if(empty($usuarioId))
					continue;
				
				$configUsuario = $configUSuarioBo->get();
				$configUsuario->nivel = Rh_Model_Bo_ConfiguracaoUsuario::NIVEL2;
				$configUsuario->id_configuracao = $object->id_configuracao;
				$configUsuario->id_usuario = $usuarioId;
				$configUsuario->save();
			}
		}
	}
	
	public function getFechamentoFolha($data, $format = 'dd/MM/yyyy')
	{
		$workspaceSession = new Zend_Session_Namespace('workspace');
		
		$configuracao = $this->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_workspace = ?' => $workspaceSession->id_workspace))->current();
		$dataArray = array();
		if(count($configuracao) > 0){
			$data = new Zend_Date($data);
			$data->setDay($configuracao->dia_inicio_folha);
			$dataArray['data_inicial'] = $data->toString($format);
			$data_fim = new Zend_Date($data);
			$data_fim->setDay(1)->addMonth(1)->subDay(2);
			$data->addDay($data_fim->getDay());
			$dataArray['data_fim'] = $data->toString($format); 
		}
		return $dataArray;
	}
	
	public function hasAprovacao($idUsuario, $nivel){
		$workspaceSession = new Zend_Session_Namespace('workspace');
		$configuracao = $this->_dao->getAllConfiguracaoUsuario($idUsuario, $nivel, $workspaceSession->id_workspace);
		if(count($configuracao) > 0){
			return true;
		}
		return false;
	}
}
