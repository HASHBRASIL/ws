<?php
/**
 * @author Ellyson Silva
 * @since 11/08/2014
 */
class Rh_Model_Dao_Configuracao extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_rh_configuracao';
	protected $_primary = 'id_configuracao';
	
	public function getAllConfiguracaoUsuario($idUsuario, $nivel, $idWorkspace)
	{
		$select = $this->_db->select();
		$select->from(array('tc' => $this->_name) )
			   ->joinInner(array('tcu' => 'ta_rh_configuracao_x_usuario'), 	'tc.id_configuracao = tcu.id_configuracao')
			   ->where('tcu.id_usuario = ?', $idUsuario)
			   ->where('tc.ativo = ?', App_Model_Dao_Abstract::ATIVO)
			   ->where('tcu.nivel = ?', $nivel)
			   ->where('id_workspace = ?', $idWorkspace);
		
		return $this->_db->fetchAll($select);
	}
}