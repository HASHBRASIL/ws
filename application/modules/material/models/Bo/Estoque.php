<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  04/05/2013
 */
class Material_Model_Bo_Estoque extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Estoque
     */
    protected $_dao;

    const MAX_CODE = 20000;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Estoque();
        parent::__construct();
    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $criteria = array('cod_lote = ?' => str_replace('.', '', $object->cod_lote));
        if(!empty($object->id_estoque)){
            $criteria['id_estoque <> ?'] = $object->id_estoque;
        }
        $estoque = $this->find($criteria);
        if(count($estoque) > 0){
            App_Validate_MessageBroker::addErrorMessage('Já possui produto com esse código');
            return false;
        }
        return true;
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $object->quantidade     = $this->_formatDecimal($request['quantidade_unidade_lote']);
        $object->vl_unitario    = $this->_formatDecimal($object->vl_unitario);
        $object->vl_total       = $this->_formatDecimal($object->vl_total);
        $object->bc_icms        = $this->_formatDecimal($object->bc_icms);
        $object->vl_icms        = $this->_formatDecimal($object->vl_icms);
        $object->vl_ipi         = $this->_formatDecimal($object->vl_ipi);
        $object->aliq_icms      = $this->_formatDecimal($object->aliq_icms);
        $object->aliq_ipi       = $this->_formatDecimal($object->aliq_ipi);
        $object->cod_lote       = str_replace('.', '', $object->cod_lote);

        if(empty($object->cod_lote)){
            $cod_lote                     = $this->_dao->getMaxLote();
            if($cod_lote < self::MAX_CODE){
                $cod_lote = self::MAX_CODE;
            }

            $object->cod_lote             = $cod_lote + 1;
        }

    }

    public function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        //Salvando o estoque e o movimento na EstoqueMovimento
        $estoqueMovimentoBo = new Material_Model_Bo_EstoqueMovimento();
        $estoqueMovimento = $estoqueMovimentoBo->get();
        $estoqueMovimentoBo->delete($object->id_estoque, $request['id_movimento']);
        $estoqueMovimento->id_estoque       = $object->id_estoque;
        $estoqueMovimento->id_movimento     = $request['id_movimento'];
        $estoqueMovimento->quantidade       = $object->quantidade;
        $estoqueMovimento->save();

        //Salvando o item com suas opções
        if( isset($request['id_opcao']) && count($request['id_opcao']) > 0 ){
            $estoqueOpcaoBo = new Material_Model_Bo_EstoqueOpcao();
            $estoqueOpcaoBo->deleteByEstoque($object->id_estoque);

            foreach ($request['id_opcao'] as $idOpcao){
                $estoqueOpcao = $estoqueOpcaoBo->get();

                $estoqueOpcao->id_estoque     = $object->id_estoque;
                $estoqueOpcao->id_opcao       = $idOpcao;
                $estoqueOpcao->save();
            }
        }
    }

    public function delete($idEstoque)
    {
        if(!empty($idEstoque)){
            $criteria = array('id_estoque = ?' => $idEstoque);
            $this->_dao->delete($criteria);
        }
    }

    public function getEstoqueRow($id_estoque)
    {
        $criteria     = "id_estoque = {$id_estoque}";
        $estoque   = $this->_dao->fetchRow($criteria);
        if(is_array($estoque)){
            return $estoque;
        }else{
            return $estoque->toArray();
        }

    }

    public function saveProcedureEstoque($request, $loop, $idMovimento, $quantidadeEstoque, $unidadeConsumo )
    {
        if(!empty($request['id_estoque'])){
            $estoqueAtual = $this->getEstoqueRow($request['id_estoque']);
        }

        $estoque = $this->get();
        if(isset($estoqueAtual)){
            $estoque->setFromArray($estoqueAtual);
        }else{
            $estoque->setFromArray($request);
        }
        $estoque->quantidade          = $quantidadeEstoque;
        $estoque->id_tipo_unidade     = $unidadeConsumo;

        $estoque->quantidade     = $this->_formatDecimal($estoque->quantidade);
        $estoque->vl_unitario    = $this->_formatDecimal($estoque->vl_unitario);
        $estoque->vl_total       = $this->_formatDecimal($estoque->vl_total);
        $estoque->bc_icms        = $this->_formatDecimal($estoque->bc_icms);
        $estoque->vl_icms        = $this->_formatDecimal($estoque->vl_icms);
        $estoque->vl_ipi         = $this->_formatDecimal($estoque->vl_ipi);
        $estoque->aliq_icms      = $this->_formatDecimal($estoque->aliq_icms);
        $estoque->aliq_ipi       = $this->_formatDecimal($estoque->aliq_ipi);

        $this->_dao->saveProcedureEstoque($estoque, $loop, $idMovimento);
    }

    public function sumItemEstoque($idItem, $idOpcao = null, $idWorkspace = null)
    {
        return $this->_dao->sumItemEstoque($idItem, $idOpcao, $idWorkspace);
    }

    public function getLote($qtdSolicitada, $idItem, $opcaoArray = null, $idWorkspace)
    {
        $listLote          = $this->_dao->getLote($qtdSolicitada, $idItem, $opcaoArray , $idWorkspace);
        if(count($listLote)){
            $listLote['total'] = $listLote[count($listLote) - 1]['total'];
        }
        return $listLote;
    }

    public function getAutocompleteLote($term, $id_item,  $id_workspace, $opcaoArray = null)
    {
        $where = array(
                'id_item = ?' => $id_item,
                'ativo = ?' => App_Model_Dao_Abstract::ATIVO,
                'id_workspace = ?' => $id_workspace
        );
        return $this->_dao->getAutocomplete($term, null, "cod_lote", $where, null, null, $opcaoArray );
    }
    /**
     * Gera paginacao
     * @param mixed $data
     * @param array $options
     * @return Zend_Paginator
     * @throws Exception if data type invalid
     */
    public function paginatorByItem( array $options, $id_item)
    {
        $data = $this->_dao->selectPaginatorByItem($options, $id_item);
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

    public function getTotalByItem($id_item)
    {
        $workspaceSession = new Zend_Session_Namespace('workspace');
        $idWorkspace = $workspaceSession->id_workspace;
        //verifica se existe saida pra calcular a entrada de materiais
        //corrigindo um erro que os proximos não virão
        $totalSaida = $this->_dao->getTotalByItem($id_item, Material_Model_Bo_TipoMovimento::SAIDA);
        $totalEstoque = $this->_dao->sumItemEstoque($id_item, null, $idWorkspace);
        $totalEntrada = floatval($totalEstoque) + floatval($totalSaida);
        return array(
                'totalSaida'     => $totalSaida,
                'totalEstoque'   => $totalEstoque,
                'totalEntrada'   => $totalEntrada
        );
    }

    public function transferWorkspace(Material_Model_Vo_Estoque $estoque, $idEstoqueTransfer, $idWorkspaceTransfer)
    {
        $estoqueOpcaoBo     = new Material_Model_Bo_EstoqueOpcao();
        $estoqueOld         = $this->get($idEstoqueTransfer);
        $estoqueArray       = $estoqueOld->toArray();

        $estoqueArray['id_estoque'] = null;
        $estoque->setFromArray($estoqueArray);

        $estoque->id_workspace     = $idWorkspaceTransfer;
        $estoqueOld->quantidade    = 0;
        $estoqueOld->ativo         = 0;

        $estoque->save();
        $estoqueOld->save();

        if(count($estoqueOld->getOpcaoList()) > 0){
            foreach ($estoqueOld->getOpcaoList() as $opcao){
                $opcaoObj = $estoqueOpcaoBo->get();

                $opcaoObj->id_opcao = $opcao->id_opcao;
                $opcaoObj->id_estoque = $estoque->id_estoque;

                $opcaoObj->save();
            }
        }

    }

}