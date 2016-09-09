<?php
/**
 * @author Vinicius Leônidas
 * @since 15/01/2014
 */
class Rh_CboController extends App_Controller_Action_AbstractCrud{
	
	protected $_bo;
	
	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_Cbo();
		parent::init();
		$this->_aclActionAnonymous = array('autocomplete');
	}
	
}