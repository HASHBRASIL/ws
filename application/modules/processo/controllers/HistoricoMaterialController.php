<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  13/08/2013
 */
class Processo_HistoricoMaterialController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_HistoricoMaterial
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Processo_Model_Bo_HistoricoMaterial();
        parent::init();
    }

    public function gridAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){
            $this->_helper->layout()->disableLayout();
        }
        $id_material_processo = $this->getParam('id_material_processo');
        $this->view->historicoList = $this->_bo->find(array('id_material_processo = ?' => $id_material_processo), 'dt_criacao DESC');
    }
}