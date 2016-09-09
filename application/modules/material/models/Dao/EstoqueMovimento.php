<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  04/05/2013
 */
class Material_Model_Dao_EstoqueMovimento extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_estoque_gm_movimento";
    protected $_primary  = array("id_estoque", "id_movimento");

    protected $_rowClass = 'Material_Model_Vo_EstoqueMovimento';

    protected $_referenceMap    = array(
            'Movimento' => array(
                    'columns'           => 'id_movimento',
                    'refTableClass'     => 'Material_Model_Dao_Movimento',
                    'refColumns'        => 'id_movimento'
            ),
            'Estoque' => array(
                    'columns'           => 'id_estoque',
                    'refTableClass'     => 'Material_Model_Dao_Estoque',
                    'refColumns'        => 'id_estoque'
            )
    );

    public function sumQuantidadeItemEmpresa($idEmpresa, $idItem)
    {
        $selectMov = $this->_db->select();
        $selectMov->from(array('tm' => 'tb_gm_movimento'), 'tm.id_movimento')
                  ->joinInner(array('tp' => 'tb_gm_protocolo'), "tp.id_protocolo = tm.id_protocolo", null)
                  ->where("tp.id_empresa_receptora = ?", $idEmpresa)
                  ->where("tm.id_tp_movimento = ?", Material_Model_Bo_TipoMovimento::ENTRADA);
        $select = $this->_db->select();
        $select->from(array('tem' => $this->_name), "sum(te.quantidade)", null)
               ->joinInner(array('te' => 'tb_gm_estoque'), "tem.id_estoque = te.id_estoque", null)
               ->where("te.ativo = ?", App_Model_Dao_Abstract::ATIVO)
               ->where("te.id_item = ?", $idItem )
               ->where("id_movimento in(?)", $selectMov);

        return $this->_db->fetchOne($select);

    }

    public function getItemByEstoque($listIdItem, $idEmpresa)
    {
        $selectMov = $this->_db->select();
        $selectMov->from(array('tm' => 'tb_gm_movimento'), 'tm.id_movimento')
                  ->joinInner(array('tp' => 'tb_gm_protocolo'), "tp.id_protocolo = tm.id_protocolo", null)
                  ->where("tp.id_empresa_receptora = ?", $idEmpresa)
                  ->where("tm.id_tp_movimento = ?", Material_Model_Bo_TipoMovimento::ENTRADA);

        $select = $this->_db->select();
        $select->from(array('tem' => $this->_name), null)
               ->joinInner(array('te' => 'tb_gm_estoque'), "tem.id_estoque = te.id_estoque", null)
               ->joinInner(array('ti' => 'tb_gm_item'), "te.id_item = ti.id_item")
               ->where("te.ativo = ?", App_Model_Dao_Abstract::ATIVO)
               ->where("id_movimento in(?)", $selectMov)
               ->group("te.id_item");

        if(count($listIdItem)){
            $select->where("te.id_item not in(?)", $listIdItem );
        }
        return $this->_db->fetchAll($select);
    }

    public function getDetailByItem($idItem)
    {
        $select = $this->_db->select();
        $select->from(array('tem' => $this->_name))
        ->joinInner(array('tm' => 'tb_gm_movimento'), 'tem.id_movimento = tm.id_movimento', array('dt_criacao' => 'tm.dt_criacao', 'cod_protocolo' => 'tm.id_protocolo'))
        ->joinInner(array('te' => 'tb_gm_estoque'), 'tem.id_estoque = te.id_estoque', array('vl_unitario' => 'te.vl_unitario'))
        ->joinLeft(array('nfe' => 'tb_gm_nfe'), 'tm.id_nfe = nfe.id_nfe', array('num_danfe'=> 'nfe.num_danfe') )
        ->joinInner(array('tu' => 'tb_usuarios'), 'tu.usu_id = tm.id_criacao_usuario', null)
        ->joinLeft(array('tes' => 'tb_empresas'), 'tu.id_empresa = tes.id', array('nome_pessoa' => 'tes.nome_razao'))
        ->where('tm.id_tp_movimento = ?', Material_Model_Bo_TipoMovimento::ENTRADA)
        ->where('te.id_item = ?', $idItem)
        ->group('tem.id_movimento');

        return $this->_db->fetchAll($select);
    }
}