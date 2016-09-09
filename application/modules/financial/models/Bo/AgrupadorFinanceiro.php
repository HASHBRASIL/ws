<?php
    /**
     * @author Carlos Vinicius Bonfim da Silva
     * @since  26/09/2013
     */
    class Financial_Model_Bo_AgrupadorFinanceiro extends App_Model_Bo_Abstract
    {
        /**
         * @var Financial_Model_Dao_AgrupadorFinanceiro
         */
        protected $_dao;

        public $fields =  array(
            'id_agrupador_financeiro'     => "ID",
            'nome_razao'      	=> 'Entidade',
            'fin_descricao'   	=> 'Descrição',
            'tmv_descricao'   => 'D/R',
            'nome'      		=> 'Time',
            'fin_valor'      	=> 'Valor'
        );
//        public $columns = array(
//            'id_agrupador_financeiro'     => "Código",
//            'nome_razao'     			  => "Entidade",
//            'fin_descricao'    			  => 'Descrição',
//            'fin_nota_fiscal'      		  => 'Nota Fiscal',
//            'nome'      				  => 'Time',
//            'fin_valor'      			  => 'Valor',
//            'pro_codigo'				  => 'Pedido',
//            'tipo_transacao' 			  => 'Tipo de Transação'
//        );

        public $fieldsFilter = array(
            'fin_valor'      			  => 'number_format',
        );

        public function __construct()
        {
            $this->_dao = new Financial_Model_Dao_AgrupadorFinanceiro();
            $this->_grupoVinculo = true;
            parent::__construct();
        }


        protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
        {
            if (empty($object->fin_descricao)) {
                throw new App_Validate_Exception($this->_translate->translate('O  campo de descrição está vazio.'));
            }
            if (empty($object->fin_valor)) {
                throw new App_Validate_Exception($this->_translate->translate('O campo de valor está vazio.'));
            }
            return true;

        }

        public function validateSplit($request)
        {

            if (empty($request['fin_valor'])) {
                throw new App_Validate_Exception($this->_translate->translate('O campo de valor está vazio.'));
            }
        }

        protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
        {
            if ($object->fin_valor){
                $object->fin_valor = $this->_formatDecimal($object->fin_valor);
            }
        }

        public function saveSplit($request)
        {

//            if ($request['id_grupo']) {
                $this->_dao->saveSplit($request, $request['id_grupo']);
//            }

//            $this->_dao->saveSplit($request);
        }

        protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
        {
            if ($request['transacao_conta_id']) {
                // salvar transacao conta ID

                $model = new Financial_Model_Dao_TransacaoConta();

                $row = $model->get($request['transacao_conta_id']);


                $row->vl_transacao_saldo += $object->fin_valor;

                if ($row->vl_transacao_saldo == $row->vl_transacao_conta) {
                    $row->st_transacao_conta = 1; // quitado
                }

                $row->save();
            }

        }
//
//	public function gridFinancialProcessoAjax($id){
//
//		return  $this->_dao->gridFinancialProcessoAjax($id);
//	}
//
//	public function saveTransfer($request){
//
//		$financeiroBo 			= new Financial_Model_Bo_Financial();
//		$relSacadofinanceiroBo 	= new Financial_Model_Bo_SacadoFinanceiro();
//		$contaBo 				= new Financial_Model_Bo_Contas();
//
//		$deConta 	= $contaBo->get($request["de_con_id"]);
//		$paraConta 	= $contaBo->get($request["para_con_id"]);
//
//		$mensagemTransferencia = "Transferência entre as contas {$deConta->con_codnome} e {$paraConta->con_codnome}, cujos sacados/credores são respectivamentes {$request["de_empresa_sacado"]} e {$request["para_empresa_sacado"]}.";
//
//		try{
//
//			$agrupadorOrigemObj = $this->get();
//			$agrupadorOrigemObj->tmv_id 		= Financial_Model_Bo_Contas::APAGAR;
//			$agrupadorOrigemObj->moe_id 		= $request["moe_id"];
//			$agrupadorOrigemObj->fin_valor 		= $this->_formatDecimal($request["fin_valor"]);
//			$agrupadorOrigemObj->fin_descricao 	= $mensagemTransferencia;
//			$agrupadorOrigemObj->id_pessoa_faturado 	= $request["id_grupo"] ;
//			$agrupadorOrigemObj->transferencia	= App_Model_Dao_Abstract::ATIVO;
//			$agrupadorOrigemObj->id_pessoa_cliente 	= $request["de_empresa_sacado_selected"];
//			$agrupadorOrigemObj->save();
//
//			$financeiroOrigemObj =  $financeiroBo->get();
//			$financeiroOrigemObj->id_agrupador_financeiro 	= $agrupadorOrigemObj->id_agrupador_financeiro;
//			$financeiroOrigemObj->fin_valor 				= $this->_formatDecimal($request["fin_valor"]);
//			$financeiroOrigemObj->con_id 					= $request["de_con_id"];
//			$financeiroOrigemObj->fin_descricao 			= $mensagemTransferencia;
//			if ($request['dateTransacao'] != ""){
//				$date = new Zend_Date($request['dateTransacao']);
//				$financeiroOrigemObj->fin_emissao 		= $date->toString('yyyy/MM/dd');
//				$financeiroOrigemObj->fin_vencimento 	= $date->toString('yyyy/MM/dd');
//				$financeiroOrigemObj->fin_compensacao 	= $date->toString('yyyy/MM/dd');
//			}
//			$financeiroOrigemObj->save();
//
//			$sacadoFinanceiroOrigemObj = $relSacadofinanceiroBo->get();
//            $sacadoFinanceiroOrigemObj->id_pessoa_empresa = $request['de_empresa_sacado_selected'];
//            $sacadoFinanceiroOrigemObj->empresas_grupo_id = null;
//            $sacadoFinanceiroOrigemObj->id_pessoa = null;
//			$sacadoFinanceiroOrigemObj->tb_financeiro_fin_id 	= $financeiroOrigemObj->fin_id;
//			$sacadoFinanceiroOrigemObj->save();
//
//			$agrupadorDestinoObj = $this->get();
//			$agrupadorDestinoObj->tmv_id 			= Financial_Model_Bo_Contas::ARECEBER;
//			$agrupadorDestinoObj->moe_id 			= $request["moe_id"];
//			$agrupadorDestinoObj->fin_valor 		= $this->_formatDecimal($request["fin_valor"]);
//			$agrupadorDestinoObj->fin_descricao 	= $mensagemTransferencia;
//			$agrupadorDestinoObj->id_pessoa_faturado 		= $request["id_grupo"] ;
//			$agrupadorDestinoObj->transferencia 	= App_Model_Dao_Abstract::ATIVO;
//			$agrupadorDestinoObj->id_pessoa_cliente 		= $request["para_empresa_sacado_selected"];
//			$agrupadorDestinoObj->id_agrupador_financeiro_correlato = $agrupadorOrigemObj->id_agrupador_financeiro;
//			$agrupadorDestinoObj->save();
//
//			$financeiroDestinoObj =  $financeiroBo->get();
//			$financeiroDestinoObj->id_agrupador_financeiro 		= $agrupadorDestinoObj->id_agrupador_financeiro;
//			$financeiroDestinoObj->fin_valor 					= $this->_formatDecimal($request["fin_valor"]);
//			$financeiroDestinoObj->con_id 						= $request["para_con_id"];
//			$financeiroDestinoObj->fin_descricao 				= $mensagemTransferencia;
//			$financeiroDestinoObj->id_financeiro_correlato		= $financeiroOrigemObj->fin_id;
//			if ($request['dateTransacao'] != ""){
//				$date = new Zend_Date($request['dateTransacao']);
//				$financeiroDestinoObj->fin_emissao 		= $date->toString('yyyy/MM/dd');
//				$financeiroDestinoObj->fin_vencimento 	= $date->toString('yyyy/MM/dd');
//				$financeiroDestinoObj->fin_compensacao	= $date->toString('yyyy/MM/dd');
//			}
//			$financeiroDestinoObj->save();
//
//			$sacadoFinanceiroDestinoObj = $relSacadofinanceiroBo->get();
//            $sacadoFinanceiroDestinoObj->id_pessoa_empresa = $request['para_empresa_sacado_selected'];
//            $sacadoFinanceiroDestinoObj->empresas_grupo_id = null;
//            $sacadoFinanceiroDestinoObj->id_pessoa = null;
//			$sacadoFinanceiroDestinoObj->tb_financeiro_fin_id 	= $financeiroDestinoObj->fin_id;
//			$sacadoFinanceiroDestinoObj->save();
//
//			$agrupadorOrigemObj->id_agrupador_financeiro_correlato 	= $agrupadorDestinoObj->id_agrupador_financeiro;
//			$financeiroOrigemObj->id_financeiro_correlato			= $financeiroDestinoObj->fin_id;
//			$agrupadorOrigemObj->save();
//			$financeiroOrigemObj->save();
//
//
//			$result = array('success' => true);
//
//		}catch (Exception $e){
//
//			$result = array('success' => false, 'response' => $e->getMessage());
//
//		}
//			return $result;
//
//	}
//
//	public function getPairsByProcesso($ativo = true, $chave = null, $valor = null,
//			$ordem = null, $limit = null, $idProcesso)
//	{
//		$where = null;
//		if($ativo){
//			$where = array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "pro_id = ?" => $idProcesso);
//		}
//		return $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
//	}
//
//	public function getFinancialWithProcessoIncompatiblePerModels(){
//	    $processoBo = new Processo_Model_Bo_Processo();
//	    $options = Array();
//	    return $processoBo->selectByPendencia($options);
//
//	}
//
//	public function duplicarAgrupadorFinanceiro($idAgrupadorFinanceiro, $dtCompetencia = null){
//
//		$antigaTs = $this->find(array('id_agrupador_financeiro = ?' => $idAgrupadorFinanceiro))->current();
//
//		$novaTs = $this->get();
//
//		if (!empty($dtCompetencia)){
//			$fin_vencimento = new Zend_Date($dtCompetencia);
//			$novaTs->fin_descricao = 'Salário referente ao mês '.$fin_vencimento->toString('MM/YY');
//		} else {
//			$novaTs->fin_descricao = $antigaTs['fin_descricao'];
//		}
//
//		$novaTs->id_grupo = $antigaTs['id_grupo'];
//		$novaTs->id_pessoa_cliente = $antigaTs['id_pessoa_cliente'];
//		$novaTs->fin_valor = $antigaTs['fin_valor'];
//		$novaTs->pro_id = $antigaTs['pro_id'];
//		$novaTs->fin_observacao = $antigaTs['fin_observacao'];
//		$novaTs->plc_id = $antigaTs['plc_id'];
//		$novaTs->tmv_id = $antigaTs['tmv_id'];
//		$novaTs->moe_id = $antigaTs['moe_id'];
//		$novaTs->cec_id = $antigaTs['cec_id'];
//		$novaTs->ope_id = $antigaTs['ope_id'];
//		$novaTs->id_pessoa_faturado = $antigaTs['id_pessoa_faturado'];
//		$novaTs->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->id;
//		$novaTs->dt_criacao = date('Y-m-d H:i:s');
//
//		$request = array();
//
//		try {
//
//			$this->saveFromRequest($request, $novaTs);
//
//		} catch (Exception $e) {
//			App_Validate_MessageBroker::addErrorMessage($e->getMessage());
//		}
//
//		return $novaTs;
//	}
//
//	/**
//	 * @desc inativa o dado a partir do id do dado. Adiciona a mensagem na classe App_Validate_MessageBroker
//	 * @param int $id
//	 */
//	public function inativarSemMSG($id)
//	{
//		return $this->_dao->inativar($id);
//	}


    public function processUpload($idMaster, $tipo)
    {
        $boPessoa = new Legacy_Model_Bo_Pessoa();

        $rs = $this->_dao->getNota($idMaster);

        $data = array();

        foreach ($rs as $row) {
            if (($row['metanome'] == 'cnpjdest') || ($row['metanome'] == 'cnpjemit')) {
                $data[$row['metanome']] = $boPessoa->getPessoaByCpfCnpj($row['valor']);
            } else {
                $data[$row['metanome']] = $row['valor'];

            }
        }

        $row = $this->_dao->salvarTransacao($data, $tipo);

        $boFinanceiro = new Relatorio_Model_Bo_Financeiro();

        $boFinanceiro->processaTransacao($row, $data, $tipo);
    }

    public function getTransacaoAberta()
    {
        return $this->_dao->getTransacaoAberta();
    }

}
