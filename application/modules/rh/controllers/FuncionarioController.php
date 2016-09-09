<?php
/**
 * @author Vinicius Leônidas
 * @since 17/12/2013
 */
class Rh_FuncionarioController extends App_Controller_Action_AbstractCrud{
	
	/**
	 * @var Rh_Model_Bo_Funcionario
	 */
	protected $_bo;
	
	protected $_redirectDelete = 'rh/funcionario/grid';
	
	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_Funcionario();
		parent::init();
		$this->_id = $this->getParam('id_rh_funcionario');
		$this->_aclActionAnonymous = array('get-funcionario');
	}
	
	public function _initForm(){
		$empresaBO 		 = new Empresa_Model_Bo_Empresa();
		$estadoBo        = new Sis_Model_Bo_Estado();
		$nacionalidadeBo = new Rh_Model_Bo_Nacionalidade();
		
		$documentoBo = new Rh_Model_Bo_DocumentoIdentidade();
		
		$certidaoBo = new Rh_Model_Bo_CertidaoCivil();
		$servicoMilitarBo = new Rh_Model_Bo_ServicoMilitar();
		$fgtsBo = new Rh_Model_Bo_Fgts();
		$ciBo = new Rh_Model_Bo_Ci();
		$outroBo = new Rh_Model_Bo_Outro();

		$admissaoBo = new Rh_Model_Bo_Admissao();
		
		$racaBo = new Rh_Model_Bo_Raca();
		$vinculoBo = new Rh_Model_Bo_Vinculo();
		$instrucaoBo = new Rh_Model_Bo_Instrucao();
		$categoriaBo = new Rh_Model_Bo_Categoria();
		$deficienteBo = new Rh_Model_Bo_Deficiencia();
		$contratoBo = new Rh_Model_Bo_Contrato();
		$ocorrenciaBo = new Rh_Model_Bo_Ocorrencia();
		$cagedBo = new Rh_Model_Bo_Caged();
		$tipoAdmissaoBo = new Rh_Model_Bo_TipoAdmissao();

		$funcionaisBo = new Rh_Model_Bo_DadosFuncionais();
		
		$cboBo = new Rh_Model_Bo_Cbo();
		$escalaBo = new Rh_Model_Bo_Escala();
		$passagemBo = new Rh_Model_Bo_Passagem();
		$localBo = new Rh_Model_Bo_Local();
		
		if ($this->getParam('id_rh_funcionario')){
			
			$this->view->comboFuncionario = $this->_bo->find(array('id_rh_funcionario = ?' => $this->getParam('id_rh_funcionario')))->current();
			$this->view->comboDocumento = $documentoBo->find(array('id_rh_funcionario = ?' => $this->getParam('id_rh_funcionario')))->current();
			$this->view->comboCertidao = $certidaoBo->find(array('id_rh_funcionario = ?' => $this->getParam('id_rh_funcionario')))->current();
			$this->view->comboServico = $servicoMilitarBo->find(array('id_rh_funcionario = ?' => $this->getParam('id_rh_funcionario')))->current();
			$this->view->comboFgts = $fgtsBo->find(array('id_rh_funcionario = ?' => $this->getParam('id_rh_funcionario')))->current();
			$this->view->comboCi = $ciBo->find(array('id_rh_funcionario = ?' => $this->getParam('id_rh_funcionario')))->current();
			$this->view->comboOutro = $outroBo->find(array('id_rh_funcionario = ?' => $this->getParam('id_rh_funcionario')))->current();
			$this->view->comboAdmissao = $admissaoBo->find(array('id_rh_funcionario = ?' => $this->getParam('id_rh_funcionario')))->current();
			$this->view->comboFuncionais = $funcionaisBo->find(array('id_rh_funcionario = ?' => $this->getParam('id_rh_funcionario')))->current();
			
			if (!empty($this->view->comboFuncionais->id_rh_cbo)) {
				$this->view->comboCbo = $cboBo->find(array('id_rh_cbo = ?' => $this->view->comboFuncionais->id_rh_cbo))->current() ;
			}
			
		}
		
		$this->view->comboPessoa		=		array(null => '---- Selecione ----')+$empresaBO->getFuncionarioPairs();
		$this->view->comboNacionalidade = $nacionalidadeBo->getPairs(false);
		$this->view->comboEstado        = array(null => "Estado")+$estadoBo->getPairs(false);
		
		$this->view->comboRaca = array(null => '---- Selecione ----')+$racaBo->getPairs(false);
		$this->view->comboVinculo = $vinculoBo->find() ;
		$this->view->comboInstrucao = $instrucaoBo->find() ;
		$this->view->comboCategoria = $categoriaBo->find() ;
		$this->view->comboDficiente = array(null => '---- Selecione ----')+$deficienteBo->getPairs(false);
		$this->view->comboContrato = $contratoBo->find() ;
		$this->view->comboOcorrencia = $ocorrenciaBo->find() ;
		$this->view->comboCaged = $cagedBo->find() ;
		$this->view->comboTipo = $tipoAdmissaoBo->find();
		$this->view->comboEscala = $escalaBo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO));
		$this->view->comboPassagem = $passagemBo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO));
		$this->view->comboLocal = $localBo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO));
		$this->view->comboSindicato = $empresaBO->getCaracteristicaPairs(Empresa_Model_Bo_Caracteristica::SINDICATO);
		
	}
	
	public function formAction(){
		
		parent::formAction();

		if ($this->getRequest()->isPost()){

			$funcionarioObj = $this->view->vo->id_rh_funcionario;
			$this->redirect("rh/funcionario/form/id_rh_funcionario/{$funcionarioObj}");
			
		}
	}
	
	public function gridAction(){
		$workspaceSession = new Zend_Session_Namespace('workspace');
		if (!$workspaceSession->free_access){
		
			$this->view->iten = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_workspace = ?" => $workspaceSession->id_workspace));
		
		} else {
				
			$this->view->iten = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO));
		
		}
	}
	
	public function getFuncionarioAction(){
		$id = $this->getParam('id');
		$this->_helper->json($this->_bo->getFun($id));
	}
}