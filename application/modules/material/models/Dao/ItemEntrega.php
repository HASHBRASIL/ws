<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  13/05/2013
 */
class Material_Model_Dao_ItemEntrega extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_item_entrega";
    protected $_primary  = 'id_item';

    protected $_rowClass = 'Material_Model_Vo_ItemEntrega';

    protected $_referenceMap    = array(
                'Item' => array(
                        'columns'           => 'id_item',
                        'refTableClass'     => 'Material_Model_Dao_Item',
                        'refColumns'        => 'id_item'
                )
            );

    public function getItemPedido($id_empresa, $id_item)
    {
        $select = $this->_db->select();
        $select->from(array('tee' => $this->_name))
               ->joinInner(array('te' => 'tb_gm_entrega'), 'tee.id_entrega = te.id_entrega')
               ->where('te.id_empresa = ?', $id_empresa)
               ->where('tee.id_item  = ?', $id_item)
               ->where('te.ativo = ?', parent::ATIVO);


        return $this->_db->fetchAll($select);
    }

    public function getPedido($idEmpresa)
    {
        $select = $this->_db->select();
        $select->from(array('tee' => $this->_name))
               ->joinInner(array('te' => 'tb_gm_entrega'), 'tee.id_entrega = te.id_entrega')
               ->joinInner(array('ts' => 'tb_gm_status'), 'ts.id_status = te.id_status', array('nome_status' => 'nome'))
               ->joinInner(array('ti' => 'tb_gm_item'), "tee.id_item = ti.id_item", array('nome_produto'=>'nome'))
               ->joinInner(array('tc' => 'tb_cidades'), "te.id_cidade = tc.cid_id", array('nome_cidade'=>'cid_nome'))
               ->where('te.id_empresa = ?', $idEmpresa)
               ->order('tee.dt_criacao DESC');
        return $this->_db->fetchAll($select);
    }

    public function sumQtdItem($id_item, $idEmpresa)
    {
        $select = $this->_db->select();
        $select->from(array('tee' => $this->_name), new Zend_Db_Expr('sum(tee.quantidade)'))
               ->joinInner(array('te' => 'tb_gm_entrega'), 'tee.id_entrega = te.id_entrega')
               ->where('te.id_empresa = ?', $idEmpresa)
               ->where('tee.id_item = ?', $id_item)
               ->where('tee.ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->_db->fetchOne($select);
    }


}