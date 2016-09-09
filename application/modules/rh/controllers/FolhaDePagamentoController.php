<?php
/**
 * @author Vinicius Leonidas
 * @since 21/11/13
 */
class Rh_FolhaDePagamentoController extends App_Controller_Action_AbstractCrud
{

    /**
     * @var Rh_Model_Bo_FolhaDePagamento
     */
	protected $_bo;
	protected $_redirectDelete = '/rh/folha-de-pagamento/grid';

	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_FolhaDePagamento();
		parent::init();
		$this->_id = $this->getParam("id_rh_folha_de_pagamento");
		$this->_aclActionAnonymous = array('autocomplete-modelo', 'migra', 'relatorio', 'table');
	}

	public function _initForm(){


		$workspaceSession = new Zend_Session_Namespace('workspace');

		$processoBo     	 = new Processo_Model_Bo_Processo();
		$moedaBo	  		 = new Financial_Model_Bo_Moeda();
		$centroCustoBo		 = new Financial_Model_Bo_CentroCusto();
		$grupoOperacaoBo	 = new Empresa_Model_Bo_GrupoOperacoes();
		$empresaBO 			 = new Empresa_Model_Bo_Empresa();
		$financialBo 		 = new Financial_Model_Bo_Financial();
		$statusBo		 	 = new Financial_Model_Bo_Status();
		$contaBo		 	 = new Financial_Model_Bo_Contas();
		$documentoInternoBo	 = new Financial_Model_Bo_DocumentoInterno();
		$documentoExternoBo	 = new Financial_Model_Bo_DocumentoExterno();
		$funcionarioBo		 = new Rh_Model_Bo_Funcionario();
		$tpPagamentoBo		 = new Rh_Model_Bo_TipoPagamento();
		
		if ($this->getParam('id_rh_folha_de_pagamento')){

			$agrupadorfinancialObj = $this->_bo->find(array("id_rh_folha_de_pagamento = ?" => $this->_id))->current();

			$this->view->pagar = $this->_bo->somaTotal($agrupadorfinancialObj->tss_id);
			$this->view->receber = $this->_bo->somaTotal($agrupadorfinancialObj->tse_id);

			$planoContaSaved = array();
			$planoContaSaved['plc_id'] = "";
			$planoContaSaved['plc_descricao'] = "";

			if(isset($agrupadorfinancialObj->getPlanoContas()->plc_id)){

				$planoContaSaved['plc_id'] = $agrupadorfinancialObj->getPlanoContas()->plc_id;
				$planoContaSaved['plc_descricao'] = $agrupadorfinancialObj->getPlanoContas()->plc_cod_contabil." ".$agrupadorfinancialObj->getPlanoContas()->plc_descricao;

			}

			$this->view->planoContaSaved = $planoContaSaved;

		}


		$this->view->comboEmpresasGrupo 	= $empresaBO->getGrupoPairs();
		$this->view->comboGrupoOperacao		= $grupoOperacaoBo->getPairs();
		$this->view->comboCentroCusto		= $centroCustoBo->getPairs();
		$this->view->comboMoedas			= $moedaBo->getPairs(false);
		$this->view->comboProcesso    		= $processoBo->getPairs(false);
		$this->view->comboStatus        	= $statusBo->getPairs(false);
		$this->view->comboContas			= $contaBo->getPairs(false);
		$this->view->comboDocumentoInterno	= $documentoInternoBo->getPairs();
		$this->view->comboDocumentoExterno	= $documentoExternoBo->getPairs();
		$this->view->comboPessoa       	    = array(null => '---- Selecione ----')+$funcionarioBo->getFuncionario(array('trf.ativo = ?' => App_Model_Dao_Abstract::ATIVO, "id_workspace = ?" => $workspaceSession->id_workspace));
		$this->view->comboTpPagamento 	    = $tpPagamentoBo->getPairs(false, null, null, 'id_tp_pagamento');

	}

	public function formAction(){

		parent::formAction();

		if(!empty($this->view->vo->id_rh_folha_de_pagamento) && !empty($this->view->vo->id_empresa)){
			$relsacadoBo = new Financial_Model_Bo_SacadoFinanceiro();
			$this->view->migra = $relsacadoBo->getFinanceiroRh($this->view->vo->id_empresa, $this->_bo->dateDmy($this->view->vo->dt_competencia));
		}
	}

	public function gridAction(){
		$tpPagamentoBo		 = new Rh_Model_Bo_TipoPagamento();
		$funcionarioBo		 = new Rh_Model_Bo_Funcionario();
		$workspaceSession	 = new Zend_Session_Namespace('workspace');
		$this->view->comboTpPagamento 	    = array(null => '---- Selecione ----')+$tpPagamentoBo->getPairs(false, null, null, 'id_tp_pagamento');
		$this->view->comboPessoa       	    = array(null => '---- Selecione ----')+$funcionarioBo->getFuncionario(array('trf.ativo = ?' => App_Model_Dao_Abstract::ATIVO, "id_workspace = ?" => $workspaceSession->id_workspace));
		if (!$workspaceSession->free_access){

			$this->view->folha = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_workspace = ?" => $workspaceSession->id_workspace));

		} else {

			$this->view->folha = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO));

		}
	}
	
	public function tableAction()
	{
		$this->_helper->layout->disableLayout();
		
		$idFuncionario = $this->getParam('id_funcionario');
		$dtreferencia 	= $this->getParam('dt_referencia');
		$idTpPagamento 	= $this->getParam('id_tp_pagamento');
		$dtPeriodo		= new Zend_Date($this->getParam('dt_periodo'));
		$dtPeriodo->setDay(1);
		$criteria = array("ativo = ?" => App_Model_Dao_Abstract::ATIVO);
		if(!$this->getParam('dt_periodo')){
			$dtPeriodo->subMonth(1);
		}

		$nextPeriodo = new Zend_Date($dtPeriodo);
		$prevPeriodo = new Zend_Date($dtPeriodo);
		$nextPeriodo->addMonth(1);
		$prevPeriodo->subMonth(1);
		
		$criteria['dt_competencia = ?'] = $dtPeriodo->toString('yyyy-MM-dd');
		if($idFuncionario)
			$criteria['id_empresa = ?'] = $idFuncionario;
		
		if($dtreferencia)
			$criteria['dt_competencia = ?'] = $this->_bo->date($dtreferencia, 'yyyy-MM-dd');
		
		if($idTpPagamento)
			$criteria['id_tp_pagamento = ?'] = $idTpPagamento;
		
		$workspaceSession = new Zend_Session_Namespace('workspace');
		if (!$workspaceSession->free_access){
			$criteria['id_workspace = ?'] = $workspaceSession->id_workspace;
		}
		$this->view->folhaList = $this->_bo->find($criteria);
		$this->view->dtPeriodo = $dtPeriodo;
		$this->view->nextPeriodo = $nextPeriodo;
		$this->view->prevPeriodo = $prevPeriodo;
	}

	public function gridTicketAjaxAction(){

		$this->_helper->layout->disableLayout();

		if($this->getParam("id_agrupador_financeiro")){

			$financialBo 		 = new Financial_Model_Bo_Financial();

			$this->view->iten = $financialBo->find(array("id_agrupador_financeiro = ?" => $this->getParam("id_agrupador_financeiro"),"ativo = ?" => App_Model_Dao_Abstract::ATIVO));
			$this->view->id = $this->getParam("id_agrupador_financeiro");
		}

	}

	public function duplicarAction(){

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$dataCompetencia = $this->_bo->dateYmd($this->_getParam('fin_emissao'));
		$dataVencimento = $this->_bo->dateYmd($this->_getParam('fin_vencimento'));
		$this->_bo->duplicarFolha($this->_getParam('id_agrupador_financeiro'), $dataCompetencia, $dataVencimento);

	}

	public function desativarTicketAction(){

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$financialBo = new Financial_Model_Bo_Financial();
		$idTicket = $this->_getParam('fin_id');

		$this->_helper->json($financialBo->deleteTicketRh($idTicket));

	}
	public function autocompleteModeloAction()
	{
		$modeloBo = new Rh_Model_Bo_ModeloSintetico();

		$term = $this->getRequest()->getParam('term');
		$tipo = $this->getRequest()->getParam('tipo');

		$list = $modeloBo->getAutocompleteModelo($term, $tipo);
		$this->_helper->json($list);
	}
	public function validarCamposAjaxAction(){

		$idFolhaDePagamento = $this->_getParam('userId');
		$data = $this->_getParam('fin_emissao');

		$this->_helper->json($this->_bo->verificarData($idFolhaDePagamento, $data));

	}

	public function reciboDePagamentoAction(){

		$this->_helper->layout()->disableLayout();

		$ticketBo = new Financial_Model_Bo_Financial();
		$agrupadorBo = new Financial_Model_Bo_AgrupadorFinanceiro();
		$sisConfigBo = new Sis_Model_Bo_Sis();
		$funcionarioBo = new Rh_Model_Bo_Funcionario();
		$funcionaisBo = new Rh_Model_Bo_DadosFuncionais();
		$admissaoBo = new Rh_Model_Bo_Admissao();
		$cboBo = new Rh_Model_Bo_Cbo();
		$relBo = new Rh_Model_Bo_ReferenciaFinanceiroModelo();

		$fin_id = $this->_getParam('fin_id');

		$ticketObj = $ticketBo->find(array('fin_id = ?' => $fin_id))->current();
		$agrupadorObj = $agrupadorBo->find(array('id_agrupador_financeiro = ?' => $ticketObj->id_agrupador_financeiro))->current();
		$sisConfigObj = $sisConfigBo->find()->current();
		$folhaObj = $this->_bo->find(array("tss_id = {$agrupadorObj->id_agrupador_financeiro} OR tse_id = {$agrupadorObj->id_agrupador_financeiro}"))->current();
		$funcionarioObj = $funcionarioBo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_empresa = ?' => $agrupadorObj->id_empresa))->current();
		$funcionaisObj = $funcionaisBo->find(array('id_rh_funcionario = ?' => $funcionarioObj->id_rh_funcionario))->current();
		$cboObj = $cboBo->find(array('id_rh_cbo = ?' => $funcionaisObj->id_rh_cbo))->current();
		$admissaoObj = $admissaoBo->find(array('id_rh_funcionario = ?' => $funcionarioObj->id_rh_funcionario))->current();
		$relObj = $relBo->find(array('fin_id = ?' => $fin_id))->current();
		$ticketObj->fin_descricao = str_replace(array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), '', $ticketObj->fin_descricao);

		$proprietarioSession = new Zend_Session_Namespace('proprietario');

		$pdf = new App_Util_Pdf(null, null, true);

		$this->view->ticket = $ticketObj;
		$this->view->agrupado = $agrupadorObj;
		$this->view->sis = $proprietarioSession->proprietario;
		$this->view->folha = $folhaObj;
		$this->view->funcional = $funcionaisObj;
		$this->view->admissao = $admissaoObj;
		$this->view->cbo = $cboObj;
		$this->view->rel = $relObj;

		$html = $this->view->render('folha-de-pagamento/recibo-de-pagamento.phtml');

		$pdf->modificarFonte('helvetica', 10);
		$pdf->adicionarHtml($html);
		$pdf->abrirArquivo();exit();
	}

	public function oleriteAction(){

		$this->_helper->layout->disableLayout();

		$id = $this->_getParam('id');

		$dados = $this->_bo->dadosOlerite($id);

		$pdf = new App_Util_Pdf(null, null, true);

		$this->view->dados = $dados;

		$html = $this->view->render('folha-de-pagamento/olerite.phtml');

		$pdf->modificarFonte('helvetica', 10);
		$pdf->adicionarHtml($html);
		$pdf->abrirArquivo();exit();
	}

	public function oleritesAction(){

		$this->_helper->layout->disableLayout();

		$data = new Zend_Date($this->_getParam('data'));
		$data = $data->toString('yyyy-MM-dd');

		$dados = $this->_bo->multiplosOlerite($data);

		$pdf = new App_Util_Pdf(null, null, true);

		foreach ($dados as $dados) :
			$this->view->dados = $dados;
			$html = $this->view->render('folha-de-pagamento/olerites.phtml');
			$pdf->modificarFonte('helvetica', 10);
			$pdf->adicionarHtml($html);
			$pdf->pagBreak();
		endforeach;

		$pdf->abrirArquivo();exit();
	}

	public function migraAction(){

		$this->noRenderAndNoLayout();
		$financialBo = new Financial_Model_Bo_Financial();
		$this->_helper->json($financialBo->migraTkAndRh($this->_getAllParams()));

	}

	public function relatorioAction(){

		$this->_helper->layout->disableLayout();

		$data = new Zend_Date($this->_getParam('data'));
		$data = $data->toString('yyyy-MM-dd');

		$relRFBo = new Rh_Model_Bo_ReferenciaFinanceiroModelo();

		$pdf = new App_Util_Pdf(null, null);

		$this->view->provento = $relRFBo->totalProventoAndDesconto('1', $data ? $data : null);
		$this->view->desconto = $relRFBo->totalProventoAndDesconto('2', $data ? $data : null);
		$this->view->dados = $this->_bo->multiplosOlerite($data);

		$html = $this->view->render('folha-de-pagamento/relatorio.phtml');

		$pdf->modificarFonte('helvetica', 10);
		$pdf->adicionarHtml($html);
		$pdf->abrirArquivo();exit();
	}

	public function deleteAction()
	{
		$this->noRenderAndNoLayout();

		//verifica se e pelo ajax
		if($this->getRequest()->isXmlHttpRequest()){

			$agrupadorBo = new Financial_Model_Bo_AgrupadorFinanceiro();
			$tksBo = new Financial_Model_Bo_Financial();

			$folhaObj = $this->_bo->get($this->_id);
			$tssObj = $tksBo->find(array('id_agrupador_financeiro = ?'=> $folhaObj['tss_id']));
			$tseObj = $tksBo->find(array('id_agrupador_financeiro = ?'=> $folhaObj['tse_id']));

			foreach ($tssObj as $tss){
				$tksBo->inativar($tss['fin_id']);
			}
			foreach ($tseObj as $tse){
				$tksBo->inativar($tse['fin_id']);
			}

			$agrupadorBo->inativar($folhaObj['tss_id']);
			$agrupadorBo->inativar($folhaObj['tse_id']);
			$this->_bo->inativar($this->_id);
			$response = $this->_mensagemJson();

			$this->_helper->json($response);
		} else {
			$agrupadorBo = new Financial_Model_Bo_AgrupadorFinanceiro();
			$tksBo = new Financial_Model_Bo_Financial();

			$folhaObj = $this->_bo->get($this->_id);
			$tssObj = $tksBo->find(array('id_agrupador_financeiro = ?'=> $folhaObj['tss_id']));
			$tseObj = $tksBo->find(array('id_agrupador_financeiro = ?'=> $folhaObj['tse_id']));

			foreach ($tssObj as $tss){
				$tksBo->inativarSemMSG($tss['fin_id']);
			}
			foreach ($tseObj as $tse){
				$tksBo->inativarSemMSG($tse['fin_id']);
			}

			$agrupadorBo->inativarSemMSG($folhaObj['tss_id']);
			$agrupadorBo->inativarSemMSG($folhaObj['tse_id']);
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
}