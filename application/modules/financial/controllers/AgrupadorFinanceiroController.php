<?php
class Financial_AgrupadorFinanceiroController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Financial_Model_Bo_AgrupadorFinanceiro
     */
    protected $_bo;

    public function init()
    {
    	$this->_redirectDelete = "financial/agrupador-financeiro/grid";
    	$this->_bo = new Financial_Model_Bo_AgrupadorFinanceiro();
    	$this->_helper->layout()->setLayout('novo_hash');
    	$this->_aclActionAnonymous = array('duplicar-transacao', 'historico');
        parent::init();
        $this->_id = $this->getParam("id_agrupador_financeiro");
    }

    public function _initForm(){

    	$processoBo     	 = new Processo_Model_Bo_Processo();
    	$moedaBo	  		 = new Financial_Model_Bo_Moeda();
    	$centroCustoBo		 = new Financial_Model_Bo_CentroCusto();
    	$grupoOperacaoBo	 = new Empresa_Model_Bo_GrupoOperacoes();
    	$pessoaBO 		 = new Legacy_Model_Bo_Pessoa();
    	$financialBo 		 = new Financial_Model_Bo_Financial();
    	$statusBo		 	 = new Financial_Model_Bo_Status();
    	$contaBo		 	 = new Financial_Model_Bo_Contas();
    	$documentoInternoBo	 = new Financial_Model_Bo_DocumentoInterno();
    	$documentoExternoBo	 = new Financial_Model_Bo_DocumentoExterno();

    	if ($this->_id){

    		$agrupadorfinancialObj = $this->_bo->get($this->_id);

    		$planoContaSaved = array();
    		$planoContaSaved['plc_id'] = "";
    		$planoContaSaved['plc_descricao'] = "";

    		if(isset($agrupadorfinancialObj->getPlanoContas()->plc_id)){

    			$planoContaSaved['plc_id'] = $agrupadorfinancialObj->getPlanoContas()->plc_id;
    			$planoContaSaved['plc_descricao'] = $agrupadorfinancialObj->getPlanoContas()->plc_cod_contabil." ".$agrupadorfinancialObj->getPlanoContas()->plc_descricao;

    		}

    		$this->view->planoContaSaved = $planoContaSaved;

    		$this->view->financialPerAgrupador = $financialBo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_agrupador_financeiro = ?"=> $this->getParam('id_agrupador_financeiro') ));


    		$valorPago = $financialBo->getFinancialListPago($this->getParam('id_agrupador_financeiro'));

    		$this->view->transacoesPagas = $valorPago;

    		$valorTotal = 0;
    		foreach ($valorPago as $key => $pago) {

    			$valorTotal = $valorTotal + $pago->fin_valor;

    		}

    		$this->view->valorTotalPago = $valorTotal;

    		if ($this->getParam('fin_id')){

    			$this->view->finIdForDialog = $this->getParam('fin_id');

    		}

    	}

    	$this->view->comboEmpresasGrupo 	= $pessoaBO->getGrupoPairs();
    	$this->view->comboGrupoOperacao		= $grupoOperacaoBo->getPairs();
    	$this->view->comboCentroCusto		= $centroCustoBo->getPairs();
    	$this->view->comboMoedas			= $moedaBo->getPairs(false);
    	$this->view->comboProcesso    		= $processoBo->getPairs(false);
    	$this->view->comboStatus        	= $statusBo->getPairs(false);
    	$this->view->comboContas			= $contaBo->getPairs();
    	$this->view->comboDocumentoInterno	= $documentoInternoBo->getPairs();
    	$this->view->comboDocumentoExterno	= $documentoExternoBo->getPairs();

    }

    public function _initGrid(){

    	$contaBo							= new Financial_Model_Bo_Contas();
    	$moedaBo 							= new Financial_Model_Bo_Moeda();

    	$this->view->comboContas 			= $contaBo->getPairs();
    	$this->view->comboMoedas			= $moedaBo->getPairs(false);

    }

    public function gridAction(){
    	$this->_initGrid();

    	$allParams = $this->getRequest()->getParams();
        if(isset($allParams['searchString'])){
            $allParams['searchString'] = str_replace(array('.', '-', '/'), '', $allParams['searchString']);
        }

        $paginator = $this->_bo->paginator($allParams);
        $this->view->paginator = $paginator;
		$this->view->fields = $this->_bo->fields;
		$this->view->columns = $this->_bo->columns;
		$this->view->formatter = array('fin_valor' => 'formatDecimal');

    }

    public function gridFinancialProcessoAjaxAction(){

    	$this->_helper->layout->disableLayout();

    	if($this->getParam("id_processo")){

    		$agrupadoFinanceiroBo = new Financial_Model_Bo_AgrupadorFinanceiro();

    		$this->view->financialList = $agrupadoFinanceiroBo->gridFinancialProcessoAjax($this->getParam("id_processo"));

    	}

    }

    public function saveTransferAction(){

    	$result = $this->_bo->saveTransfer($this->getAllParams());

    	if ($result['success'] == true){
    		App_Validate_MessageBroker::addSuccessMessage("Transferência executada com sucesso");
    		$this->redirect("/financial/agrupador-financeiro/grid");
    	}else{
    		App_Validate_MessageBroker::addErrorMessage("Transferência não pode ser executada - {$result['response']} ");
    		$this->redirect("/financial/agrupador-financeiro/grid");
    	}

    }

    public function deleteAction()
    {
    	//Desativar a view
    	$this->_helper->viewRenderer->setNoRender();

    	//verifica se e pelo ajax
    	if($this->getRequest()->isXmlHttpRequest()){
    		$tkBo = new Financial_Model_Bo_Financial();
    		$tkObj = $tkBo->find(array('id_agrupador_financeiro = ?'=> $this->_id));
    		foreach ($tkObj as $tk){
    			$tkBo->inativarSemMSG($tk['fin_id']);
    		}
    		$this->_bo->inativar($this->_id);
    		$response = $this->_mensagemJson();

    		$this->_helper->json($response);
    	} else {
    		$tkBo = new Financial_Model_Bo_Financial();
    		$tkObj = $tkBo->find(array('id_agrupador_financeiro = ?'=> $this->_id));
    		foreach ($tkObj as $tk){
    			$tkBo->inativarSemMSG($tk['fin_id']);
    		}
    		$this->_bo->inativar($this->_id);
    		if(!$this->_redirectDelete){
    			$this->_helper->redirector->gotoSimple( 'index',
    					$this->getRequest()->getControllerName(),
    					$this->getRequest()->getModuleName());
    		} else {
    			$this->_redirect($this->_redirectDelete);
    		}
    	}
    }

    public function duplicarTransacaoAction()
    {
    	$this->noRenderAndNoLayout();

     	$financeiroBo = new Financial_Model_Bo_Financial();

     	$tks = $financeiroBo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, 'id_agrupador_financeiro = ?'=> $this->_id));
     	$ts = $this->_bo->duplicarAgrupadorFinanceiro($this->_id);

    	foreach ($tks as $tk){
    		$ticket = $financeiroBo->duplicarTks($tk['fin_id'], null, null, $ts['id_agrupador_financeiro']);
    	}

    	if ($ticket == true){
    		App_Validate_MessageBroker::addSuccessMessage('Transação duplicado com sucesso.');
    		$response = array('success' => true );
    	} else {
    		$response = array('success' => false, 'msg' => $this->_mensagemJson());
    	}
     	$this->_helper->json($response);
    }

    public function historicoAction()
    {
    	if($this->getRequest()->isXmlHttpRequest()){
    		$this->_helper->layout()->disableLayout();
    	}
    	$historicoBo = new Financial_Model_Bo_HistoricoAgrupadorFinanceiro();

    	$this->view->historicoList = $historicoBo->find(array('id_agrupador_financeiro = ?' => $this->_id), 'dt_criacao DESC', 6);
    	$this->view->historicoArray = $historicoBo->find(array('id_agrupador_financeiro = ?' => $this->_id), 'dt_criacao DESC', 6);

    }

}