<?php
/**
 * @author Vinicius Leonidas
 * @since  24/10/2013
 */
class Compra_Model_Dao_CompraItemOpcao extends App_Model_Dao_Abstract
{
    protected $_name          = "ta_co_compra_item_x_gm_opcao";
    protected $_primary 			= array('id_compra_item', 'id_opcao');

    public function delOpcao($idCompra){
    	return $this->delete(array("id_compra_item = ?" => $idCompra));

    }

    public function getOpcoes($idCompra){

    	$select = $this->_db->select();
    	$select->from(array('tcito' => $this->_name))
    	->where('tcito.id_compra_item = ?', $idCompra)
    	->joinInner(array('tgo'=>'tb_gm_opcao'), 'tgo.id_opcao = tcito.id_opcao')
    	->joinInner(array('tga'=>'tb_gm_atributo'), 'tga.id_atributo = tgo.id_atributo', array('nome_atributo'=>'tga.nome'));

    	return $this->_db->fetchAll($select);
    }

    public function getIdOpcoes($idCompra){

    	$select = $this->_db->select();
    	$select->from(array('tcito' => $this->_name), 'id_opcao')
    	->where('tcito.id_compra_item = ?', $idCompra)
    	->joinInner(array('tgo'=>'tb_gm_opcao'), 'tgo.id_opcao = tcito.id_opcao', null)
    	->joinInner(array('tga'=>'tb_gm_atributo'), 'tga.id_atributo = tgo.id_atributo', null);

    	return $this->_db->fetchCol($select);
    }

    public function findOpcaoByCompra($id_compra_item)
    {
    	$select = $this->_db->select();
    	$select->from(array('tcito' => $this->_name), 'id_opcao')
    	->where('tcito.id_compra_item = ?', $id_compra_item);
    	return $this->_db->fetchCol($select);
    }
    
    public function findNomeOpcaoByCompra($id_compra_item){
    	$select = $this->_db->select();
    	$select->from(array('tcito' => $this->_name), null)
    	->where('tcito.id_compra_item = ?', $id_compra_item)
    	->joinInner(array('tgo'=>'tb_gm_opcao'), 'tgo.id_opcao = tcito.id_opcao', 'tgo.nome')
    	->joinInner(array('tga'=>'tb_gm_atributo'), 'tga.id_atributo = tgo.id_atributo', array('nome_atributo'=>'tga.nome'));
    	return $this->_db->fetchAll($select);
    }
}