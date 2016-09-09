<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 29/08/2014
 */
class Rh_HorarioFuncionarioController extends App_Controller_Action_AbstractCrud
{
	/**
	 * @var Rh_Model_Bo_HorarioFuncionario
	 */
	protected $_bo;
	
	public function init()
	{
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_HorarioFuncionario();
		parent::init();
		$this->_id = $this->getParam('id_horario_funcionario');
	}
	
	public function gridFuncionarioAction()
	{
		$idHorario = $this->getParam('id_horario');
		$this->_helper->layout()->disableLayout();
		
		$criteria = array(
				'id_horario = ?' => $idHorario,
				'ativo = ?' 	 =>App_Model_Dao_Abstract::ATIVO
		);
		$this->view->funcionarioList = $this->_bo->find($criteria);
	}

	public function getAction()
	{
		$idHorarioFuncionario = $this->getParam('id_horario_funcionario');
		$horario = $this->_bo->get($idHorarioFuncionario);
		
		$horario->data = $this->_bo->dateDmy($horario->data);
		$this->_helper->json($horario);
	}
}