<?php
class Auth_Model_Vo_Usuario extends App_Model_Vo_Row
{

	public function getEmpresa()
	{
		return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Empresa');
	}

	public function __toString()
	{
	    $empresa = $this->getEmpresa();
	    return empty($empresa->nome_razao)? $empresa->fantasia: $empresa->nome_razao;
	}

}