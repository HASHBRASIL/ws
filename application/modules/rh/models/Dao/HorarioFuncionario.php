<?php
/**
 * @author Ellyson de Jesus Silva
* @since 29/07/2014
*/
class Rh_Model_Dao_HorarioFuncionario extends App_Model_Dao_Abstract
{
	protected $_name 	= 'tb_rh_horario_funcionario';
	protected $_primary = 'id_horario_funcionario';
	
	protected $_rowClass = 'Rh_Model_Vo_HorarioFuncionario';
	
	protected $_referenceMap    = array(
	
			'Funcionario' => array(
					'columns'           => 'id_rh_funcionario',
					'refTableClass'     => 'Rh_Model_Dao_Funcionario',
					'refColumns'        => 'id_rh_funcionario'
			)
	);

}