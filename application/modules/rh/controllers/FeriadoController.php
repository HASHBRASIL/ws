<?php
/**
 * @author Vinicius Leônidas
 * @since 17/001/2014
 */
class Rh_FeriadoController extends App_Controller_Action_AbstractCrud{
	
	protected $_bo;
	
	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_Feriado();
		parent::init();
		$this->_id = $this->_getParam('id_rh_feriados');
		$this->_redirectDelete = "/rh/feriado/grid";
		$this->_aclActionAnonymous = array('autocomplete', 'grid', 'form');
	}
	
	public function gridAction(){
		
		$this->view->iten = $this->_bo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO));
		
	}	
}