<?php
class Processo_Model_Vo_Comentario extends App_Model_Vo_Row
{
	public function getEmpresa()
	{
        $pessoaDao = new Legacy_Model_Dao_Pessoa();
        return $pessoaDao->get($this->id_corporativa);
//		return $this->findParentRow('Legacy_Model_Dao_Pessoa', 'Empresa');
	}

	public function getUsuario()
	{
		return $this->findParentRow('Legacy_Model_Dao_Pessoa', 'Usuario');
	}

}