<?php
/**
 * @author Vinicius Leônidas
 * @since 06/01/2014
 */
class Rh_Model_Dao_Cbo extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_rh_cbo';
	protected $_primary = 'id_rh_cbo';
	
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
		->from(array('ms' => $this->_name ), array('ms.codigo', 'value' => $valor,'id'=>$chave, 'label' => $valor, 'codigo'))
		->order(array('ms.codigo',$ordem ? $ordem : $valor));
		if( is_numeric( $limit) ){
			$select->limit( $limit );
		}else {
			$select->limit(1000);
		}
		$select->where($valor.' like "%'.$term.'%"');
	
		return $this->_db->fetchAll($select);
	}
}