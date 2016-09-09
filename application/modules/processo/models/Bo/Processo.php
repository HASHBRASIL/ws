<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/05/2013
 */
class Processo_Model_Bo_Processo extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_Processo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_Processo();
        parent::__construct();
        $this->_hasWorkspace = true;
        $this->_getRegistersWithoutWorkspace = false;

        $workspaceSession = new Zend_Session_Namespace('workspace');

        if ($workspaceSession->free_access){
            $this->columns['name_workspace'] = 'name_workspace';
        }
    }

    /**
     * @param boolean $ativo
     * @return array ($key => $value)
     */
    public function getPairs($ativo = true, $chave = null, $valor = null,
                            $ordem = null, $limit = null )
    {
        $where = null;
        if($ativo){
            $where = array("ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }
        if ($this->_hasWorkspace) {

            $workspaceSession = new Zend_Session_Namespace('workspace');

            if (!$workspaceSession->id_workspace){
                $array = array();
                return $array ;
            }

            if ($workspaceSession->free_access != true){
                if ($this->_getRegistersWithoutWorkspace){
                    $where["id_workspace IS NULL or id_workspace = {$workspaceSession->id_workspace} "] =  "";
                }else{
                    $where["id_workspace = ?"] = $workspaceSession->id_workspace;
                }
            }
        }

        $list = $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
        foreach ($list as $key => &$value){
            if(strlen($value) > 100){
                $value = substr($value, 0,97)." ...";
            }
        }
        return $list;

    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
    	if(!empty($request['pro_data_entrega'])){
    		$dataHora = explode(" ", $request['pro_data_entrega']);
    		$date = new Zend_Date($dataHora[0]);
    		$dataHora[0] = $date->toString('yyyy/MM/dd');
    		$object->pro_data_entrega = $dataHora[0]." ".$dataHora[1];
    	}

    	if ($object->pro_vlr_unt){
    		$object->pro_vlr_unt = $this->_formatDecimal($object->pro_vlr_unt);
    	}
    	if ($object->pro_vlr_pedido){
    		$object->pro_vlr_pedido = $this->_formatDecimal($object->pro_vlr_pedido);
    	}
    	if ($object->pro_quantidade){
    		$object->pro_quantidade = str_replace(".", "", $object->pro_quantidade);
    	}

    	if (!$object->pro_codigo){
	    	$this->gerarCodigo($object);
    	}
    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null){

    	if(empty($object->sta_id)){
    		App_Validate_MessageBroker::addErrorMessage('O campo de status está vazio.');
    		return false;
    	}
    	if(empty($object->pro_quantidade)){
    		App_Validate_MessageBroker::addErrorMessage('O campo de quantidade a produzir está vazio.');
    		return false;
    	}
    	if(empty($object->pes_id) && empty($object->empresas_grupo_id) && empty($object->empresas_id)){
    		App_Validate_MessageBroker::addErrorMessage('O campo de cliente está vazio.');
    		return false;
    	}
    	if(empty($object->pro_desc_produto)){
    	    App_Validate_MessageBroker::addErrorMessage('O campo de descrição está vazio.');
    	    return false;
    	}

    	return true;
    }

    public function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {

        if(isset($request['entrega']) && $request['entrega'] && count($request['quantidade'])){
            $loteProducaoBo = new Processo_Model_Bo_LoteProducao();
            $loteProducaoBo->inativarByProcesso($object->pro_id);
            for ($i = 0; $i < count($request['quantidade']); $i++ ){
                $loteProducao     = $loteProducaoBo->get($request['id_lote_producao'][$i]);

                $loteProducao->id_processo             = $object->pro_id;
                $loteProducao->cod_lote                = $loteProducao->cod_lote ? $loteProducao->cod_lote : $loteProducaoBo->idMaxByProcesso($object->pro_id)+1 ;
                $loteProducao->quantidade              = str_replace('.', '', $request['quantidade'][$i]);
                $loteProducao->dt_entrega              = $this->date($request['dt_entrega'][$i], 'yyyy-MM-dd HH:mm:ss');
                $loteProducao->id_empresa              = $request['id_empresa_entrega'][$i];
                $loteProducao->id_criacao_usuario      = Zend_Auth::getInstance()->getIdentity()->id;
                $loteProducao->dt_criacao              = date('Y-m-d H:i:s');
                $loteProducao->ativo                   = App_Model_Dao_Abstract::ATIVO;
                $loteProducao->save();
            }
        }

    	/**
         * Salva o historico do processo
    	 */
        $historicoBo = new Processo_Model_Bo_Historico();
        $historico = $historicoBo->get();
        $historico->setFromArray($object->toArray());
        $historico->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->id;
        $historico->dt_criacao = date('Y-m-d H:i:s');
        $historico->save();

        /**
         * Ao criar um processo ele cria uma tk é um ts
         */

        $agrupadorFinanceiroBo = new Financial_Model_Bo_AgrupadorFinanceiro();

        if(empty($request['pro_id']) && $object->pro_vlr_pedido != '0.00' ){
            $financialBo           = new Financial_Model_Bo_Financial();

            $agrupadorFinanceiro     = $agrupadorFinanceiroBo->get();
            $financial               = $financialBo->get();

            $agrupadorFinanceiro->pro_id            = $object->pro_id;
            // @todo verificar qual campo é empresa no agrupfinanceiro
            $agrupadorFinanceiro->id_empresa        = $object->empresas_id;

            $agrupadorFinanceiro->id_grupo      = $object->id_grupo;
            $agrupadorFinanceiro->fin_descricao     = $object->pro_desc_produto;
            $agrupadorFinanceiro->fin_valor         = $object->pro_vlr_pedido;
            $agrupadorFinanceiro->moe_id            = Financial_Model_Bo_Moeda::REAL;
            $agrupadorFinanceiro->tmv_id            = Financial_Model_Bo_TipoMovimento::CREDITO;

            $request = array();
            $agrupadorFinanceiroBo->saveFromRequest($request, $agrupadorFinanceiro);

            $financial->id_agrupador_financeiro = $agrupadorFinanceiro->id_agrupador_financeiro;
            $financial->fin_valor               = $object->pro_vlr_pedido;
            $financial->fin_competencia         = date('Y/m/').'01';
            $financial->fin_descricao           = $object->pro_desc_produto;
            $date = new Zend_Date();
            $financial->fin_emissao             = $date->toString('yyyy/MM/dd');
            $date->setDay(1);
            $financial->fin_competencia         = $date->toString('yyyy/MM/dd');
            $date->addMonth(1)->subDate(1);
            $financial->fin_vencimento          = $date->toString('yyyy/MM/dd');
            $request['empresa_sacado_selected'] = $object->empresas_id;

            $financialBo->saveFromRequest($request, $financial);

        }

        $this->sendProcesso($object);
    }

    public function getProcessoByStatus($options = null)
    {

        $criteria = null;
        if(isset($options['statusList'])){
            $criteria = array('sta_id in(?)' => $options['statusList'] );
        }
        $statusBo = new Processo_Model_Bo_Status();
        $statusList = $statusBo->find($criteria);
        $processoStatusList = array();
        if($statusList){
            foreach ($statusList as $status){
                $processoList     = $this->_dao->getProcessoByStatus($status->sta_id);
                if(count($processoList)){
                    $processoStatusList[$status->sta_id]['descricao']     = $status->sta_descricao;
                    $processoStatusList[$status->sta_id]['list']          = $processoList;
                }
            }
        }
        return $processoStatusList;
    }

    /**
     * Gera paginacao
     * @param mixed $data
     * @param array $options
     * @return Zend_Paginator
     * @throws Exception if data type invalid
     */
    public function paginatorByPendencia( array $options)
    {
        $data = $this->_dao->selectPaginatorByPendencia($options);

        $paginator = Zend_Paginator::factory($data);
        $paginator->setCurrentPageNumber(
                isset($options['page'])
                ? $options['page']
                : 1
        )->setItemCountPerPage(
                isset($options['itens'])
                ? $options['itens']
                : 250
        )->setPageRange(PHP_INT_MAX);

        if( isset( $options[ "searchString" ] ) && empty( $options[ "searchString" ] ) ){
            unset( $options[ "searchString" ] );
            unset( $options[ "search" ] );
            unset( $options[ "searchField" ] );
        }

        return $paginator;
    }

    public function selectByPendencia( array $options)
    {

    	$select =  $this->_dao->selectPaginatorByPendencia($options);
    	return $this->_dao->getAdapter()->fetchAll($select);
    }

    public function searchProcessoWithFinancial($request)
    {
        $criteria = array();
        if(isset($request['id_empresa'])){
            $criteria['empresas_id in(?)'] = $request['id_empresa'];
        }
        if(isset($request['id_status'])){
            $criteria['sta_id in(?)'] = $request['id_status'];
        }
        if(isset($request['dt_inicio'])  && isset($request['dt_fim'])){
            $dt_inicio  = $this->date($request['dt_inicio'], 'yyyy/MM/dd');
            $dt_fim     = $this->date($request['dt_fim'], 'yyyy/MM/dd');
            $criteria["pro_data_inc between '{$dt_inicio}' and '{$dt_fim}' "] = null;
        }

        $processoObj = $this->find($criteria);
        $financialBo = new Financial_Model_Bo_Financial();
        $processoList = array();
        if(count($processoObj) <= 0){
            return $processoList;
        }
        foreach ($processoObj as $processo){
            $processoList[$processo->pro_id]['processo'] = $processo;
            if(count($processo->getAgrupadoFinancialList()) > 0){
                foreach ($processo->getAgrupadoFinancialList() as $financial){
                    $processoList[$processo->pro_id]['financial'][$financial->id_agrupador_financeiro]['list'] = $financial;
                    if($request['compensado'] == 1){
                        $criteria = array(
                                'id_agrupador_financeiro = ?' => $financial->id_agrupador_financeiro,
                                'ativo = ?' => App_Model_Dao_Abstract::ATIVO,
                                'fin_compensacao IS NOT NULL',
                                'con_id IS NOT NULL'
                        );
                        $processoList[$processo->pro_id]['financial'][$financial->id_agrupador_financeiro]['ticket'] = $financialBo->find($criteria);
                    }else{
                        $criteria = array('id_agrupador_financeiro = ?' => $financial->id_agrupador_financeiro, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);
                        $processoList[$processo->pro_id]['financial'][$financial->id_agrupador_financeiro]['ticket'] = $financialBo->find($criteria);
                    }

                }
            }else{
                //as transações estão vazias
                $processoList[$processo->pro_id]['financial'] = array();
                // os ticket estão vazios
                $processoList[$processo->pro_id]['ticket']    = array();
            }
        }

        return $processoList;
    }

    public function sendProcesso(Processo_Model_Vo_Processo $processo)
    {
        $mail = new App_Util_Mail();
        $mail->setFrom('no-reply@titaniumtech.com.br' , utf8_decode('ERP | Sistemas Integrados de Gestão Empresarial') )
        ->setSubject( utf8_decode(  "Novo processo criado {$processo->pro_codigo}" ) );

        $proprietarioSession = new Zend_Session_Namespace('proprietario');

        $mailView = new Zend_View();

        if(count($proprietarioSession->proprietario)>0){
            $mailView->logo    		  = $proprietarioSession->proprietario['logo'];
        }else{
            $mailView->logo    		  = "erp_logo.png"/*Imagem padrao pre definida*/;
        }

        $historicoBo = new Processo_Model_Bo_Historico();
        $mailView->processo          = $processo;
        $mailView->historicoList     = $historicoBo->find(array('pro_id =?' => $processo->pro_id), 'dt_criacao DESC');
        $mailView->historicoArray    = $historicoBo->find(array('pro_id = ?' => $processo->pro_id), 'dt_criacao DESC');

        $mailView->setScriptPath(APPLICATION_PATH.'/modules/processo/views/scripts/');
        $mailView->addHelperPath('App/Views/Helpers/');

        $html = $mailView->render( 'processo/send-email-new.phtml' );
        $mail->setBodyHtml( utf8_decode( $html ) );

        $email = array('ellyson@agtic.com.br', 'ceo@grupoagbr.com.br');
        $mail->setBodyHtml( $html, 'UTF-8' );
        $mail->clearRecipients();
        $mail->addTo($email );

        try{
            $mail->send();
            return true;
        }catch (Exception $e){
            App_Validate_MessageBroker::addErrorMessage("O e-mail não pode ser enviado. {$e->getMessage()} ");
            return false;
        }

    }

    public function gerarCodigo(Processo_Model_Vo_Processo $processo, $idGrupo = null)
    {

        if(empty($idGrupo)) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            $idGrupo = $identity->time['id'];
        }

        $maxCodigo = $this->_dao->maxCodigo($idGrupo);
        $maxCodigo = $maxCodigo+1;
        $processo->pro_codigo = ''."-".$maxCodigo."-".date('Y');

    }

}