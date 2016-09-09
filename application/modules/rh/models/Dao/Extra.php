<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 21/08/2014
 */
class Rh_Model_Dao_Extra extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_rh_extra';
	protected $_primary = 'id_extra';

	protected $_rowClass = 'Rh_Model_Vo_Extra';
	
	protected $_referenceMap    = array(
			'AprovadoGerente' => array(
					'columns'           => 'id_aprovacao_gerente',
					'refTableClass'     => 'Auth_Model_Dao_Usuario',
					'refColumns'        => 'usu_id'
			),
			'AprovadoDiretor' => array(
					'columns'           => 'id_aprovacao_diretor',
					'refTableClass'     => 'Auth_Model_Dao_Usuario',
					'refColumns'        => 'usu_id'
			)
	
	);

	public function sumHoraExtra($idCalculoPonto)
	{
		$select = $this->_db->select();
		$select->from($this->_name, array('sum_hora' => new Zend_Db_Expr('SEC_TO_TIME( SUM( TIME_TO_SEC( hora ) ) )')))
			   ->where('id_calculo_ponto = ?', $idCalculoPonto);
		return $this->_db->fetchOne($select);
	}
	
}