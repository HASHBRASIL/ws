<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 26/7/2013
 *
 */
class Processo_Model_Vo_Historico extends App_Model_Vo_Row
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

	public function getEmpresaGrupo()
	{
        $pessoaDao = new Legacy_Model_Dao_Pessoa();
        return $pessoaDao->get($this->empresas_grupo_id);
//		return $this->findParentRow('Empresa_Model_Dao_EmpresaGrupo', 'EmpresaGrupo');
	}

	public function getPessoal()
	{
		return $this->findParentRow('Auth_Model_Dao_Pessoal', 'Pessoal');
	}
	public function getUsuario()
	{
		return $this->findParentRow('Legacy_Model_Dao_Pessoa', 'Usuario');
	}

}