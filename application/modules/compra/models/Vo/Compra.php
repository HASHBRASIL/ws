<?php
/**
 * @author Vinicius Leônidas
 * @since 21/10/2013
 */
class Compra_Model_Vo_Compra extends App_Model_Vo_Row
{
	public function getCampanha()
	{
		return $this->findParentRow('Compra_Model_Dao_Campanha', 'Campanha');
	}
	public function getUser()
	{
		return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Corporativo');
	}
}