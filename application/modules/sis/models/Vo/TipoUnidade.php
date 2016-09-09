<?php
/**
 * @author Vinicius Leônidas
 * @since 15/10/2013
 *
 */
class Sis_Model_Vo_TipoUnidade extends App_Model_Vo_Row
{
	public function getUser()
	{
		return $this->findParentRow('Auth_Model_Dao_Usuario', 'TipoCliente');
	}
}