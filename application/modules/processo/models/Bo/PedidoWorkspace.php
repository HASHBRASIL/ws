<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  19/03/2014
 */
class Processo_Model_Bo_PedidoWorkspace extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_Processo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_Processo();
        parent::__construct();
    }

    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $processoBo     = new Processo_Model_Bo_Processo();
        $workspaceBo    = new Auth_Model_Bo_Workspace();

        $processo       = $processoBo->get($object->id_processo_pai);
        $workspaceObj   = $workspaceBo->get($request['id_workspace']);


        $object->setFromArray($processo->toArray());
        $object->setFromArray($request);

        $object->empresas_id = null;
        if(!empty($object->getProcessoPai()->getWorkspace()->id_empresa) && isset($object->getProcessoPai()->getWorkspace()->id_empresa)){
            $object->empresas_id = $object->getProcessoPai()->getWorkspace()->id_empresa;
        }

        $object->pro_id              = null;
        $object->pro_vlr_unt         = $this->_formatDecimal($object->pro_vlr_unt);
        $object->pro_vlr_pedido      = $this->_formatDecimal($object->pro_vlr_pedido);
        $object->pro_data_inc          = date('Y-m-d H:i:s');

        $processoBo->gerarCodigo($object, $workspaceObj);
    }

    public function processoPai($idProcesso, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $listProcessoArray = Array();
        $listProcesso = $this->_dao->processoPai($idProcesso);
        if(count($listProcesso) >0){
            foreach ($listProcesso as $key =>$processo){
                $listProcessoArray[$key] = $processo;
                if(count( $this->_dao->processoPai($processo['pro_id'])) > 0){
                    $listProcessoArray[$key]['children'] = $this->processoPai($processo['pro_id']);
                }
            }
        }
        return $listProcessoArray;
    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $objRequest  = new Zend_Controller_Request_Http();
        $request     = $objRequest->getParams();

        if(empty($request['id_workspace'])){
            App_Validate_MessageBroker::addErrorMessage("Selecione um workspace");
            return false;
        }
        if(empty($request['pro_quantidade'])){
            App_Validate_MessageBroker::addErrorMessage("O campo de quantidade a produzir está vazio.");
            return false;
        }
        if(empty($request['pro_desc_produto'])){
            App_Validate_MessageBroker::addErrorMessage("O campo de descrição de produção está vazio.");
            return false;
        }
        return true;
    }

    public function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        /**
         * Salva o historico do processo
         */
        $historicoBo = new Processo_Model_Bo_Historico();
        $historico = $historicoBo->get();
        $historico->setFromArray($object->toArray());
        $historico->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
        $historico->dt_criacao = date('Y-m-d H:i:s');
        $historico->save();

        /**
         * Financeiro
         */
        if($object->pro_vlr_pedido != '0.00' ){
            $financialBo           = new Financial_Model_Bo_Financial();
            $agrupadorFinanceiroBo = new Financial_Model_Bo_AgrupadorFinanceiro();

            $agrupadorFinanceiroCredito     = $agrupadorFinanceiroBo->get();
            $financialCredito               = $financialBo->get();

            $agrupadorFinanceiroDebito      = $agrupadorFinanceiroBo->get();
            $financialDebito                = $financialBo->get();

            $agrupadorFinanceiroCredito->tmv_id          = Financial_Model_Bo_TipoMovimento::CREDITO;
            $agrupadorFinanceiroCredito->id_workspace    = $object->id_workspace;
            $agrupadorFinanceiroCredito->pro_id          = $object->pro_id;

            $agrupadorFinanceiroDebito->tmv_id           = Financial_Model_Bo_TipoMovimento::DEBITO;
            $agrupadorFinanceiroDebito->id_workspace     = $object->getProcessoPai()->id_workspace;
            $agrupadorFinanceiroDebito->pro_id           = $object->id_processo_pai;

            /**
             * terminar essa parada pois ainda tenho que fazer a alteração automatica dos financeiros
             */
            $this->savedFinancial($object, $agrupadorFinanceiroCredito, $financialCredito, $object->getProcessoPai()->getWorkspace()->id_empresa);
            $this->savedFinancial($object, $agrupadorFinanceiroDebito, $financialDebito, $object->getWorkspace()->id_empresa);

            $financialCredito->id_financeiro_correlato = $financialDebito->fin_id;
            $financialDebito->id_financeiro_correlato  = $financialCredito->fin_id;

            $agrupadorFinanceiroCredito->id_agrupador_financeiro_correlato = $agrupadorFinanceiroDebito->id_agrupador_financeiro;
            $agrupadorFinanceiroDebito->id_agrupador_financeiro_correlato  = $agrupadorFinanceiroCredito->id_agrupador_financeiro;

            $financialCredito->save();
            $financialDebito->save();

            $agrupadorFinanceiroCredito->save();
            $agrupadorFinanceiroDebito->save();
        }
    }

    private function savedFinancial($processo,$agrupadorFinanceiro, $financial, $idEmpresa = null )
    {
        $financialBo           = new Financial_Model_Bo_Financial();
        $agrupadorFinanceiroBo = new Financial_Model_Bo_AgrupadorFinanceiro();

        $agrupadorFinanceiro->fin_descricao     = $processo->pro_desc_produto;
        $agrupadorFinanceiro->fin_valor         = $processo->pro_vlr_pedido;
        $agrupadorFinanceiro->moe_id            = Financial_Model_Bo_Moeda::REAL;

        if($idEmpresa){
            $agrupadorFinanceiro->id_empresa        = $idEmpresa;
        }

        $request = array();
        $agrupadorFinanceiroBo->saveFromRequest($request, $agrupadorFinanceiro);

        $financial->id_agrupador_financeiro = $agrupadorFinanceiro->id_agrupador_financeiro;
        $financial->fin_valor               = $processo->pro_vlr_pedido;
        $financial->fin_competencia         = date('Y/m/').'01';
        $financial->fin_descricao           = $processo->pro_desc_produto;
        $date = new Zend_Date();
        $financial->fin_emissao             = $date->toString('yyyy/MM/dd');
        $date->setDay(1);
        $financial->fin_competencia         = $date->toString('yyyy/MM/dd');
        $date->addMonth(1)->subDate(1);
        $financial->fin_vencimento          = $date->toString('yyyy/MM/dd');

        if($idEmpresa){
            $request['empresa_sacado_selected']        = $idEmpresa;
        }

        $financialBo->saveFromRequest($request, $financial);

    }
}