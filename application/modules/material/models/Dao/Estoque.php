<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  04/05/2013
 */
class Material_Model_Dao_Estoque extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_estoque";
    protected $_primary  = "id_estoque";

    protected $_rowClass = 'Material_Model_Vo_Estoque';

    protected $_dependentTables = array('Material_Model_Dao_ItemEntrega', 'Material_Model_Dao_EstoqueMovimento');

    protected $_referenceMap    = array(
            'Item' => array(
                    'columns'           => 'id_item',
                    'refTableClass'     => 'Material_Model_Dao_Item',
                    'refColumns'        => 'id_item'
            ),
            'Unidade' => array(
                    'columns'           => 'id_tipo_unidade',
                    'refTableClass'     => 'Sis_Model_Dao_TipoUnidade',
                    'refColumns'        => 'id_tipo_unidade'
            ),
            'Estoque Opcao' => array(
                    'columns'           => 'id_estoque',
                    'refTableClass'     => 'Material_Model_Dao_EstoqueOpcao',
                    'refColumns'        => 'id_estoque'
            ),
            'Workspace'    => array(
                    'columns'           => 'id_workspace',
                    'refTableClass'     => 'Auth_Model_Dao_Workspace',
                    'refColumns'        => 'id_workspace'
            )
    );

    public function getMaxLote()
    {
        $select = $this->select();
        $select->from(array('te' => $this->_name), new Zend_Db_Expr("max( cast(cod_lote as unsigned) )"));

        return $this->_db->fetchOne($select);
    }

    public function saveProcedureEstoque($estoque, $loop, $idMovimento)
    {
        $idUsuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
        $query = $this->_db->query("call insert_estoque(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                array($estoque->id_item, $estoque->id_tipo_unidade,
                    $estoque->id_marca, $estoque->codigo, $estoque->ncm_sh, $estoque->cst,
                    $estoque->cfop, $estoque->quantidade, $estoque->vl_unitario, $estoque->vl_total,
                    $estoque->bc_icms, $estoque->vl_icms, $estoque->vl_ipi, $estoque->aliq_icms,
                    $estoque->aliq_ipi,$idUsuario, $loop, $idMovimento));
    }

    public function sumItemEstoque($idItem, $opcaoArray = null, $idWorkspace)
    {
        $select = $this->_db->select();
        $select->from($this->_name, 'sum(quantidade)')
               ->where('id_item = ?', $idItem)
               ->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
        if($opcaoArray){
            $selectTa = $this->_db->select();
            $selectTa->from('ta_gm_estoque_x_opcao', 'id_estoque')
                     ->where('id_opcao in(?)', $opcaoArray)
                     ->group('id_estoque')
                     ->having('count(id_estoque) = ?', count($opcaoArray));
            $select->where("id_estoque in ({$selectTa})");
        }

        //responsavel por verificar o estoque pelo workspace
        if($idWorkspace)
            $select->where('id_workspace = ?', $idWorkspace);

        return $this->_db->fetchOne($select);
    }


    public function getLote($qtdSolicitada, $idItem, $opcaoArray = null, $idWorkspace)
    {
        //crio uma variavel de usuario para o select somar o total e trazer o lote necessário
        $reiniciarTotal = $this->_db->query("set @totalEstoque := 0;");
        $select = $this->_db->select();
        $select->from($this->_name, array('total' => new Zend_Db_Expr('@totalEstoque := quantidade+@totalEstoque'), 'id_estoque', 'id_tipo_unidade', 'cod_lote', 'quantidade'))
               ->where("@totalEstoque <= ?", floatval($qtdSolicitada))
               ->where('id_item = ?', $idItem)
               ->where('ativo = ?', App_Model_Dao_Abstract::ATIVO)
               ->where('cod_lote is not null');

        if($opcaoArray){
            $selectTa = $this->_db->select();
            $selectTa->from('ta_gm_estoque_x_opcao', 'id_estoque')
                     ->where('id_opcao in(?)', $opcaoArray)
                     ->group('id_estoque')
                     ->having('count(id_estoque) = ?', count($opcaoArray));
            $select->where("id_estoque in ({$selectTa})");
        }

        //responsavel por verificar o estoque pelo workspace
        $select->where('id_workspace = ?', $idWorkspace);
        $listEstoque = $this->_db->fetchAll($select);
        $reiniciarTotal->execute();

        return $listEstoque;
    }

    /**
     * @param string $chave o campo que será usado como chave.
     * Caso String vazia ou null, pega a chave primaria definida no atributo $_primary
     * @param string $valor o campo que deve ser retornado no valor
     * Caso String vazia ou null, pega a chave primaria definida no atributo $_namePairs
     * @param string|array $where
     * @param string $ordem
     * @param string $limit
     * @return array(value => $chave, label => valor)
     */
    public function getAutocomplete($term, $chave = null, $valor = null, $where = null, $ordem = null, $limit = null, $opcaoArray = null){
        if(empty($chave)){
            if(is_array($this->_primary)){
                $chave = $this->_primary[1];
            }else{
                $chave = $this->_primary;
            }
        }

        if(empty($valor)){
            $valor = $this->_namePairs;
        }

        $select = $this->_db
        ->select()
        ->from($this->_name, array('value' => $valor,'id'=>$chave, 'label' => $valor, 'quantidade'))
        ->order($ordem ? $ordem : $valor);

        if( is_numeric( $limit) ){
            $select->limit( $limit );
        }else {
            $select->limit(1000);
        }

        if($where){
            if (is_array($where)){
                foreach ($where as $key => $value){
                    $select->where($key, $value);
                }
            }else{
                $select->where($where);
            }
        }

        if($opcaoArray){
            $selectTa = $this->_db->select();
            $selectTa->from('ta_gm_estoque_x_opcao', 'id_estoque')
                     ->where('id_opcao in(?)', $opcaoArray)
                     ->group('id_estoque')
                     ->having('count(id_estoque) = ?', count($opcaoArray));
            $select->where("id_estoque in ({$selectTa})");
        }

        $select->where($valor.' like "%'.$term.'%"');

        return $this->_db->fetchAll($select);

    }


    public function selectPaginatorByItem(array $options = null, $id_item)
    {
        $select = $this->_db->select();
        $select->from(array('te' => $this->_name))
        ->joinInner(array('ti' => 'tb_gm_item'), 'te.id_item = ti.id_item', array('descricao' => 'ti.nome'))
        ->where('te.id_item = ?', $id_item);
        $workspaceSession = new Zend_Session_Namespace('workspace');
        if($workspaceSession->free_access != true){
            $select->where('te.id_workspace = ?', $workspaceSession->id_workspace);
        }
        $this->_searchPaginator($select, $options);
        return $select;
    }


    public function getTotalByItem($id_item, $id_movimento = null)
    {
        $value = "sum(te.quantidade)";
        if($id_movimento){
            $value = "sum(tem.quantidade)";
        }
        $select = $this->_db->select();
        $select->from(array('te' => $this->_name), array('total' => new Zend_Db_Expr($value)))
               ->joinInner(array('tem' => 'tb_gm_estoque_gm_movimento'), 'te.id_estoque = tem.id_estoque', null)
               ->joinInner(array('tm' => 'tb_gm_movimento'), 'tem.id_movimento = tm.id_movimento', null)
               ->where('te.id_item = ?', $id_item)
               ->where('te.ativo = ?', App_Model_Dao_Abstract::ATIVO);

        if($id_movimento){
            $select->where('tm.id_tp_movimento = ?', $id_movimento);
        }

        return $this->_db->fetchOne($select);
    }

    /**
     * Provisório pois ainda existe item em estoque que ainda não foi sicronizado
     * Entao desta forma irá trazer a soma total do item sicronizado ou não
     * @todo corrigir pois não é pra existir no futuro
     * @since 10/07/2013
     *
     */
    public function sumItemEstoqueProv($idItem, $idMarca = null)
    {
        $select = $this->_db->select();
        $select->from($this->_name, 'sum(quantidade)')
        ->where('id_item = ?', $idItem)
        ->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);

        if($idMarca){
            $select->where('id_marca = ?', $idMarca);
        }

        return $this->_db->fetchOne($select);
    }

}