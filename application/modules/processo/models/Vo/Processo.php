<?php
class Processo_Model_Vo_Processo extends App_Model_Vo_Row
{
	public function getEmpresa()
	{
        $pessoaDao = new Legacy_Model_Dao_Pessoa();
        return $pessoaDao->get($this->empresas_id);
//		return $this->findParentRow('Legacy_Model_Dao_Pessoa', 'Empresa');
	}

	public function getStatus()
	{
		return $this->findParentRow('Processo_Model_Dao_Status', 'Status');
	}
	public function getArquivoList()
	{
		if($this->pro_id){
			return $this->findDependentRowset('Processo_Model_Dao_Arquivo', 'Arquivo');
		}
		return null;
	}

	public function getProcessoServicoList()
	{
	    if($this->pro_id){
	        return $this->findDependentRowset('Processo_Model_Dao_ProcessoServico', 'Processo');
	    }
	    return null;
	}

	public function getLoteProducaoList()
	{
	    if($this->pro_id){
	        $loteProducaoDao = new Processo_Model_Dao_LoteProducao();
	        $select = $loteProducaoDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
	        return $this->findDependentRowset('Processo_Model_Dao_LoteProducao', 'Processo', $select);
	    }
	    return null;
	}

	public function getAgrupadoFinancialList()
	{
	    if($this->pro_id){
	        $financialDao = new Financial_Model_Dao_AgrupadorFinanceiro();
	        $select = $financialDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);
	        return $this->findDependentRowset('Financial_Model_Dao_AgrupadorFinanceiro', 'Processo', $select);
	    }
	    return null;
	}

	public function getProcessoPai()
	{
	    return $this->findParentRow('Processo_Model_Dao_Processo', 'ProcessoPai');
	}

	public function getWorkspace()
	{
	    return $this->findParentRow('Auth_Model_Dao_Workspace', 'Workspace');
	}
}