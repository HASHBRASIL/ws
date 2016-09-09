<?php
class Processo_Model_Vo_Planejamento extends App_Model_Vo_Row
{
	public function getProcesso()
	{
		return $this->findParentRow('Processo_Model_Dao_Processo', 'Processo');
	}

	public function getPrioridade()
	{
		return $this->findParentRow('Processo_Model_Dao_Prioridade', 'Prioridade');
	}

}