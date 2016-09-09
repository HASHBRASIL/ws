<?php
/**
 * @author Vinicius Leônidas
 * @since 10/01/2014
 */
 class Rh_EscalaController extends App_Controller_Action_AbstractCrud{
	
 	protected $_bo;
 	
 	protected $_redirectDelete = 'rh/escala/grid';
 	
 	protected $_id;
 	
 	public function init(){
 		$this->_helper->layout()->setLayout('metronic');
 		$this->_bo = new Rh_Model_Bo_Escala();
 		parent::init();
 		$this->_id = $this->getParam('id_rh_escala');
 		//$this->_aclActionAnonymous = array('autocomplete-modelo');
 	}
 	
 	public function gridAction(){
 		
 		$this->view->iten = $this->_bo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO));
 		
 	}
 	
}