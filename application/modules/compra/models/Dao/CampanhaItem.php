<?php
/**
 * @author Vinicius Leônidas
 *
 */
class Compra_Model_Dao_CampanhaItem extends App_Model_Dao_Abstract
{

	protected $_name          = "tb_co_campanha_item";
	protected $_primary       = "id_campanha_item";
	

	protected $_rowClass = 'Compra_Model_Vo_CampanhaItem';
	
	protected $_dependentTables = array('Compra_Model_Dao_CampanhaCorporativo');
	
	protected $_referenceMap    = array(
			'Item' => array(
					'columns'           => 'id_item',
					'refTableClass'     => 'Material_Model_Dao_Item',
					'refColumns'        => 'id_item'
			));
	
	public function getItens($id_campanha, $id_item = null, $term = null, $valor = null){
		 $select = $this->_db->select();
		 $select->from(array('tcci' => $this->_name))
		 ->where('tcci.id_campanha = ?', $id_campanha)
		 ->joinInner(array('tgi' => 'tb_gm_item'), 'tgi.id_item = tcci.id_item', array('tgi.*'));
		 if(!empty($id_item)){
		 	$select->where('tgi.id_item = ?', $id_item);
		 	return $this->_db->fetchRow($select);
		 }
		 if (!empty($term)) {
		 	$select->where($term." = ?", $valor);
		 	return $this->_db->fetchRow($select);
		 }else{
		 	return $this->_db->fetchAll($select);
		 }
	}
	
	public function getItensReferencia($id_campanha, $valor = null){
		$select = $this->_db->select();
		$select->from(array('tcci' => $this->_name), '')
		->where('tcci.id_campanha = ?', $id_campanha)
		->joinInner(array('tgi' => 'tb_gm_item'), 'tgi.id_item = tcci.id_item', array('label' => 'tgi.referencia', 'id' => 'tgi.id_item'));
		$select->where('tgi.referencia IS NOT NULL');
		$select->where('tgi.referencia like "%'.$valor.'%"');
		return $this->_db->fetchAll($select);
	}
	
	public function getItensNome($id_campanha, $valor = null){
		$select = $this->_db->select();
		$select->from(array('tcci' => $this->_name), '')
		->where('tcci.id_campanha = ?', $id_campanha)
		->joinInner(array('tgi' => 'tb_gm_item'), 'tgi.id_item = tcci.id_item', array('label' => 'tgi.nome', 'id' => 'tgi.id_item'));
		$select->where('tgi.nome like "%'.$valor.'%"');
		return $this->_db->fetchAll($select);
	}
}