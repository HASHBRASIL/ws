<?php
class Rh_Model_Vo_Extra extends App_Model_Vo_Row
{

	public function getAprovadoGerente()
	{
		return $this->findParentRow('Auth_Model_Dao_Usuario', 'AprovadoGerente');
	}
	
	public function getAprovadoDiretor()
	{
		return $this->findParentRow('Auth_Model_Dao_Usuario', 'AprovadoDiretor');
	}


}