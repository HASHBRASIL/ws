<?php
/**
 * @author Vinicius Leônidas
 * @since 03/12/2013
 */
class Rh_Model_Dao_ModeloSintetico extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_rh_modelo_sintetico';
	protected $_primary = 'id_rh_modelo_sintetico';
	protected $_namePairs = 'descricao';
	
	protected $_rowClass = 'Rh_Model_Vo_ModeloSintetico';
	
	protected $_referenceMap    = array(
			'Entrada' => array(
					'columns'           => 'id_rh_entrada_sintetico',
					'refTableClass'     => 'Rh_Model_Dao_EntradaSintetico',
					'refColumns'        => 'id_rh_entrada_sintetico'
			),
			'Natureza' => array(
					'columns'           => 'id_rh_natureza_sintetico',
					'refTableClass'     => 'Rh_Model_Dao_NaturezaSintetico',
					'refColumns'        => 'id_rh_natureza_sintetico'
			)
	);
	
	protected $_dependentTables = array('Rh_Model_Dao_ReferenciaFinanceiroModelo');	
	
	public function getAutocompleteModelo($term, $chave = null, $valor = null, $where = null, $ordem = null, $limit = null){
		
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
		->from(array('ms' => $this->_name ), array('ms.codigo', 'value' => $valor,'id'=>$chave, 'label' => $valor, 'id_rh_entrada_sintetico'))
		->order(array('ms.codigo',$ordem ? $ordem : $valor))
		->joinInner(array('tes' => 'tb_rh_entrada_sintetico'), "tes.id_rh_entrada_sintetico = ms.id_rh_entrada_sintetico", array('tes.nome'));
		if( is_numeric( $limit) ){
			$select->limit( $limit );
		}else {	
			$select->limit(1000);
		}
		if($where){
			if (is_array($where)){
				foreach ($where as $key => $value){
					$select->where($key, $value);
				}
			}else{
				$select->where($where);
			}
		}
		$select->where($valor.' like "%'.$term.'%"');
	
		return $this->_db->fetchAll($select);
	}
}