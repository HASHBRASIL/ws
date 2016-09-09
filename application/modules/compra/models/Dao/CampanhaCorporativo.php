<?php
/**
 * @author Vinicius Leônidas
 * @since 21/10/2013
 */
class Compra_Model_Dao_CampanhaCorporativo extends App_Model_Dao_Abstract
{
	protected $_name          = "tb_co_campanha_corporativa";
	protected $_primary       = "id_campanha_corporativa";

	protected $_rowClass = 'Compra_Model_Vo_CampanhaCorporativo';

	protected $_referenceMap    = array(
			'Campanha' => array(
					'columns'           => 'id_campanha',
					'refTableClass'     => 'Compra_Model_Dao_Campanha',
					'refColumns'        => 'id_campanha'
			),
			'Corporativo' => array(
					'columns'           => 'id_corporativa',
					'refTableClass'     => 'Empresa_Model_Dao_Empresa',
					'refColumns'        => 'id'
			),
			'tipo_comissao' => array(
					'columns'           => 'id_tp_comissao',
					'refTableClass'     => 'Compra_Model_Dao_TipoComissao',
					'refColumns'        => 'id_tp_comissao'
			));

	public function getCampanhasCorporativa(){
		$select = $this->_db->select();
		$select->from(array('tccc' => $this->_name))
		->where("tccc.id_corporativa = ?", Zend_Auth::getInstance()->getIdentity()->id)
		->joinInner(array('tcc' => 'tb_co_campanha'), 'tcc.id_campanha = tccc.id_campanha')
		->where('tcc.dt_fim >= ?', date("Y-m-d H:m:s") )
		->where("tcc.ativo = ?", App_Model_Dao_Abstract::ATIVO);
		return $this->_db->fetchAll($select);
	}
	
}