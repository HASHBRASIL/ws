<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  24/05/2013
 */
class Material_Model_Bo_Movimento extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Movimento
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Movimento();
        parent::__construct();
    }


    /**
     * @desc So irá salvar no banco se tudo ocorre bem se não correr ele não salvará os dados nas tabela de estoque e na tabela de movimento
     * (non-PHPdoc)
     * @see App_Model_Bo_Abstract::saveFromRequest()
     */
    public function saveFromRequest( $request, $object){
        //desabilitando o limite de memória de processamento do servidor
        ini_set( "memory_limit", -1 );
        //desabilitando o limite do tempo de execução
        ini_set( "max_execution_time", 0 );
        $object = $object->setFromArray($request);

        $this->_insertCriacao($object);

        $resultValidation = $this->_validar($object);
        if(!$resultValidation){
            throw new App_Validate_Exception();
        }
        //$this->_dao->getAdapter()->beginTransaction();
        try {
            $this->_preSave($object, $request);
            $object->save();
            $this->_postSave($object, $request);
            // irá proseguir com a transação do banco
            //$this->_dao->getAdapter()->commit();
        } catch (Exception $e) {
            // Reversão se transação falhar
            //$this->_dao->getAdapter()->rollBack();
            App_Validate_MessageBroker::addErrorMessage("O sistema se encontra fora do ar no momento. Caso persista entre em contato com o Administrador. ".$e->getMessage());
            throw new App_Validate_Exception();
        }
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $id_tp_movimento = $request['id_tp_movimento'];
        if($id_tp_movimento == Material_Model_Bo_TipoMovimento::ENTRADA){
        	$object->quantidade = $this->_formatDecimal($request['quantidade']);
            $object->id_processo = empty($request['id_processo'])? null : $request['id_processo'];
        }else{
            $object->quantidade = $this->_formatDecimal($request['total_qtd_solicitado']);
            if($object->id_protocolo){
                $protocoloBo = new Material_Model_Bo_Protocolo();
                $protocolo = $protocoloBo->get($object->id_protocolo);
                if($protocolo->id_processo){
                    $materialProcessoBo     = new Processo_Model_Bo_MaterialProcesso();
                    $materialProcesso       = $materialProcessoBo->get();

                    $materialProcesso->id_processo         = $protocolo->id_processo;
                    $materialProcesso->id_status_material  = Processo_Model_Bo_StatusMaterial::BAIXA_TOTAL;
                    $materialProcesso->id_tp_material      = Processo_Model_Bo_TipoMaterial::PROPRIO;
                    $materialProcesso->qtd_baixado         = $object->quantidade;

                    $materialProcessoBo->saveFromRequest($request, $materialProcesso);
                    $object->id_material_processo = $materialProcesso->id_material_processo;
                }
            }
        }
    }

    public function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $itemBo        = new Material_Model_Bo_Item();
        $estoqueBo     = new Material_Model_Bo_Estoque();
        $estoqueMovBo  = new Material_Model_Bo_EstoqueMovimento();

        $id_tp_movimento = $object->id_tp_movimento;

        if($id_tp_movimento == Material_Model_Bo_TipoMovimento::ENTRADA){
            $item = $itemBo->get($request['id_item']);
            if(empty($item->id_unidade_rastreabilidade)){
                $item->id_unidade_rastreabilidade = $request['rastreabilidade'];
                $item->save();
            }
/**
 * apagar pois vai salvar via javascript
            $loop = 0;
            $quantidadeEstoque;
            if($item->id_unidade_rastreabilidade == $item->id_tipo_unidade_compra){
                $loop = intval(round($object->quantidade, 2));
                $quantidadeEstoque = $request['qtd_unidade'];
            }elseif ($item->id_unidade_rastreabilidade == $item->id_tipo_unidade_consumo){
                $request['qtd_unidade'] = $this->_formatDecimal($request['qtd_unidade']);
                $loop = intval(round($object->quantidade,2)) * intval(round($request['qtd_unidade'], 2));
                $quantidadeEstoque = 1;
            }

            //$estoqueBo->saveProcedureEstoque($request, $loop, $object->id_movimento, $quantidadeEstoque, $item->id_tipo_unidade_consumo);apagar pois vai salvar via javascript
*/
        }elseif  ($id_tp_movimento != Material_Model_Bo_TipoMovimento::ENTRADA){
            foreach ($request['id_estoque'] as $id){
                if(isset($request['qtd_prot_solicitada'][$id]) && bccomp($this->_formatDecimal($request['qtd_prot_solicitada'][$id]), 0,2) != 0){
                    $estoque = $estoqueBo->get($id);
                    $estoque->quantidade = $estoque->quantidade - $this->_formatDecimal($request['qtd_prot_solicitada'][$id]);
                    if(bccomp($estoque->quantidade, 0,2) == 0){
                        $estoque->ativo    = App_Model_Dao_Abstract::INATIVO;
                    }
                    $estoque->save();

                    //salvando no estoque
                    $estoqueMov                 = $estoqueMovBo->get();
                    $estoqueMov->id_estoque     = $estoque->id_estoque;
                    $estoqueMov->id_movimento   = $object->id_movimento;
                    $estoqueMov->quantidade     = $this->_formatDecimal($request['qtd_prot_solicitada'][$id]);
                    $estoqueMov->save();
                }
            }
        }

        if($object->id_tp_movimento == Material_Model_Bo_TipoMovimento::SAIDA && !empty($object->id_processo)){
            $materialProcesso = new Processo_Model_Bo_MaterialProcesso();
            $materialProcesso->saveByMovimento($object,$request);
        }

    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if($object->id_tp_movimento != Material_Model_Bo_TipoMovimento::ENTRADA){
            $objRequest   = new Zend_Controller_Request_Http();
            $estoqueBo    = new Material_Model_Bo_Estoque();
            $request = $objRequest->getParams();

            foreach ($request['id_estoque'] as $id){
                $estoque = $estoqueBo->get($id);
                if(isset($request['num_protocolo'][$id]) && $request['num_protocolo'][$id] != $estoque->cod_lote){
                    App_Validate_MessageBroker::addErrorMessage("O numero do lote está incorreto!");
                    return false;
                }
            }

        }

        return true;
    }

    private function _insertCriacao($object)
    {
        //verifica se possuir o campo e se possuir verifica se é criação ou atualização
        if(isset($object->id_criacao_usuario)){
            if(empty($object->id_criacao_usuario)){
                $object->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
            }else if(isset($object->id_atualizacao_usuario)){
                $object->id_atualizacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
            }
        }

        //verifica se possuir o campo e se possuir verifica se é criação ou atualização
        if(isset($object->dt_criacao)){
            if(empty($object->dt_criacao)){
                $object->dt_criacao = date('Y-m-d H:i:s');
            }else if(isset($object->dt_atualizacao)){
                $object->dt_atualizacao = date('Y-m-d H:i:s');
            }
        }
    }

    public function getSaidaByProcesso($idProcesso)
    {
        $idItemList = $this->_dao->getIdItemByProcesso($idProcesso);
        $estoque = null;
        if($idItemList){
            foreach ($idItemList as $item){
                $estoque[$item['id_item']]['nome'] = $item['item'];
                $estoque[$item['id_item']]['list'] = $this->_dao->getSaidaByProcessoItem($idProcesso, $item['id_item']);
            }
        }
        return $estoque;
    }

    public function getAllByAny($filtro)
    {
        return $this->_dao->getAllByAny($filtro);
    }

    /**
     * Metodo responsavel por guarda os movimentos de transferencia
     * Ele salva dois tipos de movimento diferente um baixa e outro entrada de material
     * @param Material_Model_Vo_Estoque $estoqueNew
     * @param int $idEstoqueOld
     */
    public function saveTransferEstoque(Material_Model_Vo_Estoque $estoqueNew, $idEstoqueOld)
    {
        $movimentoEstoqueBo = new Material_Model_Bo_EstoqueMovimento();

        $movimentoEntrada    = $this->get();
        $movimentoSaida      = $this->get();

        $movimentoEntrada->id_tp_movimento     = Material_Model_Bo_TipoMovimento::ENTRADA;
        //como é transferencia de um lote, sempre vai ser 1 pois so poderá compra 1 unidade de compra
        $movimentoEntrada->quantidade          = 1;
        $movimentoEntrada->transferencia        = 1;
        $movimentoEntrada->id_criacao_usuario  = Zend_Auth::getInstance()->getIdentity()->usu_id;
        $movimentoEntrada->dt_criacao          = date('Y-m-d H:i:s');

        $movimentoSaida->id_tp_movimento       = Material_Model_Bo_TipoMovimento::SAIDA;
        $movimentoSaida->quantidade            = $estoqueNew->quantidade;
        $movimentoSaida->transferencia          = 1;
        $movimentoSaida->id_criacao_usuario    = Zend_Auth::getInstance()->getIdentity()->usu_id;
        $movimentoSaida->dt_criacao            = date('Y-m-d H:i:s');

        $movimentoEntrada->save();
        $movimentoSaida->save();

        $movimentoEstoqueEntrada     = $movimentoEstoqueBo->get();
        $movimentoEstoqueSaida       = $movimentoEstoqueBo->get();

        $movimentoEstoqueEntrada->id_movimento     = $movimentoEntrada->id_movimento;
        $movimentoEstoqueEntrada->id_estoque       = $estoqueNew->id_estoque;
        $movimentoEstoqueEntrada->quantidade       = $estoqueNew->quantidade;

        $movimentoEstoqueSaida->id_movimento       = $movimentoSaida->id_movimento;
        $movimentoEstoqueSaida->id_estoque         = $idEstoqueOld;
        $movimentoEstoqueSaida->quantidade         = $estoqueNew->quantidade;

        $movimentoEstoqueEntrada->save();
        $movimentoEstoqueSaida->save();
    }
}