<?php
/**
 * @author Vinicius Leônidas
 * @since 14/10/2013
 *
 */
class Sis_Model_Vo_Feedback extends App_Model_Vo_Row
{
	public function getAssunto()
	{
		return $this->findParentRow('Sis_Model_Dao_TipoFeedback', 'TipoFeedback');
	}
	public function getUser()
	{
		return $this->findParentRow('Auth_Model_Dao_Usuario', 'TipoCliente');
	}
	public function getStatus()
	{
		return $this->findParentRow('Sis_Model_Dao_StatusFeedback', 'Status');
	}
}