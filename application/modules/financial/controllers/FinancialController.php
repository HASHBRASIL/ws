<?php
class Financial_FinancialController extends App_Controller_Action_TwigCrud
{
    /**
     * @var Financial_Model_Bo_Financial
     */
    protected $_bo;

    public function init()
    {
//    	$this->_redirectDelete = "financial/financial/grid";
    	$this->_bo = new Financial_Model_Bo_Financial();
//    	$this->_aclActionAnonymous = array('get-pairs-per-type','next-or-previous-id', 'get', 'duplicar-tks-ajax', 'historico');
    	$this->_helper->layout()->setLayout('novo_hash');
        parent::init();
        $this->_id = $this->getParam("id");
    }

    public function _initForm(){


//        var_dump($this->getAllParams());

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

//        if ($this->_id){
//
//            $agrupadorfinancialObj = $this->_bo->get($this->_id);
//
//            $planoContaSaved = array();
//            $planoContaSaved['plc_id'] = "";
//            $planoContaSaved['plc_descricao'] = "";
//
//            if(isset($agrupadorfinancialObj->getPlanoContas()->plc_id)){
//
//                $planoContaSaved['plc_id'] = $agrupadorfinancialObj->getPlanoContas()->plc_id;
//                $planoContaSaved['plc_descricao'] = $agrupadorfinancialObj->getPlanoContas()->plc_cod_contabil." ".$agrupadorfinancialObj->getPlanoContas()->plc_descricao;
//
//            }
//
//            $this->view->planoContaSaved = $planoContaSaved;
//
//            $this->view->financialPerAgrupador = $financialBo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_agrupador_financeiro = ?"=> $this->getParam('id_agrupador_financeiro') ));
//
//
//            $valorPago = $financialBo->getFinancialListPago($this->getParam('id_agrupador_financeiro'));
//
//            $this->view->transacoesPagas = $valorPago;
//
//            $valorTotal = 0;
//            foreach ($valorPago as $key => $pago) {
//
//                $valorTotal = $valorTotal + $pago->fin_valor;
//
//            }
//
//            $this->view->valorTotalPago = $valorTotal;
//
//            if ($this->getParam('fin_id')){
//
//                $this->view->finIdForDialog = $this->getParam('fin_id');
//
//            }
//
//        }

        $this->view->comboEmpresasGrupo 	= $pessoaBO->getGrupoPairs();
        $this->view->comboGrupoOperacao		= $grupoOperacaoBo->getPairs();
        $this->view->comboCentroCusto		= $centroCustoBo->getPairs();
        $this->view->comboMoedas			= $moedaBo->getPairs(false);
        $this->view->comboProcesso    		= $processoBo->getPairs(false);
        $this->view->comboStatus        	= $statusBo->getPairs(false);
        $this->view->comboContas			= $contaBo->getPairs();
        $this->view->comboDocumentoInterno	= $documentoInternoBo->getPairs();
        $this->view->comboDocumentoExterno	= $documentoExternoBo->getPairs();

        foreach ($this->servico['filhos'] as $filho) {
            if ($filho['metadata']['ws_comportamento'] == 'filter') {
                // @autocomplete
                $autocomplete = $filho;
            }
        }

//        var_dump($autocomplete);exit;

        $this->view->autoCompletePessoa = $autocomplete;

    }


    public function _initGrid(){
    	//$statusBo		 	 = new Financial_Model_Bo_Status();
        $pessoaBO 		 = new Legacy_Model_Bo_Pessoa();
    	$contaBo		 	 = new Financial_Model_Bo_Contas();
    	$centroCustoBo		 = new Financial_Model_Bo_CentroCusto();
    	$grupoOperacaoBo	 = new Empresa_Model_Bo_GrupoOperacoes();
    	$planoContas 		 = new Financial_Model_Bo_PlanoContas();
    	$contaBo		 	 = new Financial_Model_Bo_Contas();

    	//$this->view->comboStatus        	= $statusBo->getPairs(false);
        $this->view->comboEmpresasGrupo 	= $pessoaBO->getGrupoPairs();
    	$this->view->comboContas			= $contaBo->getPairs(false);
    	$this->view->comboCentroCusto		= $centroCustoBo->getPairs();
    	$this->view->comboGrupoOperacao		= $grupoOperacaoBo->getPairs();
    	$this->view->comboPlanoContas		= $planoContas->getPairsPerType();
    	$this->view->comboContas			= $contaBo->getPairs(false);
    }

//    public function gridAction(){
//
//    	$this->_initGrid();
//
//    	$allParams = $this->getRequest()->getParams();
//        if(isset($allParams['searchString'])){
//            $allParams['searchString'] = str_replace(array('.', '-', '/'), '', $allParams['searchString']);
//        }
//
//        $allParams = $this->_bo->formatDateFinancial($allParams);
//        $paginator = $this->_bo->paginator($allParams);
//        $this->view->paginator = $paginator;
//		$this->view->fields = $this->_bo->fields;
//		$this->view->columns = $this->_bo->columns;
//
//		$this->view->formatter = array( 'fin_emissao' => 'date', 'fin_vencimento' => 'date', 'fin_compensacao' => 'date', 'fin_valor' => 'formatDecimal');
//
//        $paramsSearch = new Zend_Session_Namespace('paramsSearch');
//        $paramsSearch->paramsSearch = $allParams;
//    }

//    public function _initIndex(){
//
//    	//$statusBo    = new Financial_Model_Bo_Status();
//    	//$this->view->comboStatus        = $statusBo->getPairs(false);
//
//    	$contaBo		= new Financial_Model_Bo_Contas();
//    	$this->view->comboContas = $contaBo->getPairs(false);
//
//    }

//	public function indexAction(){
//
//		$this->_initIndex();
//
//		 if($this->getRequest()->isPost()){
//
//		 	$allParams = $this->getAllParams();
//		 	$allParams['data_emissao'] = str_replace("/", "-", $allParams['data_emissao']);
//		 	$allParams['data_emissao2'] = str_replace("/", "-", $allParams['data_emissao2']);
//		 	$allParams['data_vencimento'] = str_replace("/", "-", $allParams['data_vencimento']);
//		 	$allParams['data_vencimento2'] = str_replace("/", "-", $allParams['data_vencimento2']);
//		 	$allParams['data_compensacao'] = str_replace("/", "-", $allParams['data_compensacao']);
//		 	$allParams['data_compensacao2'] = str_replace("/", "-", $allParams['data_compensacao2']);
//
//		 	$this->_helper->redirector->gotoSimple( "grid", "financial","financial",$allParams);
//
//		 }
//
//	}

//	public function reciboAction(){
//
//		$agrupadorFinanceiroBo = new Financial_Model_Bo_AgrupadorFinanceiro();
//		$pessoaBo = new Legacy_Model_Bo_Pessoa();
//
//		$financialObj = $this->_bo->get($this->getParam('fin_id'));
//		$this->view->financialObj  =  $financialObj;
//		$agrupadorFinanceiroObj = $agrupadorFinanceiroBo->find(array("id_agrupador_financeiro = ?" => $financialObj->id_agrupador_financeiro))->current();
//		$this->view->userName = Zend_Auth::getInstance()->getIdentity()->nomeusuario;
//		$this->view->agrupadorFinanceiroObj = $agrupadorFinanceiroObj;
//		$faturado = $pessoaBo->get($financialObj->id_pessoa_faturado);
//
//		$this->view->faturadoContraPor = $faturado->nome_razao;
//		$this->view->faturadoCpfCnpj = $faturado->cnpj_cpf;
//	}
//
//
//	public function saveFinancialFromProcessoAjaxAction(){
//
//		$this->_helper->layout->disableLayout();
//		$this->_helper->viewRenderer->setNoRender();
//
//		$object = $this->_bo->get();
//
//		try{
//			$this->_bo->saveFromRequest($this->getAllParams(), $object);
//			if($this->getRequest()->isXmlHttpRequest()){
//				$response = array('success' => true);
//				$this->_helper->json($response);
//			}
//
//		}catch (App_Validate_Exception $e){
//
//			$response = array('success' => false, 'error' => $this->_mensagemJson());
//			$this->_helper->json($response);
//		}
//		catch (Exception $e){
//			$response = array('success' => false, 'error' => $e->getMessage());
//			$this->_helper->json($response);
//		}
//	}
//	public function printAction(){
//	    $this->_helper->layout()->setLayout('metronic');
//
//	    $idMovimentacaoFinanceira = $this->getParam("id");
//	    $regMovimentacaoFinanceira = $this->_bo->get($idMovimentacaoFinanceira);
//
//	    $empresasBo = new Empresa_Model_Bo_Empresa();
//
//	    $empresa = $empresasBo->get($regMovimentacaoFinanceira->grupo_id);
//
//	    $this->view->faturadoPor = $empresa->nome_razao;
//	    $this->view->columns = $this->_bo->columns;
//
//	    $this->view->usuario = Zend_Auth::getInstance()->getIdentity()->nome_razao;
//	    $this->view->registros = $regMovimentacaoFinanceira;
//	    $this->view->id =  $idMovimentacaoFinanceira;
//
//	}
//
//	public function getAction()
//	{
//
//		$sacadoFinanceiroBo = new Financial_Model_Bo_SacadoFinanceiro();
//		$empresaBo = new Empresa_Model_Bo_Empresa();
//
//		$this->_helper->viewRenderer->setNoRender();
//		$this->_helper->layout->disableLayout();
//		$fin_id = $this->getParam('fin_id');
//		$financeiroObj = $this->_bo->get($fin_id);
//		$financeiroJson = $financeiroObj->toArray();
//
//		$financeiroJson['fin_compensacao'] = $this->_bo->dateDmy($financeiroObj->fin_compensacao);
//		$financeiroJson['fin_emissao'] = $this->_bo->dateDmy($financeiroObj->fin_emissao);
//		$financeiroJson['fin_vencimento'] = $this->_bo->dateDmy($financeiroObj->fin_vencimento);
//		$financeiroJson['fin_competencia'] = $this->_bo->dateDmy($financeiroObj->fin_competencia);
//
//		$sacadoFinanceiroObj = $sacadoFinanceiroBo->find(array("tb_financeiro_fin_id = ?" => $fin_id ))->current();
//
//		if (!empty($sacadoFinanceiroObj['empresas_id'])){
//
//			$empresaObj = $empresaBo->find(array("id = ?" => $sacadoFinanceiroObj->empresas_id,"ativo = ?" => App_Model_Dao_Abstract::ATIVO))->current();
//
//				$financeiroJson = $financeiroJson + array("success" => true) + array("id_empresa" => $empresaObj->id, "nome_razao" => $empresaObj->nome_razao);
//				$this->_helper->json($financeiroJson);
//		}else{
//
//			$financeiroJson = $financeiroJson + array("success" => true);
//			$this->_helper->json($financeiroJson);
//		}
//
//
//	}
//
//	public function getFinancialPerModelsAction(){
//
//		$this->_helper->layout->disableLayout();
//
//		$options = $this->getParam('options');
//
//		$this->view->financialList = $this->_bo->getFinancialPerModelsAction($options);
//	}
//
//	public function getFinancialWithProcessoIncompatiblePerModelsAction(){
//
//		$this->_helper->layout->disableLayout();
//
//		$options = $this->getParam('options');
//
//		$agrupadorFinanceiroBo = new Financial_Model_Bo_AgrupadorFinanceiro();
//		$this->view->processoList = $agrupadorFinanceiroBo->getFinancialWithProcessoIncompatiblePerModels();
//
//
//	}
//
//	public function gridPdfAction()
//	{
//	    $this->noRenderAndNoLayout();
//
//	    $allParams = $this->getRequest()->getParams();
//	    if(isset($allParams['searchString'])){
//	        $allParams['searchString'] = str_replace(array('.', '-', '/'), '', $allParams['searchString']);
//	    }
//
//	    $allParams = $this->_bo->formatDateFinancial($allParams);
//	    $listFinancial = $this->_bo->allSearchFinancial($allParams);
//
//	    $pdf = new App_Util_Pdf(null, null, null, null, null, "L");
//	    $this->view->listFinancial = $listFinancial;
//
//	    $html = $this->view->render('financial/grid-pdf.phtml');
//
//        $pdf->modificarFonte('helvetica', 10);
//        $pdf->adicionarHtml($html);
//        $pdf->abrirArquivo();exit();
//	}
//
//	public function duplicarTksAjaxAction(){
//		$this->noRenderAndNoLayout();
//
//		$dtVencimento = $this->_bo->date($this->_getParam('vencimento'), 'yyyy/MM/dd');
//		$dtCompetencia = $this->_bo->date($this->_getParam('competencia'), 'yyyy/MM/dd');
//		$tk = $this->_bo->duplicarTks($this->_id, $dtVencimento, $dtCompetencia);
//
//		if ($tk == true){
//			App_Validate_MessageBroker::addSuccessMessage('Ticket duplicado com sucesso.');
//			$response = array('success' => true );
//		} else {
//			$response = array('success' => false, 'msg' => $this->_mensagemJson());
//		}
//		$this->_helper->json($response);
//
//	}
//
//	public function historicoAction()
//	{
//		if($this->getRequest()->isXmlHttpRequest()){
//			$this->_helper->layout()->disableLayout();
//		}
//		$historicoBo = new Financial_Model_Bo_HistoricoFinanceiro();
//
//		$this->view->historicoList = $historicoBo->find(array('fin_id = ?' => $this->_id), 'dt_criacao DESC', 6);
//		$this->view->historicoArray = $historicoBo->find(array('fin_id = ?' => $this->_id), 'dt_criacao DESC', 6);
//
//	}
//
//	public function gridEditableAction()
//	{
//	    $this->_helper->layout()->disableLayout();
//	    $idAgrupadorFinanceiro = $this->getParam('id_agrupador_financeiro');
//        $criteria = array(
//                    'ativo = ?' => App_Model_Dao_Abstract::ATIVO,
//                    'id_agrupador_financeiro = ?' => $idAgrupadorFinanceiro
//                );
//        $this->view->financialList = $this->_bo->find($criteria);
//	}
//
//	public function gridAjaxAction()
//	{
//	    $this->_helper->layout()->disableLayout();
//	    $idAgrupadorFinanceiro = $this->getParam('id_agrupador_financeiro');
//	    $agrupadorFinanceiroBo = new Financial_Model_Bo_AgrupadorFinanceiro();
//        $criteria = array(
//                    'ativo = ?' => App_Model_Dao_Abstract::ATIVO,
//                    'id_agrupador_financeiro = ?' => $idAgrupadorFinanceiro
//                );
//        $this->view->financialList = $this->_bo->find($criteria);
//        $this->view->agrupadorFinanceiro = $agrupadorFinanceiroBo->get($idAgrupadorFinanceiro);
//	}

}