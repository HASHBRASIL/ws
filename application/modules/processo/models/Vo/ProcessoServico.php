<?php
class Processo_Model_Vo_ProcessoServico extends App_Model_Vo_Row
{
	public function getProcesso()
	{
		return $this->findParentRow('Processo_Model_Dao_Processo', 'Processo');
	}

	public function getServico()
	{
		return $this->findParentRow('Service_Model_Dao_Servico', 'Servico');
	}
}