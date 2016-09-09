<?php
class Processo_Model_Vo_LoteProducao extends App_Model_Vo_Row
{
	public function getEmpresa()
	{
        $pessoaDao = new Legacy_Model_Dao_Pessoa();
        return $pessoaDao->get($this->id_empresa);
//		return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Empresa');
	}

}