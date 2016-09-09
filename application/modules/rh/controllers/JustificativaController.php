<?php
/**
 * @author Vinicius Leônidas
 * @since 15/01/2014
 */
class Rh_JustificativaController extends App_Controller_Action_AbstractCrud{
	
	protected $_bo;
	
	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_JustificativaPonto();
		parent::init();
		$this->_id = $this->_getParam('id_rh_justificacao_ponto');
		$this->_redirectDelete = "/rh/justificativa/grid";
	}
	
	public function gridAction(){
		
		$this->view->iten = $this->_bo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO));
		
	}
}