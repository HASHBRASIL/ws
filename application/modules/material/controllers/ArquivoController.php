<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  11/10/2013
 */
class Material_ArquivoController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Material_Model_Bo_Arquivo
     */
    protected $_bo;

    public function init()
    {
    	$this->_helper->layout()->setLayout('metronic');
        $this->_bo = new Material_Model_Bo_Arquivo();
        parent::init();
    }

    public function gridItemAction()
    {
        $this->_helper->layout()->disableLayout();
        $idItem     = $this->getParam('id_item');
        $criteria   = array(
                'id_item = ?' => $idItem,
                'ativo = ?'   => App_Model_Dao_Abstract::ATIVO
        );

        $this->view->arquivoList = $this->_bo->find($criteria);
    }
}