<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 21/08/2014
 */
class Rh_ExtraController extends App_Controller_Action_AbstractCrud
{
	/**
	 * @var Rh_Model_Bo_Extra
	 */
	protected $_bo;
	
	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_Extra();
		parent::init();
		$this->_aclActionAnonymous = array('autocomplete');
		$this->_id = $this->getParam('id_extra');
	}
	
	public function gridByPontoAction()
	{
		$this->_helper->layout()->disableLayout();

		$ponto = $this->_getParam('ponto');
		$idFuncionario = $this->_getParam('funcionario');

		$configuracaoBo = new Rh_Model_Bo_Configuracao();
		$datePeriodo = $configuracaoBo->getFechamentoFolha($this->_getParam('data_inicial'), 'yyyy-MM-dd');
		$extraList = $this->_bo->getExtraList($idFuncionario, $datePeriodo['data_inicial'], $datePeriodo['data_fim'] );
		
		$this->view->extraList = $extraList;
	}
	
	public function aprovarGerenteAction()
	{
		$idExtra = $this->getParam('id_extra');
		$extra = $this->_bo->get($idExtra);
		
		if(!empty($extra->id_extra)){
			try {
				$this->_bo->aprovarGerente($extra);
				$this->_helper->json(array('success' => true));
			} catch (Exception $e) {
				$this->_helper->json(array('success' => false, 'mensagem' =>$this->_mensagemJson()));
			}
		}
	}

	public function aprovarDiretorAction()
	{
		$idExtra = $this->getParam('id_extra');
		$extra = $this->_bo->get($idExtra);
	
		if(!empty($extra->id_extra)){
			try {
				$this->_bo->aprovarDiretor($extra);
				$this->_helper->json(array('success' => true));
			} catch (Exception $e) {
				$this->_helper->json(array('success' => false, 'mensagem' =>$this->_mensagemJson()));
			}
		}
	}

	public function reprovarGerenteAction()
	{
		$idExtra = $this->getParam('id_extra');
		$extra = $this->_bo->get($idExtra);
	
		if(!empty($extra->id_extra)){
			try {
				$this->_bo->reprovarGerente($extra);
				$this->_helper->json(array('success' => true));
			} catch (Exception $e) {
				App_Validate_MessageBroker::addErrorMessage($e->getMessage());
				$this->_helper->json(array('success' => false, 'mensagem' =>$this->_mensagemJson()));
			}
		}
	}

	public function reprovarDiretorAction()
	{
		$idExtra = $this->getParam('id_extra');
		$extra = $this->_bo->get($idExtra);
	
		if(!empty($extra->id_extra)){
			try {
				$this->_bo->reprovarDiretor($extra);
				$this->_helper->json(array('success' => true));
			} catch (Exception $e) {
				App_Validate_MessageBroker::addErrorMessage($e->getMessage());
				$this->_helper->json(array('success' => false, 'mensagem' =>$this->_mensagemJson()));
			}
		}
	}
}