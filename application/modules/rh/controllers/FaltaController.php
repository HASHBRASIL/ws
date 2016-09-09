<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 25/08/2014
 */
class Rh_FaltaController extends App_Controller_Action_AbstractCrud
{
	/**
	 * @var Rh_Model_Bo_Falta
	 */
	protected $_bo;
	
	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_Falta();
		parent::init();
		$this->_id = $this->getParam('id_falta');
	}
	
	public function gridByPontoAction()
	{
		$this->_helper->layout()->disableLayout();
		$ponto = $this->_getParam('ponto');
		$idFuncionario = $this->_getParam('funcionario');

		$configuracaoBo = new Rh_Model_Bo_Configuracao();
		$datePeriodo = $configuracaoBo->getFechamentoFolha($this->_getParam('data_inicial'), 'yyyy-MM-dd');
		$faltaList = $this->_bo->getFaltaList($idFuncionario, $datePeriodo['data_inicial'], $datePeriodo['data_fim'] );
		
		$this->view->faltaList = $faltaList;
	}
}