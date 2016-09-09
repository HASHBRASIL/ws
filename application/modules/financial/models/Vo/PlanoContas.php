<?php
class Financial_Model_Vo_PlanoContas extends App_Model_Vo_Row
{

	public function getPlanoContasPai()
	{
		return $this->findParentRow('Financial_Model_Dao_PlanoContas', 'PlanoContas');
	}

	public function getGrupoContas()
	{
		return $this->findParentRow('Financial_Model_Dao_GrupoContas', 'GrupoContas');
	}

    public function getGrupo()
    {
        return $this->findParentRow('Legacy_Model_Dao_Grupo', 'Grupo');
    }
}