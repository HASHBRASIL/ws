<?php
class Financial_Model_Vo_Contas extends App_Model_Vo_Row
{

	public function getBancos()
	{
		return $this->findParentRow('Financial_Model_Dao_Bancos', 'Bancos');
	}

	public function getTipoContaBanco()
	{
		return $this->findParentRow('Financial_Model_Dao_TipoContaBanco', 'TipoContaBanco');
	}

	public function getGrupo()
	{
		return $this->findParentRow('Legacy_Model_Dao_Grupo', 'Grupo');
	}

}