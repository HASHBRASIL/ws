<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 19/08/2014
 */
class Rh_ConfigExtraController extends App_Controller_Action_AbstractCrud
{
	/**
	 * @var Rh_Model_Bo_ConfigExtra
	 */
	protected $_bo;
	
	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_ConfigExtra();
		parent::init();
		$this->_id = $this->getParam('id_config_extra');
	}
	
}