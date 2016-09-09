<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  28/06/2013
 */
class Processo_StatusController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_Status
     */
    protected $_bo;
    protected $_aclActionAnonymous = array("autocomplete","quick-search-ajax", 'get');
    public function init()
    {
    	$this->_bo = new Processo_Model_Bo_Status();
        parent::init();
        $this->_helper->layout->setLayout('novo_hash');
    }
    public function _initForm()
    {
    	$this->_id = $this->getParam("sta_id");
    }
    public function gridAction()
    {

        $identity = Zend_Auth::getInstance()->getIdentity();

//        $select->where("p.id_grupo = ?", $identity->grupo['id']);
        $statusList = $this->_bo->find(array("id_grupo = ? or id_grupo IS NULL" => $identity->time['id']));
//
//        $workspaceSession = new Zend_Session_Namespace('workspace');
//
//        if ($workspaceSession->free_access){
//            $statusList = $this->_bo->find();
//        }else{
//            $statusList = $this->_bo->find("id_workspace = {$workspaceSession->id_workspace} or id_workspace IS NULL");
//        }
		$this->view->statusList = $statusList;
		$this->view->workspaceSession = $workspaceSession;
    }

    public function getAction()
    {
        $status = $this->_bo->get($this->_id);
        $this->_helper->json($status);
    }

}