<?php
/**
 * @author Ellyson Silva
 * @since 11/08/2014
 */
class Rh_Model_Dao_ConfiguracaoUsuario extends App_Model_Dao_Abstract
{
	protected $_name = 'ta_rh_configuracao_x_usuario';
	protected $_primary = array('id_configuracao', 'id_usuario');
	
	public function deleteByConfiguracao($idConfiguracao){
		$criteria = array('id_configuracao = ?' => $idConfiguracao);
		return $this->delete($criteria);
	}
	
}