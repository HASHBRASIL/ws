<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  24/05/2013
 */
class Material_Model_Dao_Movimento extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_movimento";
    protected $_primary  = "id_movimento";

    protected $_rowClass = 'Material_Model_Vo_Movimento';

    protected $_dependentTables = array('Material_Model_Dao_EstoqueMovimento');

    protected $_referenceMap    = array(
            'Protocolo' => array(
                    'columns'           => 'id_protocolo',
                    'refTableClass'     => 'Material_Model_Dao_Protocolo',
                    'refColumns'        => 'id_protocolo'
            ),
            'MaterialProcesso' => array(
                    'columns'           => 'id_material_processo',
                    'refTableClass'     => 'Processo_Model_Dao_MaterialProcesso',
                    'refColumns'        => 'id_material_processo'
            )
    );

    /**
     * @desc irá pegar todos os item que sairam pelo protocolo de saida
     * filtrando pelo idProcesso
     * @param int $idProcesso
     * @return array
     */
    public function getSaidaByProcessoItem($idProcesso, $idItem)
    {
        $select = $this->_db->select();
        $select->from(array('tm' => $this->_name), null)
               ->joinLeft(array('tp'=>'tb_gm_protocolo'), 'tm.id_protocolo = tp.id_protocolo', array('id_protocolo', 'dt_saida'=>'dt_criacao'))
               ->joinInner(array('tem' => 'tb_gm_estoque_gm_movimento'), ' tm.id_movimento = tem.id_movimento', array('quantidade_solicitada' => 'quantidade'))
               ->joinInner(array('te' => 'tb_gm_estoque'), 'tem.id_estoque = te.id_estoque', 'cod_lote')
               ->joinInner(array('ti' => 'tb_gm_item'), 'te.id_item = ti.id_item', array('item' => 'nome'))
               ->joinInner(array('tu' => 'tb_tipo_unidade'), 'te.id_tipo_unidade = tu.id_tipo_unidade', array('nome_unidade' => 'nome'))
               ->where('tm.id_tp_movimento = ?', Material_Model_Bo_TipoMovimento::SAIDA)
               ->where('tp.id_processo = ? or tm.id_processo = ?', $idProcesso)
               ->where('te.id_item = ?', $idItem)
               ->order('te.id_item');

        return $this->_db->fetchAll($select);
    }

    /**
     * @desc irá pegar todos os id_item e nome do item que sairam pelo protocolo de saida
     * filtrando pelo idProcesso
     * @param int $idProcesso
     * @return array
     */
    public function getIdItemByProcesso($idProcesso)
    {
        $select = $this->_db->select();
        $select->from(array('tm' => $this->_name), null)
               ->joinLeft(array('tp'=>'tb_gm_protocolo'), 'tm.id_protocolo = tp.id_protocolo', null)
               ->joinInner(array('tem' => 'tb_gm_estoque_gm_movimento'), ' tm.id_movimento = tem.id_movimento', null)
               ->joinInner(array('te' => 'tb_gm_estoque'), 'tem.id_estoque = te.id_estoque', 'id_item')
               ->joinInner(array('ti' => 'tb_gm_item'), 'te.id_item = ti.id_item', array('item' => 'nome'))
               ->where('tm.id_tp_movimento = ?', Material_Model_Bo_TipoMovimento::SAIDA)
               ->where('tp.id_processo = ? or tm.id_processo = ?', $idProcesso)
               ->order('te.id_item')
               ->group('ti.id_item');

        return $this->_db->fetchAll($select);
    }

    /**
     * @desc responsável por pegar todos os movimentos do estoque a partir do filtro de qlq campo do movimento
     * @param array (nome_campo => valor_filtrado)
     * @return array
     */
    public function getAllByAny($filtro = null)
    {
        $select = $this->_db->select();
        $select->from(array('tm' => $this->_name), array('quantidade_movimento' => 'quantidade'))
               ->joinInner(array('tem' => 'tb_gm_estoque_gm_movimento'), 'tm.id_movimento = tem.id_movimento', array('quantidade_mov_estoque' => 'quantidade'))
               ->joinInner(array('te' => 'tb_gm_estoque'), 'tem.id_estoque = te.id_estoque', array('vl_unitario' => 'vl_unitario', 'cod_lote', 'quantidade_estoque' => 'quantidade'))
               ->joinInner(array('ti' => 'tb_gm_item'), 'te.id_item = ti.id_item', array('nome_item' => 'nome'))
               ->joinInner(array('tu' => 'tb_tipo_unidade'), 'ti.id_tipo_unidade_consumo = tu.id_tipo_unidade', array('nome_unidade' => 'nome'));

        if($filtro){
        	foreach ($filtro as $nome_campo => $valor_filtrado){
        		$select->where('tm.'.$nome_campo.' = ?', $valor_filtrado);
        	}
        }
        return $this->_db->fetchAll($select);
    }


}