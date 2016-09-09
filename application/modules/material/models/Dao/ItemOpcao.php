<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  03/10/2013
 */
class Material_Model_Dao_ItemOpcao extends App_Model_Dao_Abstract
{
    protected $_name     = "ta_gm_item_x_opcao";
    protected $_primary  = array("id_item", "id_opcao");

    public function findOpcao($idItem, $idAtributo = null)
    {
        $select = $this->_db->select();
        $select->from(array('tio' => $this->_name))
               ->joinInner(array('tmo' => 'tb_gm_opcao'), 'tio.id_opcao = tmo.id_opcao', array('nome_opcao' => 'nome'))
               ->joinInner(array('ta' => 'tb_gm_atributo'), 'tmo.id_atributo = ta.id_atributo', array('id_atributo', 'nome_atributo' => 'nome'))
               ->joinInner(array('ti' => 'tb_gm_item'), 'tio.id_item = ti.id_item', array('nome_item' => 'nome', 'id_item','produto_finalizado', 'revenda', 'materia_prima', 'ncm_sh'))
               ->where('tio.id_item = ?', $idItem)
               ->order('tmo.id_atributo');

        if($idAtributo){
            $select->where('tmo.id_atributo = ?', $idAtributo);
        }

        return $this->_db->fetchAll($select);
    }


    public function findAtributoByItem($idItem)
    {
        $select = $this->_db->select();
        $select->from(array('tio' => $this->_name), null)
               ->joinInner(array('tmo' => 'tb_gm_opcao'), 'tio.id_opcao = tmo.id_opcao', null)
               ->joinInner(array('ta' => 'tb_gm_atributo'), 'tmo.id_atributo = ta.id_atributo', array('id_atributo', 'nome_atributo' => 'nome'))
               ->where('tio.id_item = ?', $idItem)
               ->order('tmo.id_atributo')
               ->group('tmo.id_atributo');

        return $this->_db->fetchAll($select);

    }

    public function deleteByAtributo($idAtributo, $idItem)
    {
        $select = $this->_db->select();
        $select->from(array('tio' => $this->_name), null)
               ->joinInner(array('tmo' => 'tb_gm_opcao'), 'tio.id_opcao = tmo.id_opcao', 'id_opcao')
               ->joinInner(array('ta' => 'tb_gm_atributo'), 'tmo.id_atributo = ta.id_atributo', array('id_atributo', 'nome_atributo' => 'nome'))
               ->where('tio.id_item = ?', $idItem)
               ->where('tmo.id_atributo = ?', $idAtributo);
        $opcaoList = $this->_db->fetchCol($select);
        if(count($opcaoList) > 0){
            $where = array('id_item = ?' => $idItem, 'id_opcao in(?)' => $opcaoList);
            $this->delete($where);
            return true;
        }
        return false;
    }
}