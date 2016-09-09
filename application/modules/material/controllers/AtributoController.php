<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  25/09/2013
 */
class Material_AtributoController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Material_Model_Bo_Atributo
     */
    protected $_bo;

    public function init()
    {
    	$this->_helper->layout()->setLayout('metronic');
        $this->_bo = new Material_Model_Bo_Atributo();
        parent::init();
        $this->_id = $this->getParam('id_atributo');
        $this->_redirectDelete = 'material/atributo/grid';
    }

    public function gridOpcaoAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$opcaoBo             = new Material_Model_Bo_Opcao();
    	$idAtributo          = $this->getParam('id_atributo');
    	$criteria            = array('id_atributo = ?' => $idAtributo, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);

    	$this->view->opcaoList = $opcaoBo->find($criteria);
    }

    public function gridAction()
    {
        $workspaceSession    = new Zend_Session_Namespace('workspace');
        $criteria            = array('ativo = ?' => App_Model_Dao_Abstract::ATIVO);

        if(!$workspaceSession->free_access){
            $criteria[] = "id_workspace = {$workspaceSession->id_workspace} or id_workspace IS NULL";
        }
        $this->view->atributoList = $this->_bo->find($criteria);
    }


}