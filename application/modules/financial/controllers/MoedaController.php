<?php
class Financial_MoedaController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Financial_Model_Bo_Moeda
     */
    protected $_bo;

    public function init()
    {
    	$this->_bo = new Financial_Model_Bo_Moeda;
        parent::init();
        $this->_aclActionAnonymous = array('get');
    }
    
    public function getAction()
    {
    	$idMoeda = $this->getParam('id_moeda');
    	$moeda = $this->_bo->get($idMoeda);
    	$this->_helper->json($moeda);
    }

}