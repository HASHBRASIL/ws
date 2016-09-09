<?php
class Financial_Model_Vo_SacadoFinanceiro extends App_Model_Vo_Row
{

	public function getEmpresa()
	{
        $pessoaDao = new Legacy_Model_Dao_Pessoa();
        return $pessoaDao->get($this->id_pessoa_empresa);
	}
}