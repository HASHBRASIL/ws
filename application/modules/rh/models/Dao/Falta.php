<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 25/08/2014
 */
class Rh_Model_Dao_Falta extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_rh_falta';
	protected $_primary = 'id_falta';
	
	public function sumHoraFalta($idCalculoPonto)
	{
		$select = $this->_db->select();
		$select->from($this->_name, array('sum_hora' => new Zend_Db_Expr('SEC_TO_TIME( SUM( TIME_TO_SEC( hora ) ) )')))
		->where('id_calculo_ponto = ?', $idCalculoPonto);
		return $this->_db->fetchOne($select);
	}
	
	public function validarWeekDsr($numWeek)
	{
		$select = $this->_db->select();
		$select->from(array('tf' => 'tb_rh_falta'))
			   ->joinInner(array('tc' => 'tb_rh_calculo_ponto'), 'tf.id_calculo_ponto = tc.id_calculo_ponto')
			   ->where('week(tc.data, 1) = ?', $numWeek)
			   ->where('tf.dsr = ?', true);
		return $this->_db->fetchAll($select);
	}
}