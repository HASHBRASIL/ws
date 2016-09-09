<?php
/**
 * @author Vinicius Leônidas
 * @since 14/02/2014
 */
class Rh_Model_Dao_JustificativaPonto extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_rh_justificacao_ponto';
	protected $_primary = 'id_rh_justificacao_ponto';
    protected $_namePairs = 'descricao';
	
    public function getJustificativa($where){
    	$select = $this->_db
    	->select()
    	->from(array($this->_name ), array('id_rh_justificacao_ponto','descricao'));
    	if($where){
    		if (is_array($where)){
    			foreach ($where as $key => $value){
    				$select->where($key, $value);
    			}
    		}else{
    			$select->where($where);
    		}
    	}
    
    	return $this->_db->fetchPairs($select);
    }
}