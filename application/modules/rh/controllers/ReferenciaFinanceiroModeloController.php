<?php

class Rh_ReferenciaFinanceiroModeloController extends App_Controller_Action_AbstractCrud
{
	protected $_bo;

	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_ReferenciaFinanceiroModelo();
		$this->_aclActionAnonymous = array('get');
		parent::init();
	}
	public function getAction()
	{
	
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$fin_id = $this->getParam('fin_id');
		$relRhFinanceiro = $this->_bo->find(array('fin_id = ?' => $fin_id))->current();
		$relRhFinanceiroJson = $relRhFinanceiro;
	
		$this->_helper->json($relRhFinanceiroJson);
	
	}
}
