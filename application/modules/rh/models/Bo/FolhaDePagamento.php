<?php
/**
 * @author Vinicius Leônidas
 * @since 24/11/2013
 */
class Rh_Model_Bo_FolhaDePagamento extends App_Model_Bo_Abstract
{
	protected $_dao;

	public function __construct(){
		$this->_dao = new Rh_Model_Dao_FolhaDePagamento();
		parent::__construct();
	}

	protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		if(empty($object->id_empresa)){
			$folhaPagamento 	= $this->get($object->id_rh_folha_de_pagamento);
			$object->id_empresa = $folhaPagamento->id_empresa;
			if(empty($object->id_empresa)){
				App_Validate_MessageBroker::addErrorMessage('Funcionario não foi encontrado.');
				return false;
			}
		}
		if(empty($object->id_tp_pagamento)){
			App_Validate_MessageBroker::addErrorMessage('Selecione um tipo de folha.');
			return false;
		}

		$dataVerificada = $this->verificarData($object->id_empresa, $object->dt_competencia, $object->id_rh_folha_de_pagamento);

		if($dataVerificada['resposta'] == false && $object->id_tp_pagamento == Rh_Model_Bo_TipoPagamento::PRINCIPAL){
			App_Validate_MessageBroker::addErrorMessage('Já existe uma folha de pagamento para esse funcionário no mês cadastrado');
			return false;
		}
		return true;
	}

	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null){

		$agrupadoBo = new Financial_Model_Bo_AgrupadorFinanceiro();
		$request['id_empresa'] = $object->id_empresa;

		if ($object->dt_competencia != ""){
			$fin_vencimento = new Zend_Date($object->dt_competencia);
			$object->dt_competencia = $fin_vencimento->toString('yyyy/MM/dd');
		}

		$tssObj = $agrupadoBo->get($object->tss_id);
		$tssObj->id_workspace 	= $request['id_workspace'];
		$tssObj->id_empresa		= $object->id_empresa;
		$tssObj->fin_descricao 	= 'Salário referente ao mês '.$fin_vencimento->toString('MM/YY');
		$tssObj->fin_observacao = $request['observacao'];
		if(empty($tssObj->fin_valor)){
			$tssObj->fin_valor = '0.00';
		}
		$tssObj->tmv_id = 1;

		$agrupadoBo->saveFromRequest($request, $tssObj);

		$tseObj = $agrupadoBo->get($object->tse_id);
		$tseObj->id_workspace 	= $request['id_workspace'];
		$tseObj->id_empresa		= $object->id_empresa;
		$tseObj->fin_descricao 	= 'Salário referente ao mês '.$fin_vencimento->toString('MM/YY');
		$tseObj->fin_observacao = $request['observacao'];
		if(empty($tseObj->fin_valor)){
			$tseObj->fin_valor = '0.00';
		}
		$tseObj->tmv_id = 2;
		$agrupadoBo->saveFromRequest($request, $tseObj);

		$object->tss_id = $tssObj->id_agrupador_financeiro;
		$object->tse_id = $tseObj->id_agrupador_financeiro;

	}

	public function somaTotal($idTk){

		$ticketBo = new Financial_Model_Bo_Financial();

		$ticketAgrupado = $ticketBo->find(array('id_agrupador_financeiro = ?' => $idTk, "ativo = ?" => App_Model_Dao_Abstract::ATIVO));

		$totalAgrupador = '';

		foreach ($ticketAgrupado as $totalTicket){
			$totalAgrupador = $totalAgrupador + $totalTicket->fin_valor;
		}

		return $totalAgrupador;
	}

	public function duplicarFolha($idFolha, $dtCompetencia, $dtVencimento){

		$agrupadoBo = new Financial_Model_Bo_AgrupadorFinanceiro();
		$ticketBo = new Financial_Model_Bo_Financial();

		$folhaDePagamento = $this->find(array('id_rh_folha_de_pagamento = ?' => $idFolha))->current();

		$this->_dao->getAdapter()->beginTransaction();

		try {

			$novaTsSaida = $agrupadoBo->duplicarAgrupadorFinanceiro($folhaDePagamento->tss_id, $dtCompetencia);
			$novoTkSaida = $ticketBo->duplicarFinancial($folhaDePagamento->tss_id, $novaTsSaida['id_agrupador_financeiro'], $dtCompetencia, $dtVencimento);

			$novaTsEntrada = $agrupadoBo->duplicarAgrupadorFinanceiro($folhaDePagamento->tse_id, $dtCompetencia);
			$novoTkEntrada = $ticketBo->duplicarFinancial($folhaDePagamento->tse_id, $novaTsEntrada['id_agrupador_financeiro'], $dtCompetencia, $dtVencimento);

			$novaFolhaDePagamento = $this->get();
			$novaFolhaDePagamento->tss_id 			= $novaTsSaida['id_agrupador_financeiro'];
			$novaFolhaDePagamento->tse_id 			= $novaTsEntrada['id_agrupador_financeiro'];
			$novaFolhaDePagamento->id_workspace 	= $folhaDePagamento->id_workspace;
			$novaFolhaDePagamento->id_empresa 		= $folhaDePagamento->id_empresa;
			$novaFolhaDePagamento->descricao 		= $folhaDePagamento->descricao;
			$novaFolhaDePagamento->dt_competencia	= $dtCompetencia;
			$novaFolhaDePagamento->observacao 		= $folhaDePagamento->observacao;
			$novaFolhaDePagamento->plc_id 			= $folhaDePagamento->plc_id;
			$novaFolhaDePagamento->moe_id 			= $folhaDePagamento->moe_id;
			$novaFolhaDePagamento->cec_id 			= $folhaDePagamento->cec_id;
			$novaFolhaDePagamento->ope_id 			= $folhaDePagamento->ope_id;
			$novaFolhaDePagamento->grupo_id 		= $folhaDePagamento->grupo_id;
			$novaFolhaDePagamento->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
			$novaFolhaDePagamento->dt_criacao 		  = date('Y-m-d H:i:s');
			$novaFolhaDePagamento->save();
			$this->_dao->getAdapter()->commit();

		} catch (Exception $e) {
			$this->_dao->getAdapter()->rollBack();
			App_Validate_MessageBroker::addErrorMessage($e->getMessage());
		}
		return true;
	}

	public function verificarData($idEmpresa, $data, $idFolhaPagamento = null){

		$resposta = array();
		$criteria = array('id_empresa = ?' => $idEmpresa, "ativo = ?" => App_Model_Dao_Abstract::ATIVO, 'id_tp_pagamento = ?' => Rh_Model_Bo_TipoPagamento::PRINCIPAL);
		if($idFolhaPagamento)
			$criteria['id_rh_folha_de_pagamento <> ?'] = $idFolhaPagamento;
		
		$folhaDePagamento = $this->find($criteria);

		foreach ($folhaDePagamento as $folhas){

			$data = new Zend_Date($data);
			$dataComparada = new Zend_Date($folhas['dt_competencia']);

			$comparaData = $data->compare($dataComparada, 'yyyy-MM');

			if ($comparaData == 0) {
				return $resposta = array("success" => true, "resposta" => false, "message" => "Escolha outro mês");
			}
		}
		return $resposta = array("success" => true, "resposta" => true);
	}

	public function dadosOlerite($idFolha){

		$folhaObj = $this->get($idFolha);

		$ticketBo = new Financial_Model_Bo_Financial();
		$tseObj = $ticketBo->tksOlerite($folhaObj->tse_id);
		$tssObj = $ticketBo->tksOlerite($folhaObj->tss_id);

		$funcionarioBo = new Rh_Model_Bo_Funcionario();
		$funcionarioObj = $funcionarioBo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_empresa = ?' => $folhaObj->id_empresa))->current();

		$funcionaisBo = new Rh_Model_Bo_DadosFuncionais();
		$funcionaisObj = $funcionaisBo->find(array('id_rh_funcionario = ?' => $funcionarioObj->id_rh_funcionario))->current();

		$admissaoBo = new Rh_Model_Bo_Admissao();
		$admissaoObj = $admissaoBo->find(array('id_rh_funcionario = ?' => $funcionarioObj->id_rh_funcionario))->current();

		$cboBo = new Rh_Model_Bo_Cbo();
		$cboObj = $cboBo->find(array('id_rh_cbo = ?' => $funcionaisObj->id_rh_cbo))->current();

		$empresaBo = new Empresa_Model_Bo_Empresa();
		$empresaObj = $empresaBo->get($funcionarioObj->id_empresa);

		$sisConfigBo = new Sis_Model_Bo_Sis();
		$workspaceSession = new Zend_Session_Namespace('workspace');
		$sisConfigObj = $sisConfigBo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_workspace = ?'=>$workspaceSession->id_workspace))->current();

		$totais = array();

		$inss = '';
		$fgts = '';
		$irrf = '';
		$totalTss = '';
		$totalTse = '';

		foreach ($tssObj as $tss){
			if ($tss['inss'] == 1) {
				$inss = $inss + $tss['fin_valor'];
				$totais['inss'] = $inss;
			}
			if ($tss['fgts'] == 1) {
				$fgts = $fgts + $tss['fin_valor'];
				$totais['fgts'] = $fgts;
			}
			if ($tss['irrf'] == 1) {
				$irrf = $irrf + $tss['fin_valor'];
				$totais['irrf'] = $irrf;
			}

			$totalTss = $totalTss + $tss['fin_valor'];
			$totais['totalTss'] = $totalTss;

			$tss['fin_descricao'] = str_replace(array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), '', $tss['fin_descricao']);
		}

		foreach ($tseObj as $tse){

			$totalTse = $totalTse + $tse['fin_valor'];
			$totais['totalTse'] = $totalTse;

			$tse['fin_descricao'] = str_replace(array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), '', $tss['fin_descricao']);
		}

		$fgtsObj = $this->calcularFgts($totais['fgts']);

		$dadosOlerite = array('folha' => $folhaObj, 'valorFgts' => $fgtsObj, 'base' => $totais, 'tss' => $tssObj, 'tse' => $tseObj, 'funcionario' => $funcionarioObj, 'funcional' => $funcionaisObj,
				 'admissao' => $admissaoObj, 'cbo' => $cboObj, 'sis' => $sisConfigObj, 'corporativo' => $empresaObj);

		return $dadosOlerite;

	}

	public function multiplosOlerite($dataFolha){

		$workspaceSession = new Zend_Session_Namespace('workspace');

		$folhasObj = $this->find(array('dt_competencia = ?' => $dataFolha, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO, "id_workspace = ?" => $workspaceSession->id_workspace));

		$dadosOlerite = array();

		foreach ($folhasObj as $folha):

			$ticketBo = new Financial_Model_Bo_Financial();
			$tseObj = $ticketBo->tksOlerite($folha['tse_id']);
			$tssObj = $ticketBo->tksOlerite($folha['tss_id']);

			$funcionarioBo = new Rh_Model_Bo_Funcionario();
			$funcionarioObj = $funcionarioBo->find(array('id_empresa = ?' => $folha['id_empresa']))->current();

			$funcionaisBo = new Rh_Model_Bo_DadosFuncionais();
			$funcionaisObj = $funcionaisBo->find(array('id_rh_funcionario = ?' => $funcionarioObj->id_rh_funcionario))->current();

			$admissaoBo = new Rh_Model_Bo_Admissao();
			$admissaoObj = $admissaoBo->find(array('id_rh_funcionario = ?' => $funcionarioObj->id_rh_funcionario))->current();

			$cboBo = new Rh_Model_Bo_Cbo();
			$cboObj = $cboBo->get($funcionaisObj->id_rh_cbo);

			$empresaBo = new Empresa_Model_Bo_Empresa();
			$empresaObj = $empresaBo->get($funcionarioObj->id_empresa);

			$sisConfigBo = new Sis_Model_Bo_Sis();
			$sisConfigObj = $sisConfigBo->find()->current();

			$totais = array();

			$inss = '';
			$fgts = '';
			$irrf = '';
			$totalTss = '';
			$totalTse = '';

			foreach ($tssObj as $tss){
				if ($tss['inss'] == 1) {
					$inss = $inss + $tss['fin_valor'];
					$totais['inss'] = $inss;
				}
				if ($tss['fgts'] == 1) {
					$fgts = $fgts + $tss['fin_valor'];
					$totais['fgts'] = $fgts;
				}
				if ($tss['irrf'] == 1) {
					$irrf = $irrf + $tss['fin_valor'];
					$totais['irrf'] = $irrf;
				}

				$totalTss = $totalTss + $tss['fin_valor'];
				$totais['totalTss'] = $totalTss;

				$tss['fin_descricao'] = str_replace(array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), '', $tss['fin_descricao']);
			}

			foreach ($tseObj as $tse){

				$totalTse = $totalTse + $tse['fin_valor'];
				$totais['totalTse'] = $totalTse;

				$tse['fin_descricao'] = str_replace(array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), '', $tss['fin_descricao']);
			}

			$fgtsObj = $this->calcularFgts($totais['fgts']);
			$dadosOlerite[] = array('folha' => $folha, 'valorFgts' => $fgtsObj, 'base' => $totais, 'tss' => $tssObj, 'tse' => $tseObj, 'funcionario' => $funcionarioObj, 'funcional' => $funcionaisObj,
					'admissao' => $admissaoObj, 'cbo' => $cboObj, 'sis' => $sisConfigObj, 'corporativo' => $empresaObj);

		endforeach;

		return $dadosOlerite;

	}

	public function calcularFgts($baseFgts =null)
	{
	    $fgts = "0,00";
	    if($baseFgts){
	        $fgts = $baseFgts * 8 / 100;
	    }
	    return $fgts;

	}
}