<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  11/06/2013
 */
class Financial_Model_Bo_Financial extends App_Model_Bo_Abstract
{
    /**
     * @var Financial_Model_Dao_Financial
     */
    protected $_dao;

    public $fields =  array(	'fin_id'     => "Código",
    		'emp.nome_razao'     => "Entidade",
    		'fin_descricao'    => 'Descrição',
    		'con_codnome'    => 'Conta',
    		'fin_emissao'      => 'Emissao',
    		'fin_vencimento'      => 'Vencimento',
    		'fin_compensacao'	=>'Compensação',
    		'fin_nota_fiscal'      => 'Nota Fiscal',
    		'nome'      => 'Grupo',
    		'fin_valor'      => 'Valor',
    		'tipo_conta'	=> 'Tipo Financeiro'/*,
    		App_Model_Dao_Abstract::STRING_SEARCH        => 'Todos'*/
    );
    public $columns = array( 	'fin_id'     => "Código",
    		'nome_razao'     => "Entidade",
    		'fin_descricao'    => 'Descrição',
    		'tipo_conta'		=> 'Tipo Financeiro',
    		'con_codnome'    => 'Conta',
    		'fin_emissao'      => 'Emissao',
    		'fin_vencimento'      => 'Vencimento',
    		'fin_compensacao'	=>'Compensação',
    		'fin_nota_fiscal'      => 'Nota Fiscal',
    		'nome'      => 'Workspace',
    		'fin_valor'      => 'Valor'
    );

    public function __construct()
    {
        $this->_dao = new Financial_Model_Dao_Financial();
        parent::__construct();
    }

    public function formatDateFinancial($allParams){


    	foreach ($allParams as $key => $params){

    		if ($key == "data_emissao" || $key == "data_emissao2" || $key == "data_vencimento" || $key == "data_vencimento2" || $key == "data_compensacao" || $key == "data_compensacao2" ){

    			if ($params != ""){

    				$date = new Zend_Date($params);
    				$allParams[$key] = $date->toString('yyyy/MM/dd');
    			}

    		}

    	}
    	return $allParams;
    }

    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
    	if ($object->fin_valor){
    		$object->fin_valor = $this->_formatDecimal($object->fin_valor);
    	}
    	if ($object->fin_vencimento != ""){
    		$fin_vencimento = new Zend_Date($object->fin_vencimento);
    		$object->fin_vencimento = $fin_vencimento->toString('yyyy/MM/dd');
    	}
    	if ($object->fin_compensacao != ""){
    		$fin_compensacao = new Zend_Date($object->fin_compensacao);
    		$object->fin_compensacao = $fin_compensacao->toString('yyyy/MM/dd');
    	}
    	if ($object->fin_competencia != ""){
    		$fin_competencia = new Zend_Date($object->fin_competencia);
    		$object->fin_competencia = $fin_competencia->toString('yyyy/MM/dd');
    	}
    	if ($object->fin_emissao != ""){
    		$fin_emissao = new Zend_Date($object->fin_emissao);
    		$object->fin_emissao = $fin_emissao->toString('yyyy/MM/dd');
    	}

    }

    protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null){

    	if (!empty($request['rh'])) {

    		$agrupadoBo = new Financial_Model_Bo_AgrupadorFinanceiro();
    		$refRhFinanceiroBo = new Rh_Model_Bo_ReferenciaFinanceiroModelo();

    		$ticketAgrupado = $this->find(array('id_agrupador_financeiro = ?' => $object->id_agrupador_financeiro, "ativo = ?" => App_Model_Dao_Abstract::ATIVO));

    		$totalAgrupador = '';

    		foreach ($ticketAgrupado as $totalTicket){
					$totalAgrupador = $totalAgrupador + $totalTicket->fin_valor;
    		}

    		$tsObj = $agrupadoBo->get($object->id_agrupador_financeiro);
    		$tsObj->fin_valor = $totalAgrupador;
    		$agrupadoBo->saveFromRequest($requestNull= array(), $tsObj);

    		$refRhFinanceiroBo->delReferencia($object->fin_id);

    		$refObj = $refRhFinanceiroBo->get();
    		$refObj->fin_id = $object->fin_id;
    		$refObj->id_rh_modelo_sintetico = $request['id_rh_modelo_sintetico'];
    		$refObj->referencia = $request['referencia'] == '' ? "" : $request['referencia'];
    		$refObj->vl_base = $request['vl_base'] == '' ? "" : $request['vl_base'];
    		$refObj->save();

    	}

    	if (isset ($request['empresa_sacado_selected'])){

    		$sacadoFinanceiroBo = new Financial_Model_Bo_SacadoFinanceiro();
    		$sacadoFinanceiroObj = $sacadoFinanceiroBo->get($object->fin_id);

	    	$sacadoFinanceiroObj->id_pessoa_empresa = $request['empresa_sacado_selected'];
	    	$sacadoFinanceiroObj->empresas_grupo_id = null;
	    	$sacadoFinanceiroObj->id_pessoa = null;
	    	$sacadoFinanceiroObj->tb_financeiro_fin_id = $object->fin_id;
	    	$sacadoFinanceiroObj->save();
    	}

    	$historico = new Financial_Model_Bo_HistoricoFinanceiro();
    	$historicoObj = $historico->get();

    	$historicoObj->setFromArray($object->toArray());
		$historicoObj->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->id;
		$historicoObj->dt_criacao = date('Y-m-d H:i:s');

    	try {

    		$historicoObj->save();

    	} catch (Exception $e) {

    		App_Validate_MessageBroker::addErrorMessage($e->getMessage());

    	}
    	if(!empty($object->id_financeiro_correlato)){
    	    /**
             * Cria um array com o financeiro que foi salvo e apago
             * aquelas chaves que nao serão utilizados
    	     */
    	    $financialArray = $object->toArray();
    	    unset($financialArray['fin_id']);
    	    unset($financialArray['id_financeiro_correlato']);
    	    unset($financialArray['id_agrupador_financeiro']);
    	    if(!empty($object->getAgrupadorFinanceiro()->transferencia)){
    	    	unset($financialArray['con_id']);
    	    }

    	    $financialCorrelato = $this->get($object->id_financeiro_correlato);
    	    $historicoCorrelatoObj = $historico->get();

    	    $financialCorrelato->setFromArray($financialArray);
    	    $financialCorrelato->save();

    	    $historicoCorrelatoObj->setFromArray($financialCorrelato->toArray());
    	    $historicoCorrelatoObj->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
    	    $historicoCorrelatoObj->dt_criacao = date('Y-m-d H:i:s');
    	    $historicoCorrelatoObj->save();
    	}elseif (!empty($object->getAgrupadorFinanceiro()->id_agrupador_financeiro) && empty($object->id_financeiro_correlato)){
    	    /**
             * Cria um array com o financeiro que foi salvo e apago
             * aquelas chaves que nao serão utilizados
    	     */
    	    $financialArray = $object->toArray();
    	    unset($financialArray['fin_id']);
    	    $financialArray['id_financeiro_correlato'] = $object->fin_id;
    	    $financialArray['id_agrupador_financeiro'] = $object->getAgrupadorFinanceiro()->id_agrupador_financeiro;

    	    $financialCorrelato = $this->get();
    	    $historicoCorrelatoObj = $historico->get();

    	    $financialCorrelato->setFromArray($financialArray);
    	    $financialCorrelato->save();

    	    $historicoCorrelatoObj->setFromArray($financialCorrelato->toArray());
    	    $historicoCorrelatoObj->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
    	    $historicoCorrelatoObj->dt_criacao = date('Y-m-d H:i:s');
    	    $historicoCorrelatoObj->save();
    	    $object->id_financeiro_correlato = $financialCorrelato->fin_id;
    	    $object->save();

    	}

    	$paramsSearch = new Zend_Session_Namespace('paramsSearch');
    	$paramsSearch->unsetAll();

    }

    public function preparacaoSacadoFinanceiro($object = null)
    {
	    $movimentoFinanceiro = new stdClass();

	    $movimentoFinanceiro->nomeEmpresa = null;
	    $movimentoFinanceiro->idEmpresa = null;
	    $movimentoFinanceiro->idEmpresaGrupo = null;
	    $movimentoFinanceiro->idFuncionario = null;

    	if ($object == null){

    		return $movimentoFinanceiro;
    	}

    	$movimentoFinanceiro->nomeEmpresa = $object->getSacadoFinanceiro()->getEmpresa()->nome_razao;
    	$movimentoFinanceiro->idEmpresa = $object->getSacadoFinanceiro()->getEmpresa()->id;
    	return $movimentoFinanceiro;

    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
    	/*if(empty($object->stf_id)){
    		App_Validate_MessageBroker::addErrorMessage('O campo de status está vazio.');
    		return false;
    	}*/
    	if(empty($object->fin_descricao)){
    		App_Validate_MessageBroker::addErrorMessage('O campo de descrição está vazio.');
    		return false;
    	}
    	if(empty($object->fin_valor)){
    		App_Validate_MessageBroker::addErrorMessage('O campo de valor está vazio.');
    		return false;
    	}

    	return true;
    }

    public function nextOrPreviousIdFinancial($id){

    	$paramsSearch = new Zend_Session_Namespace('paramsSearch');

    	if ($paramsSearch->paramsSearch){

    		return  $this->_dao->nextOrPreviousIdFinancial($id,$paramsSearch->paramsSearch);

    	}

    	return $this->_dao->nextOrPreviousIdFinancial($id);
    }

    public function findByEmpresa($idEmpresa)
    {
        $options = array('empresaList' => $idEmpresa);
        $select = $this->_dao->selectPaginator($options);
        return $this->_dao->getAdapter()->fetchAll($select);
    }

    public function findFinancialForGetLimiteAjax($id){

    	return  $this->_dao->findFinancialForGetLimiteAjax($id);

    }

    public function sumVlTotalByProcesso($idProcesso)
    {
        return $this->_dao->sumVlTotalByProcesso($idProcesso);
    }

    public function getExtrato($request){

    	if ($request['de_fin_inclusao']!=""){
    		$de_fin_inclusao = new Zend_Date($request['de_fin_inclusao'], 'dd/MM/yy');
    		$request['de_fin_inclusao'] = $de_fin_inclusao->toString('yyyy/MM/dd');
    	}
    	if ($request['ate_fin_inclusao']!=""){
    		$ate_fin_inclusao = new Zend_Date($request['ate_fin_inclusao'], 'dd/MM/yy');
    		$request['ate_fin_inclusao'] = $ate_fin_inclusao->toString('yyyy/MM/dd');
    	}

    	if ($request['de_fin_competencia']!=""){
    	    $de_fin_inclusao = new Zend_Date('01/'.$request['de_fin_competencia']);
    	    $request['de_fin_competencia'] = $de_fin_inclusao->toString('yyyy/MM/dd');
    	}
    	if ($request['ate_fin_competencia']!=""){
            $ate_fin_inclusao = new Zend_Date('01/'.$request['ate_fin_competencia']);
            $request['ate_fin_competencia'] = $ate_fin_inclusao->toString('yyyy/MM/dd');
    	}
    	if ($request['de_fin_compensacao']!=""){
    	    $de_fin_compensacao = new Zend_Date($request['de_fin_compensacao']);
    	    $request['de_fin_compensacao'] = $de_fin_compensacao->toString('yyyy/MM/dd');
    	}
    	if ($request['ate_fin_compensacao']!=""){
            $ate_fin_compensacao = new Zend_Date($request['ate_fin_compensacao']);
            $request['ate_fin_compensacao'] = $ate_fin_compensacao->toString('yyyy/MM/dd');
    	}
    	if ($request['de_fin_vencimento']!=""){
    	    $de_fin_vencimento = new Zend_Date($request['de_fin_vencimento']);
    	    $request['de_fin_vencimento'] = $de_fin_vencimento->toString('yyyy/MM/dd');
    	}
    	if ($request['ate_fin_vencimento']!=""){
            $ate_fin_vencimento = new Zend_Date($request['ate_fin_vencimento']);
            $request['ate_fin_vencimento'] = $ate_fin_vencimento->toString('yyyy/MM/dd');
    	}
    	return $this->_dao->getExtrato($request);
    }

    public function getFinancialListAtrasados($idAgrupador){

    	return $this->_dao->getFinancialListAtrasados($idAgrupador);
    }

    public function getFinancialListHoje($idAgrupador){

    	return $this->_dao->getFinancialListHoje($idAgrupador);
    }

    public function getFinancialListSeteDias($idAgrupador){

    	return $this->_dao->getFinancialListSeteDias($idAgrupador);
    }

    public function getFinancialListPago($idAgrupador){

    	return $this->_dao->getFinancialListPago($idAgrupador);
    }

    public function getFinancialPerModelsAction($options)
    {
    	$select = $this->_dao->selectPaginator($options);
    	return $this->_dao->getAdapter()->fetchAll($select);
    }

    public function duplicarFinancial($idAgrupadorFinanceiro, $novoAgrupadorFinanciero, $dtCompetencia = null, $dtVencimento = null){

    	$refRhFinanceiroBo = new Rh_Model_Bo_ReferenciaFinanceiroModelo();
    	$agrupadorBo = new Financial_Model_Bo_AgrupadorFinanceiro();

    	$tk = $this->find(array('id_agrupador_financeiro = ?' => $idAgrupadorFinanceiro, "ativo = ?" => App_Model_Dao_Abstract::ATIVO));
    	$agrupadoObj = $agrupadorBo->find(array('id_agrupador_financeiro = ?' => $novoAgrupadorFinanciero))->current();

    	$dtCompetencia = new Zend_Date($dtCompetencia);
    	$dtVencimento = new Zend_Date($dtVencimento);
	    	foreach ($tk as $ticktNovo){

		    	try {

		    		$novoTk = $this->get();
		    		$novoTk->tid_id = $ticktNovo['tid_id'];
		    		$novoTk->tie_id = $ticktNovo['tie_id'];
		    		$novoTk->fin_vencimento = $dtVencimento->toString('yyyy/MM/dd');
		    		$novoTk->fin_competencia = $dtCompetencia->toString('yyyy/MM/dd');
		    		$novoTk->fin_descricao = $ticktNovo['fin_descricao'];
		    		$novoTk->fin_valor = $ticktNovo['fin_valor'];
		    		$novoTk->fin_emissao = date('Y-m-d H:i:s');
		    		$novoTk->fin_observacao = $ticktNovo['fin_observacao'];
		    		$novoTk->ope_id = $ticktNovo['ope_id'];
		    		$novoTk->plc_id = $ticktNovo['plc_id'];
		    		$novoTk->id_pessoa_faturado = $ticktNovo['id_pessoa_faturado'];
		    		$novoTk->fin_numero_doc = $ticktNovo['fin_numero_doc'];
		    		$novoTk->fin_num_doc_os = $ticktNovo['fin_num_doc_os'];
		    		$novoTk->cec_id = $ticktNovo['cec_id'];
		    		$novoTk->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->id;
		    		$novoTk->id_agrupador_financeiro = $novoAgrupadorFinanciero;

		    		$tieckSalvo = $this->saveFromRequest($request = array(), $novoTk);

		    		$modeloSintetico = $refRhFinanceiroBo->find(array('fin_id = ?' => $ticktNovo['fin_id']))->current();

		    		$novoModeloSintetico = $refRhFinanceiroBo->get();
		    		$novoModeloSintetico->fin_id = $novoTk->fin_id;
		    		$novoModeloSintetico->id_rh_modelo_sintetico = $modeloSintetico['id_rh_modelo_sintetico'];
		    		$novoModeloSintetico->referencia = $modeloSintetico['referencia'];
		    		$novoModeloSintetico->vl_base = $modeloSintetico['vl_base'];
		    		$novoModeloSintetico->save();

		    		$sacadoFinanceiroBo = new Financial_Model_Bo_SacadoFinanceiro();
		    		$sacadoFinanceiroObj = $sacadoFinanceiroBo->get();

                    $sacadoFinanceiroObj->id_pessoa_empresa = $agrupadoObj->id_pessoa_cliente;
                    $sacadoFinanceiroObj->empresas_grupo_id = null;
                    $sacadoFinanceiroObj->id_pessoa = null;


		    		$sacadoFinanceiroObj->tb_financeiro_fin_id = $novoTk->fin_id;
		    		$sacadoFinanceiroObj->save();

		    	} catch (Exception $e) {
		    		App_Validate_MessageBroker::addErrorMessage($e->getMessage());
		    	}

	    	}
    }

    public function deleteTicketRh($idTicket){

    	$resposta = array();

    	$agrupadorBo = new Financial_Model_Bo_AgrupadorFinanceiro();

    	$ticketObj = $this->find(array('fin_id = ?' => $idTicket))->current();

    	$this->_dao->getAdapter()->beginTransaction();

    	try {

	    	$agrupadorObj = $agrupadorBo->get($ticketObj->id_agrupador_financeiro);
	    	$agrupadorObj->fin_valor = $agrupadorObj->fin_valor - $ticketObj->fin_valor;
				$agrupadorObj->save();

	    	$this->inativar($idTicket);
	    	$this->_dao->getAdapter()->commit();
	    	return $resposta = array("success" => true, "type" => true);

    	} catch (Exception $e) {
    		$this->_dao->getAdapter()->rollBack();
    		return $resposta = array("success" => true, "type" => false);
    	}

    }

    public function tksOlerite($idAgrupador){

    	return $this->_dao->tksOlerite($idAgrupador);

    }

    public function allSearchFinancial($option = null)
    {
        $select = $this->_dao->selectPaginator($option);
        return $this->_dao->getAdapter()->fetchAll($select);
    }

    public function migraTkAndRh($dados){

    	$agrupadorBo = new Financial_Model_Bo_AgrupadorFinanceiro();
    	$modeloSinteticoBo = new Rh_Model_Bo_ModeloSintetico();
    	$refRhFinanceiroBo = new Rh_Model_Bo_ReferenciaFinanceiroModelo();

    	$velhaTk = $this->get($dados['fin_id']);

    	$contar = $this->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_agrupador_financeiro = ?' => $velhaTk->id_agrupador_financeiro))->count();

    	$this->_dao->getAdapter()->beginTransaction();

    	try {

	     	$novoTk = $this->get();
    		$novoTk->tid_id = $velhaTk->tid_id;
    		$novoTk->tie_id = $velhaTk->tie_id;
    		$novoTk->fin_vencimento = $velhaTk->fin_vencimento;
    		$novoTk->fin_competencia = $velhaTk->fin_competencia;
    		$novoTk->fin_descricao = $dados['fin_descricao'];
    		$novoTk->fin_valor = $velhaTk->fin_valor;
    		$novoTk->fin_emissao = $velhaTk->fin_emissao;
    		$novoTk->fin_observacao = $velhaTk->fin_observacao;
    		$novoTk->ope_id = $velhaTk->ope_id;
    		$novoTk->plc_id = $velhaTk->plc_id;
    		$novoTk->id_pessoa_faturado = $velhaTk->id_pessoa_faturado;
    		$novoTk->fin_numero_doc = $velhaTk->fin_numero_doc;
    		$novoTk->fin_num_doc_os = $velhaTk->fin_num_doc_os;
    		$novoTk->cec_id = $velhaTk->cec_id;
    		$novoTk->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->id;
    		$novoTk->id_agrupador_financeiro = $dados['id_agrupador_financeiro'];
    		$novoTk->save();

	    	$novoModeloSintetico = $refRhFinanceiroBo->get();
	    	$novoModeloSintetico->fin_id = $novoTk->fin_id;
	    	$novoModeloSintetico->id_rh_modelo_sintetico = $dados['id_rh_modelo_sintetico'];
	    	$novoModeloSintetico->referencia = $dados['referencia'];
	    	$novoModeloSintetico->save();

	    	$agrupador = $agrupadorBo->get($dados['id_agrupador_financeiro']);
	    	$agrupador->fin_valor = $agrupador->fin_valor + $novoTk->fin_valor;
	    	$agrupador->save();


	    	$this->_dao->getAdapter()->commit();
	    	$resposta = array("success" => true);

    	} catch (Exception $e) {
    		$this->_dao->getAdapter()->rollBack();
    		$resposta = array("success" => false);
    	}

    	if ($contar == 1) {
    		$this->inativar($dados['fin_id']);
    		$agrupadorBo->inativar($velhaTk->id_agrupador_financeiro);
    	} else {
    		$this->inativar($dados['fin_id']);
    	}

    	return $resposta;
    }


    /**
     * @todo entender esse codigo feito pelo Vinicius
     * talvez não é necessário mais
     * @param unknown $idAgrupador
     */
    public function tksOleriteFgts($idAgrupador){
    	return $this->_dao->tksOleriteFgts($idAgrupador);
    }

    /**
     * @desc inativa o dado a partir do id do dado. Adiciona a mensagem na classe App_Validate_MessageBroker
     * @param int $id
     */
    public function inativarSemMSG($id)
    {
    	return $this->_dao->inativar($id);
    }

    public function duplicarTks($fin_id, $dtVencimento = null, $dtCompetencia = null, $idAgrupador = null){

    	$tickt = $this->get($fin_id);

    	$agrupadorBo = new Financial_Model_Bo_AgrupadorFinanceiro();
    	$agrupadoObj = $agrupadorBo->find(array('id_agrupador_financeiro = ?' => $tickt->id_agrupador_financeiro))->current();


   		try {
   			$novoTk = $this->get();

   			if (!empty($idAgrupador)) {
   				$novoTk->id_agrupador_financeiro = $idAgrupador;
   			} else {
   				$novoTk->id_agrupador_financeiro = $tickt->id_agrupador_financeiro;
   			}

   			if (!empty($dtVencimento)) {
    			$novoTk->fin_vencimento = $dtVencimento;
   			} else {
    			$novoTk->fin_vencimento = $tickt['fin_vencimento'];;
   			}

   			if (!empty($dtCompetencia)) {
    			$novoTk->fin_competencia = $dtCompetencia;
   			} else {
    			$novoTk->fin_competencia = $tickt['fin_competencia'];;
   			}

    		$novoTk->tid_id = $tickt['tid_id'];
    		$novoTk->tie_id = $tickt['tie_id'];
    		$novoTk->fin_descricao = $tickt['fin_descricao'];
    		$novoTk->fin_valor = $tickt['fin_valor'];
    		$novoTk->fin_emissao = date('Y-m-d H:i:s');
    		$novoTk->fin_observacao = $tickt['fin_observacao'];
    		$novoTk->ope_id = $tickt['ope_id'];
    		$novoTk->plc_id = $tickt['plc_id'];
    		$novoTk->id_pessoa_faturado = $tickt['id_pessoa_faturado'];
    		$novoTk->fin_numero_doc = $tickt['fin_numero_doc'];
    		$novoTk->fin_num_doc_os = $tickt['fin_num_doc_os'];
    		$novoTk->cec_id = $tickt['cec_id'];
    		$novoTk->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->id;

   			$sacadoFinanceiroBo = new Financial_Model_Bo_SacadoFinanceiro();
   			$sacadoFinanceiroObj = $sacadoFinanceiroBo->get();

    		$this->saveFromRequest($request = array(), $novoTk);

            $sacadoFinanceiroObj->id_pessoa_empresa = $agrupadoObj->id_pessoa_cliente;
            $sacadoFinanceiroObj->empresas_grupo_id = null;
            $sacadoFinanceiroObj->id_pessoa = null;
   			$sacadoFinanceiroObj->tb_financeiro_fin_id = $novoTk->fin_id;
   			$sacadoFinanceiroObj->save();

   			$resposta = true;

   		} catch (Exception $e) {

   			App_Validate_MessageBroker::addErrorMessage('Entre em contato com o administrador. Error'.$e->getMessage());
   			return false;

   		}
    return $resposta;
    }
}