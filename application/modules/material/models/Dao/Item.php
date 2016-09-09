<?php
class Material_Model_Dao_Item extends App_Model_Dao_Abstract
{
    protected $_name = "mat_tb_gm_item";
    protected $_primary = "id_item";
    protected $_namePairs = "nome";

    protected $_rowClass = 'Material_Model_Vo_Item';

    protected $_dependentTables = array('Material_Model_Dao_Estoque', 'Compra_Model_Dao_CompraItem', 'Compra_Model_Dao_CampanhaItem');

    protected $_referenceMap    = array(
            'Grupo' => array(
                    'columns'           => 'id_grupo',
                    'refTableClass'     => 'Material_Model_Dao_Grupo',
                    'refColumns'        => 'id_grupo'
            ),
            'Sub Grupo' => array(
                    'columns'           => 'id_subgrupo',
                    'refTableClass'     => 'Material_Model_Dao_Subgrupo',
                    'refColumns'        => 'id_subgrupo'
            ),
            'Classe' => array(
                    'columns'           => 'id_classe',
                    'refTableClass'     => 'Material_Model_Dao_Classe',
                    'refColumns'        => 'id_classe'
            ),
            'Unidade Compra' => array(
                    'columns'           => 'id_tipo_unidade_compra',
                    'refTableClass'     => 'Sis_Model_Dao_TipoUnidade',
                    'refColumns'        => 'id_tipo_unidade'
            ),
            'Unidade Consumo' => array(
                    'columns'           => 'id_tipo_unidade_consumo',
                    'refTableClass'     => 'Sis_Model_Dao_TipoUnidade',
                    'refColumns'        => 'id_tipo_unidade'
            )
    );

    public function getListItem($id_grupo = null, $id_subgrupo = null, $id_classe = null)
    {
        $select = $this->_db->select();
        $select->from(array('ti' => $this->_name))
               ->joinInner(array('ttu' => 'tb_tipo_unidade'), 'ttu.id_tipo_unidade = ti.id_tipo_unidade_compra', array('nome_unidade_compra'=>'nome'))
               ->joinInner(array('ttuc' => 'tb_tipo_unidade'), 'ttuc.id_tipo_unidade = ti.id_tipo_unidade_consumo', array('nome_unidade_consumo'=>'nome'))
               ->where('ti.ativo = ?', App_Model_Dao_Abstract::ATIVO);

        if(!empty($id_grupo)){
            $select->where('id_grupo = ?', $id_grupo);
        }

        if(!empty($id_subgrupo)){
            $select->where('id_subgrupo = ?', $id_subgrupo);
        }

        if(!empty($id_classe)){
            $select->where('id_classe = ?', $id_classe);
        }
        return $this->_db->fetchAll($select);
    }

    /**
     * @desc Irá retornar somente a coluna pedida
     * @param String $colName
     * @return array
     */
    public function getCol($colName)
    {
        $select = $this->_db->select();
        $select->from($this->_name, $colName)
        ->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);

        return $this->_db->fetchCol($select);
    }


    public function getAutocomplete($term, $chave = null, $valor = null, $where = null, $ordem = null, $limit = null){
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
        ->from($this->_name, array('value' => $valor,'id'=>$chave, 'label' => $valor, 'id_tipo_unidade_compra', 'id_tipo_unidade_consumo', 'ncm_sh' => 'ncm_sh'))
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
        $select->where($valor.' like "%'.$term.'%"');

        return $this->_db->fetchAll($select);

    }

    public function getAutocompleteToSumEstoque($term, $chave = null, $valor = null, $where = null, $ordem = null, $limit = null){
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

        $selectEstoque = $this->_db->select()
                              ->from(array('te' => 'tb_gm_estoque'), new Zend_Db_Expr('sum(te.quantidade)'))
                              ->where('te.ativo = ?', App_Model_Dao_Abstract::ATIVO)
                              ->where('te.id_item = ti.id_item');

        $select = $this->_db
        ->select()
        ->from(array('ti' => $this->_name), array(
                                        'value' => new Zend_Db_Expr("CONCAT_WS(' - ',ti.$valor,NULL,($selectEstoque))"),
                                        'id'    =>$chave, 'label' => new Zend_Db_Expr("CONCAT_WS(' - ',ti.$valor,NULL,($selectEstoque))"),
                                        'id_tipo_unidade_compra', 'id_tipo_unidade_consumo', 'ncm_sh' => 'ncm_sh'))
        ->order($ordem ? $ordem : $valor);

        if( is_numeric( $limit) ){
            $select->limit( $limit );
        }else {
            $select->limit(1000);
        }

        if($where){
            if (is_array($where)){
                foreach ($where as $key => $value){
                    $select->where("ti.".$key, $value);
                }
            }else{
                $select->where($where);
            }
        }
        $select->where("ti.".$valor.' like "%'.$term.'%"');

        return $this->_db->fetchAll($select);

    }

    public function findProdutoByRequest($request)
    {
        $select = $this->_db->select();
        $select->from(array('ti' => $this->_name));

        foreach ($request as $key => $value){
            if(is_numeric($value)){
                $select->orWhere("$key = ?", $value);
            }elseif(!empty($value)){
                $select->orWhere("$key like ?", "%$value%");
            }
        }
        return $this->_db->fetchAll($select);
    }
}