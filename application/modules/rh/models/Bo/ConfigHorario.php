<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 24/07/2014
 */
class Rh_Model_Bo_ConfigHorario extends App_Model_Bo_Abstract
{
	/**
	 * @var Rh_Model_Dao_ConfigHorario
	 */
	protected $_dao;
	
	public function __construct()
	{
		$this->_dao = new Rh_Model_Dao_ConfigHorario();
		parent::__construct();
	}
	
	public function getListHorario($idHorario = null)
	{
		$semana = $this->_dao->getSemana();
		$horarioList = array();
		if($idHorario){
			$criteria = array(
					'ativo = ?' => App_Model_Dao_Abstract::ATIVO,
					'id_horario = ?' => $idHorario
			);
		}
		foreach ($semana as $key=>$nome){
			$criteria['semana = ?'] = $key;
			$config = null;
			if($idHorario){
				$config = $this->find($criteria)->current();
			}
			$horarioList[] = array(
					'num_semana' 	=> $key,
					'nome_semana' 	=> $nome,
					'config'		=> $config
			);
			 
		}
		
		return $horarioList;
	}
	
	public function getSemana()
	{
		return $this->_dao->getSemana();
	}
	
	public function duplicarFromRequest($request)
	{
		$criteria = array('ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_horario = ?' => $request['id_horario'], 'semana = ?' =>$request['semana_de']);
		$configHorario = $this->find($criteria)->current();
		if(count($configHorario) != 1){
			App_Validate_MessageBroker::addErrorMessage("Esta semana não é valida.");
			throw new App_Validate_Exception();
		}
		$configHorario = $configHorario->toArray();
		unset($configHorario['id_config_horario']);
		unset($configHorario['semana']);
		
        try {
        	foreach ($request['semana_para'] as $idSemana){
        		$criteria = array('ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_horario = ?' => $request['id_horario'], 'semana = ?' =>$idSemana);
        		$configHorarioDuplicado = $this->find($criteria)->current();
        		
        		if(empty($configHorarioDuplicado)){
        			$configHorarioDuplicado = $this->get();
        		}
        		
        		$configHorarioDuplicado->setFromArray($configHorario);
        		$configHorarioDuplicado->semana 			= $idSemana;
        		$configHorarioDuplicado->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
        		$configHorarioDuplicado->dt_criacao 		= date('Y-m-d H:i:s');
        		
        		$configHorarioDuplicado->save();
        	}
        } catch (Exception $e) {
            App_Validate_MessageBroker::addErrorMessage("O sistema encontra-se fora do ar no momento. Caso persista entre em contato com o Administrador.".$e->getMessage());
            throw new App_Validate_Exception();
        }
		
	}
	
	public function horarioPadraoByDia($data, $idFuncionario)
	{
		return $this->_dao->horarioPadraoByDia($data, $idFuncionario);
	}
	
	public function hasFolga($idHorario, $data)
	{
		$data = new Zend_Date($data);
		$horario = $this->find(array('id_horario = ?' => $idHorario, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'semana = ?' => $data->toString(Zend_Date::WEEKDAY_8601)))->current();
		if(empty($horario->entrada1) && empty($horario->entrada2) && empty($horario->saida1) && empty($horario->saida2)){
			return true;
		}
		return false;
		
	}
}
