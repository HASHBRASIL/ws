<?php
/**
 * @author Vinícius S P Leônidas
 * @since 20/03/2014
 */
class Comercial_RelatorioController extends App_Controller_Action_AbstractCrud
{
	
	protected $_bo;
	
	public function init()
	{
		$this->_bo = new Comercial_Model_Bo_Relatorio();
		$this->_helper->layout()->setLayout('metronic');
		parent::init();
		$this->_aclActionAnonymous = array('index', 'relatorio');
	}
	
	public function indexAction(){

		$grupoGeograficoBo         = new Sis_Model_Bo_GrupoGeografico();

		$this->view->comboGrupoGeografico     = $grupoGeograficoBo->getPairs();
	}
	
	public function relatorioAction(){
		
		$dados = $this->_getAllParams();
		
		$lista = $this->_bo->buscarRelatorio($dados);
		
		$this->view->dados = $lista;
	}
	
	public function pedidoAction()
	{
		$workspaceSession = new Zend_Session_Namespace('workspace');
		$statusBo = new Processo_Model_Bo_Status();
		$workspaceBo = new Auth_Model_Bo_Workspace();
	
		if( $workspaceSession->free_access){
			$this->view->workspacePairs = $workspaceBo->getPairs();
		}
		$this->view->id_workspace = $workspaceSession->id_workspace;
		$this->view->statusPairs  = $statusBo->getPairs();
	}
}