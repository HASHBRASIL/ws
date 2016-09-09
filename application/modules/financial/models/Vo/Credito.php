<?php
class Financial_Model_Vo_Credito extends App_Model_Vo_Row
{

	public function getEmpresa()
	{
        $pessoaDao = new Legacy_Model_Dao_Pessoa();
        return $pessoaDao->get($this->empresas_id);
//		return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Empresa');
	}

}