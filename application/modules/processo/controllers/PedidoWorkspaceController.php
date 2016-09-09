<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  19/03/2014
 */
class Processo_pedidoWorkspaceController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_PedidoWorkspace
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Processo_Model_Bo_PedidoWorkspace();
        parent::init();
        $this->_helper->layout->setLayout('novo_hash');
    }

    public function _initForm()
    {
        $idProcesso = $this->getParam('id_processo');
        if(empty($idProcesso)){
            $this->redirect('processo/processo/grid');
        }
        $processoBo = new Processo_Model_Bo_Processo();
        $workspaceBo = new Auth_Model_Bo_Workspace();

        $processo = $processoBo->get($idProcesso);
        $this->view->processo = $processo;
        $this->view->workspaceCombo = array(null=>'---- Selecione ----')+$workspaceBo->getPairs();

        if (!$workspaceBo->validateRegisterWithWorkspace($processo->id_workspace)){
            $this->redirect("/processo/processo/grid");
        }
    }

    public function formAction()
    {
        $idProcesso = $this->getParam('id_processo');
        parent::formAction();
        $this->view->processoPaiList = $this->_bo->processoPai($idProcesso);

    }

}