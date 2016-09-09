<?php
/**
 * @author Vinicius Leônidas
 * @since 12/01/2014
 */
 class Rh_PassagemController extends App_Controller_Action_AbstractCrud{
	
 	protected $_bo;
 	protected $_redirectDelete = 'rh/passagem/grid';
 	
 	public function init(){
 		$this->_helper->layout()->setLayout('metronic');
 		$this->_bo = new Rh_Model_Bo_Passagem();
 		parent::init();
 		$this->_id = $this->getParam('id_rh_passagem');
 	}

 	public function gridAction(){
 			
 		$this->view->iten = $this->_bo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO));
 			
 	}
 	
 }