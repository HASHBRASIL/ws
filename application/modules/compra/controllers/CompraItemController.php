<?php
class  Compra_CompraItemController extends App_Controller_Action_AbstractCrud
{
	protected $_bo;
	
	public function init()
	{
		$this->_bo = new Compra_Model_Bo_CompraItem();
		$this->_aclActionAnonymous = array('form');
		$this->_helper->layout()->setLayout('metronic');
		parent::init();
		$this->_id = $this->getParam('id_compra_item');
	}
	
}