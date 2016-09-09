<?php
class Rh_Model_Vo_FolhaDePagamento extends App_Model_Vo_Row
{

    public function getPlanoContas()
    {
    	return $this->findParentRow('Financial_Model_Dao_PlanoContas', 'PlanoContas');
    }

    public function getMoeda()
    {
        return $this->findParentRow('Financial_Model_Dao_Moeda', 'Moeda');
    }

    public function getCentroCusto()
    {
        return $this->findParentRow('Financial_Model_Dao_CentroCusto', 'CentroCusto');
    }

    public function getTipoMovimento()
    {
        return $this->findParentRow('Financial_Model_Dao_TipoMovimento', 'TipoMovimento');
    }
    public function getEmpresa()
    {
    	return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Empresa');
    }

    public function getTipoPagamento()
    {
    	return $this->findParentRow('Rh_Model_Dao_TipoPagamento', 'TipoPagamento');
    }

}