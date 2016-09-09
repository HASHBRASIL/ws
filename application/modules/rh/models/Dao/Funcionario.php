<?php
/**
 * @author Vinicius Leônidas
 * @since 17/12/2013
 */
class Rh_Model_Dao_Funcionario extends App_Model_Dao_Abstract{
	
	protected $_name = 'tb_rh_funcionario';
	protected $_primary = 'id_rh_funcionario';
	
	protected $_rowClass = 'Rh_Model_Vo_Funcionario';
	
	protected $_referenceMap    = array(
	
			'Empresa' => array(
					'columns'           => 'id_empresa',
					'refTableClass'     => 'Empresa_Model_Dao_Empresa',
					'refColumns'        => 'id'
			)
	);
	
	public function getFuncionario($where){
		$date = new Zend_Date();
		$select = $this->_db
		->select()
		->from(array('trf' => $this->_name ), null)
		->joinInner(array('te' => 'tb_empresas'), "te.id = trf.id_empresa", array('te.id','te.nome_razao'))
		->where('trf.dt_demissao > ? or trf.dt_demissao is null', $date->toString('yyyy-MM-dd'));
		if($where){
			if (is_array($where)){
				foreach ($where as $key => $value){
					$select->where($key, $value);
				}
			}else{
				$select->where($where);
			}
		}
		$select->order('te.nome_razao');
		return $this->_db->fetchPairs($select);
	}
	
	public function getIdFuncionario($where, $order = null){

		$date = new Zend_Date();
		$select = $this->_db
		->select()
		->from(array('trf' => $this->_name ), 'trf.id_rh_funcionario')
		->joinInner(array('te' => 'tb_empresas'), "te.id = trf.id_empresa", array('te.nome_razao'))
		->where('trf.dt_demissao > ? or trf.dt_demissao is null', $date->toString('yyyy-MM-dd'));
		if($where){
			if (is_array($where)){
				foreach ($where as $key => $value){
					$select->where($key, $value);
				}
			}else{
				$select->where($where);
			}
		}
		if($order){
			$select->order($order);
		}
		return $this->_db->fetchPairs($select);
	}
}