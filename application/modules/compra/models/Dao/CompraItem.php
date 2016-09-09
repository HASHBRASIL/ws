<?php
/**
 * @author Vinicius Leonidas
 * @since 21/10/2013
 */
class Compra_Model_Dao_CompraItem extends App_Model_Dao_Abstract
{	
	protected $_name          = "tb_co_compra_item";
	protected $_primary       = "id_compra_item";
	

	protected $_rowClass = 'Compra_Model_Vo_CompraItem';
	
	protected $_referenceMap    = array(
			'Item' => array(
					'columns'           => 'id_item',
					'refTableClass'     => 'Material_Model_Dao_Item',
					'refColumns'        => 'id_item'
			));
	
	public function getItensList($id_campanha = null){
		
		$select = $this->_db->select();
		$select->from(array('tcci' => $this->_name))
		->where('tcci.ativo  = ?', App_Model_Dao_Abstract::ATIVO)
		->joinInner(array('tcc' => 'tb_co_compra'), 'tcc.id_compra = tcci.id_compra')
		->where('tcc.finalizado = ?', App_Model_Dao_Abstract::ATIVO)
		->where('tcc.ativo  = ?', App_Model_Dao_Abstract::ATIVO)
		->joinInner(array('tgi' => 'tb_gm_item'), 'tgi.id_item = tcci.id_item', 'nome');
		if (!empty($id_campanha)) {
			$select->where('tcc.id_campanha = ?', $id_campanha);
		}
		return $this->_db->fetchAll($select);
	} 
}