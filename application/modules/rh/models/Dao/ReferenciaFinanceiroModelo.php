<?php
/**
 * @author Vinicius Leônidas
* @since 03/12/2013
*/
class Rh_Model_Dao_ReferenciaFinanceiroModelo extends App_Model_Dao_Abstract
{
	protected $_name          = "rel_rh_financeiro";
	protected $_primary       = array("fin_id","id_rh_modelo_sintetico");
	
	protected $_rowClass = 'Rh_Model_Vo_ReferenciaFinanceiroModelo';
	
	protected $_referenceMap    = array(
			'Modelo' => array(
					'columns'           => 'id_rh_modelo_sintetico',
					'refTableClass'     => 'Rh_Model_Dao_ModeloSintetico',
					'refColumns'        => 'id_rh_modelo_sintetico'
			),
			'Financial' => array(
					'columns'           => 'fin_id',
					'refTableClass'     => 'Financial_Model_Dao_Financial',
					'refColumns'        => 'fin_id'
			)
	);
	
	protected $_dependentTables = array('Financial_Model_Dao_Financial','Rh_Model_Dao_ModeloSintetico');
	
	public function delReferencia($id){
		return $this->delete(array("fin_id = ?" => $id));
	}
		
	public function totalProventoAndDesconto($tipo, $dataCompetencia = null){
		$workspaceSession = new Zend_Session_Namespace('workspace');
		
		$select = $this->_db->select();
		$select->from(array('rel' => $this->_name), array('referencia' => 'SUM(rel.referencia)'))
		->joinLeft(array('tf' => 'tb_financeiro'), 'rel.fin_id = tf.fin_id', array('total' => 'SUM(tf.fin_valor)'))
		->joinLeft(array('tms' => 'tb_rh_modelo_sintetico'), 'rel.id_rh_modelo_sintetico = tms.id_rh_modelo_sintetico', array('tms.codigo', 'tms.descricao'))
		->where('tf.ativo = ?', App_Model_Dao_Abstract::ATIVO)
		->where('tfp.ativo = ?', App_Model_Dao_Abstract::ATIVO)
		->where("tfp.id_workspace = ?", $workspaceSession->id_workspace)
		->group('rel.id_rh_modelo_sintetico');
		if($tipo == 1){
			$select->joinLeft(array('tfp' => 'tb_rh_folha_de_pagamento'), "tf.id_agrupador_financeiro = tfp.tss_id", null)
			->where('tf.id_agrupador_financeiro = tfp.tss_id');
		}else {
			$select->joinLeft(array('tfp' => 'tb_rh_folha_de_pagamento'), "tf.id_agrupador_financeiro = tfp.tse_id", null)
			->where('tf.id_agrupador_financeiro = tfp.tse_id');
		}
		if (!empty($dataCompetencia)) {
			$select->where('tf.fin_competencia = ?', $dataCompetencia);
		}
		if (!empty($tipo)) {
			$select->where("id_rh_natureza_sintetico = ?", $tipo);
			return $this->_db->fetchAll($select);
		} else {
			return $this->_db->fetchAll($select);
		}
	}
}