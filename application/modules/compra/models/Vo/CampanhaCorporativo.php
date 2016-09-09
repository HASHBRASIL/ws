<?php
/**
 * @author Vinicius Leônidas
 * @since 21/10/2013
 */
class Compra_Model_Vo_CampanhaCorporativo extends App_Model_Vo_Row
{
	public function getCampanha()
	{
		return $this->findParentRow('Compra_Model_Dao_Campanha', 'Campanha');
	}
	public function getCorporativo()
	{
	    return $this->findParentRow('Empresa_Model_Dao_Empresa','Corporativo');
	}

	public function getTipoComissao()
	{
	    return $this->findParentRow('Compra_Model_Dao_TipoComissao','tipo_comissao');
	}
	
	public function getFinalizado()
	{
		$campanhaDao = new Compra_Model_Dao_Campanha();
		$select = $campanhaDao->select()->where('finalizado = ?', App_Model_Dao_Abstract::ATIVO);
		return $this->findDependentRowset('Compra_Model_Dao_Campanha', 'Campanha', $select);
	}
}