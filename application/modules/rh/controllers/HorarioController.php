<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 24/08/2014
 */
class Rh_HorarioController extends App_Controller_Action_AbstractCrud
{
	/**
	 * @var Rh_Model_Bo_Horario
	 */
	protected $_bo;
	
	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_Horario();
		parent::init();
		$this->_id = $this->getParam('id_horario');
	}
	
	public function gridAction()
	{
		$criteria = array('ativo = ?' => App_Model_Dao_Abstract::ATIVO);

		$workspaceSession = new Zend_Session_Namespace('workspace');
		if($workspaceSession->free_access != true){
			$criteria["id_workspace IS NULL or id_workspace = ? "] = $workspaceSession->id_workspace;
		}
		$this->view->horarioList = $this->_bo->find($criteria);
	}
	
	public function _initForm()
	{
		$funcionarioBo			= new Rh_Model_Bo_Funcionario();
		$configHorario  		= new Rh_Model_Bo_ConfigHorario();
		$configExtra			= new Rh_Model_Bo_ConfigExtra();
		$workspaceSession	    = new Zend_Session_Namespace('workspace');
		
		$idHorario 			= $this->getParam('id_horario');
		$configHorarioList  = $configHorario->getListHorario($idHorario);
		$configExtraList	= $configExtra->getListExtra($idHorario);
		
		$this->view->comboFuncionario 	= array(null => '---- Selecione ----')+$funcionarioBo->getIdFuncionario(array('trf.ativo = ?' => App_Model_Dao_Abstract::ATIVO, "id_workspace = ?" => $workspaceSession->id_workspace));
		$this->view->configHorarioList  = $configHorarioList;
		$this->view->semanaList 		= $configHorario->getSemana();
		$this->view->configExtraList	= $configExtraList;
	}
	
	public function gridHorarioAction()
	{
		$this->_helper->layout()->disableLayout();
		$idFuncionario 	= $this->getParam('id_funcionario');
		$dtInicio 		= $this->getParam('data_inicial');
		
		$horarioFuncionarioBo 	= new Rh_Model_Bo_HorarioFuncionario();
		$configHorarioBo		= new Rh_Model_Bo_ConfigHorario();
		$configuracaoBo			= new Rh_Model_Bo_Configuracao();
		
		if(empty($idFuncionario)){
			$this->_helper->json(array('success' => false, 'message' => 'Escolha um funcionário'));
		}
		if(empty($dtInicio)){
			$this->_helper->json(array('success' => false, 'message' => 'Escolha uma data inicio'));
		}
		
		$datePeriodo = $configuracaoBo->getFechamentoFolha($dtInicio, 'yyyy-MM-dd');
		$horarioPadrao = $horarioFuncionarioBo->getHorarioPadrao( $datePeriodo['data_inicial'], $datePeriodo['data_fim'], $idFuncionario);
		if(empty($horarioPadrao)){
			$this->_helper->json(array('success' => false, 'message' => 'Não possui horário padrão'));
		}
		
		$configHorarioPadrao = array();
		if(is_array($horarioPadrao)){
			foreach ($horarioPadrao as $horario){
				$configHorarioPadrao[$horario->data] = $configHorarioBo->getListHorario($horario->id_horario);
			}
		}elseif (is_object($horarioPadrao)){
			$configHorarioPadrao[] = $configHorarioBo->getListHorario($horarioPadrao->id_horario);
		}
		$this->view->configHorarioPadrao = $configHorarioPadrao;
	}
}