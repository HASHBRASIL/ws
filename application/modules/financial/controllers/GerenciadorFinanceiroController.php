<?php
class Financial_GerenciadorFinanceiroController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Financial_Model_Bo_GerenciadorFinanceiro
     */
    protected $_bo;

    public function init()
    {
    	$this->_bo = new Financial_Model_Bo_GerenciadorFinanceiro();
        $this->_aclActionAnonymous = array("consolidated-an-account-ajax");
        $this->_helper->layout()->setLayout('novo_hash');
        parent::init();
    }


	public function _initForm(){

	}

	public function gridAction(){

		$this->_helper->layout()->disableLayout();

		$planoContasBo = new Financial_Model_Bo_PlanoContas();
		$centroCustoBo = new Financial_Model_Bo_CentroCusto();
		$contasBo = new Financial_Model_Bo_Contas();
		$empresasBO = new Empresa_Model_Bo_Empresa();
        $pessoaBO = new Legacy_Model_Bo_Pessoa();
		$grupoOperacaoBo = new Empresa_Model_Bo_GrupoOperacoes();

		$workspaceSession = new Zend_Session_Namespace('workspace');

		$request = $this->getAllParams();

		foreach ($request as $key => $param) {
			if ($param == ""){
				$request[$key] = null;
			}
		}

        $identity = Zend_Auth::getInstance()->getIdentity();

//        $select->where("agf.id_grupo = ?", $identity->grupo['id']);

//		$workspaceId = null;
//		if (!$workspaceSession->free_access){
//
//			$workspaceId = $workspaceSession->id_workspace;
//
//		}

		$planoContasListTransacao = $planoContasBo->getListPlanoWithAgrupadorAndWorkspacePerTransacao($request['plc_id'],$identity->time['id']);

		$planoContasListTicket = $planoContasBo->getListPlanoWithAgrupadorAndWorkspacePerTicket($request['plc_id'],$identity->time['id']);

		$contasListTicket = $contasBo->getListContaWithFinanceiroAndWorkspacePerTicket($request['con_id'],$identity->time['id']);

		$centroCustoListTransacao = $centroCustoBo->getListCentroCustoWithFinanceiroAndWorkspacePerTransacao($request['cec_id'],$identity->time['id']);

		$centroCustoListTicket = $centroCustoBo->getListCentroCustoWithFinanceiroAndWorkspacePerTicket($request['cec_id'],$identity->time['id']);

		$faturadoContraListTransacao = $pessoaBO->getListFaturadoWithAgrupadorAndWorkspacePerTransacao($request['id_pessoa_faturado'],$identity->time['id']);

		$faturadoContraListTicket = $pessoaBO->getListFaturadoWithAgrupadorAndWorkspacePerTicket($request['id_pessoa_faturado'],$identity->time['id']);

		$grupoOperacaoListTransacao = $grupoOperacaoBo->getListGrupoWithAgrupadorAndWorkspacePerTransacao($request['ope_id'],$identity->time['id']);

		$grupoOperacaoListTicket = $grupoOperacaoBo->getListGrupoWithFinanceiroAndWorkspacePerTicket($request['ope_id'],$identity->time['id']);

		$this->view->grupoOperacaoListTransacao = $grupoOperacaoListTransacao;
		$this->view->grupoOperacaoListTicket = $grupoOperacaoListTicket;
		$this->view->faturadoContraListTicket = $faturadoContraListTicket;
		$this->view->faturadoContraListTransacao = $faturadoContraListTransacao;
		$this->view->centroCustoListTransacao = $centroCustoListTransacao;
		$this->view->centroCustoListTicket = $centroCustoListTicket;
		$this->view->planoContasListTransacao = $planoContasListTransacao;
		$this->view->planoContasListTicket = $planoContasListTicket;
		$this->view->contasListTicket = $contasListTicket;

	}

	public function indexAction(){

		$workspaceSession = new Zend_Session_Namespace('workspace');
		$this->view->workspaceSession = $workspaceSession;

		$grupoOperacaoBo = new Empresa_Model_Bo_GrupoOperacoes();
        $pessoaBO 		 = new Legacy_Model_Bo_Pessoa();
		$planoContasBo = new Financial_Model_Bo_PlanoContas();
		$centroCustoBo = new Financial_Model_Bo_CentroCusto();
		$contasBo = new Financial_Model_Bo_Contas();

        $this->view->comboEmpresasGrupo 	= $pessoaBO->getGrupoPairs();
		$this->view->comboGrupoOperacao		= $grupoOperacaoBo->getPairs();
		$this->view->comboCentroCusto		= $centroCustoBo->getPairs();
		$this->view->comboContas			= $contasBo->getPairs(false);
		$this->view->comboPlanoContas		= $planoContasBo->getPairs();

	}

	public function consolidatedAnAccountAjaxAction(){

		$this->noRenderAndNoLayout();

		$contaBo = new Financial_Model_Bo_Contas();
		$array = array();

		$date = new Zend_Date();
		$date->add(-3,Zend_Date::MONTH);
		$array['_3monthAgoPay'] = $contaBo->getContasPerWorkspace($date->toString("Y-M-dd"), Financial_Model_Bo_Contas::APAGAR);
		$date = new Zend_Date();
		$date->add(-3,Zend_Date::MONTH);
		$array['_3monthAgoReceive'] = $contaBo->getContasPerWorkspace($date->toString("Y-M-dd"), Financial_Model_Bo_Contas::ARECEBER);
		$date = new Zend_Date();
		$date->add(-6,Zend_Date::MONTH);
		$array['_6monthAgoPay'] = $contaBo->getContasPerWorkspace($date->toString("Y-M-dd"), Financial_Model_Bo_Contas::APAGAR);
		$date = new Zend_Date();
		$date->add(-6,Zend_Date::MONTH);
		$array['_6monthAgoReceive'] = $contaBo->getContasPerWorkspace($date->toString("Y-M-dd"), Financial_Model_Bo_Contas::ARECEBER);
		$date = new Zend_Date();
		$date->add(-12,Zend_Date::MONTH);
		$array['_12monthAgoPay'] = $contaBo->getContasPerWorkspace($date->toString("Y-M-dd"), Financial_Model_Bo_Contas::APAGAR);
		$date = new Zend_Date();
		$date->add(-12,Zend_Date::MONTH);
		$array['_12monthAgoReceive'] = $contaBo->getContasPerWorkspace($date->toString("Y-M-dd"),Financial_Model_Bo_Contas::ARECEBER);

		$this->_helper->json(array("success" => true)+$array);
	}
}