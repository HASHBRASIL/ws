<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  28/08/2013
 */
class Sis_SegmentoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Sis_Model_Bo_Segmento
     */
    protected $_bo;

    protected $_authAnonymous = array("form" , "grid", "delete");
    protected $_redirectDelete = "sis/segmento/grid";
    
    public function init()
    {
        $this->_bo = new Sis_Model_Bo_Segmento;
        $this->_helper->layout()->setLayout('metronic');
        parent::init();
        $this->_id = $this->getRequest()->getParam('seg_id');
    }
    
    public function gridAction(){
    	
    	$this->view->segmentoList = $this->_bo->find(array("seg_ativo = ?"=> App_Model_Dao_Abstract::ATIVO));
    }

}