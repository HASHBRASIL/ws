<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 24/07/2014
 */
class Rh_Model_Dao_ConfigHorario extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_rh_config_horario';
	protected $_primary = 'id_config_horario';
	
	/**
	 * @desc Weekday according to ISO 8601 (1 = Monday, 7 = Sunday)
	 */
	public function getSemana()
	{
		return array(
						1 => 'segunda-feira', 
						2 => 'terça-feira', 
						3 => 'quarta-feira', 
						4 => 'quinta-feira',
						5 => 'Sexta-feira', 
						6 => 'sábado', 
						7 => 'domingo'
				);
	}

	public function horarioPadraoByDia($data, $idFuncionario)
	{
		$dataZend = new Zend_Date($data);
		$idSemana = $dataZend->toString(Zend_Date::WEEKDAY_8601);
		$select = $this->_db->select();
		$select->from(Array('tch' => $this->_name))
			   ->joinInner(array('func'=>'tb_rh_horario_funcionario'), 'tch.id_horario = func.id_horario', null)
			   ->where('func.data <= ?', $data)
			   ->where('func.id_rh_funcionario = ?', $idFuncionario)
			   ->where('tch.semana = ?', $idSemana);
		return $this->_db->fetchRow($select);
	}
}