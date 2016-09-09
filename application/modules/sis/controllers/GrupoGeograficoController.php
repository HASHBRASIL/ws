<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  09/04/2013
 */
class Sis_GrupoGeograficoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Sis_Model_Bo_GrupoGeografico
     */
    protected $_bo;

    /**
     * irá criar um objeto do BO
     * @see App_Controller_Action_AbstractCrud::init()
     */
    public function init()
    {
        $this->_bo = new Sis_Model_Bo_GrupoGeografico();
        parent::init();
        $this->_helper->layout()->setLayout('metronic');
        $this->_redirectDelete = ("/sis/grupo-geografico/grid");
        $this->_id = $this->getRequest()->getParam('id_grupo_geografico');
    }
    
    public function gridAction(){
    	 
    	$this->view->grupoGeograficoList = $this->_bo->find(array("ativo = ?"=> App_Model_Dao_Abstract::ATIVO));
    }
}