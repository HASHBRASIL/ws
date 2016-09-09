<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 11/08/2014
 */
class Rh_ConfiguracaoController extends App_Controller_Action_AbstractCrud
{
	/**
	 * @var Rh_Model_Bo_Configuracao
	 */
	protected $_bo;
	
	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_Configuracao();
		parent::init();
		$this->_id = $this->getParam('id_configuracao');
 		$this->_redirectUnauthorizedWorkspace = "form";
	}
	
	public function _initForm()
	{
		$usuarioBo = new Auth_Model_Bo_Usuario();
		$comboDia = array();
		for($i = 1; $i <= 31; $i++){
			$comboDia[$i] = $i;
		}
		if(empty($this->_id)){
			
        	$workspaceSession = new Zend_Session_Namespace('workspace');
			$configuracao = $this->_bo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_workspace = ?' => $workspaceSession->id_workspace))->current();
			if(count($configuracao) > 0){
				$this->redirect('/rh/configuracao/form/id_configuracao/'.$configuracao->id_configuracao);
			}
		}
		
		$this->view->comboUsuario = array(null => '---- Selecione ----')+$usuarioBo->getPairs();
		$this->view->comboDia = $comboDia;
	}

	public function formAction()
	{
		parent::formAction();
		$configuracaoUsuarioBo = new Rh_Model_Bo_ConfiguracaoUsuario();
		if(!empty($this->view->vo->id_configuracao)){
			$this->view->configUsuarioNivel1 = $configuracaoUsuarioBo->find(array('id_configuracao = ?'=> $this->view->vo->id_configuracao, 'nivel = ?' => Rh_Model_Bo_ConfiguracaoUsuario::NIVEL1 ));
			$this->view->configUsuarioNivel2 = $configuracaoUsuarioBo->find(array('id_configuracao = ?'=> $this->view->vo->id_configuracao, 'nivel = ?' => Rh_Model_Bo_ConfiguracaoUsuario::NIVEL2 ));
		}
	}
	
}