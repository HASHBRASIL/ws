<?php
class Financial_Model_Vo_AgrupadorFinanceiro extends App_Model_Vo_Row
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
    public function getEmpresaCliente()
    {
        $pessoaDao = new Legacy_Model_Dao_Pessoa();
        return $pessoaDao->get($this->id_pessoa_cliente);
//    	return $this->findParentRow('Legacy_Model_Dao_Pessoa', 'EmpresaCliente');
    }
    public function getGrupo()
    {
        return $this->findParentRow('Legacy_Model_Dao_Grupo', 'Grupo');
    }

    public function getUsuario()
    {
    	return $this->findParentRow('Legacy_Model_Dao_Pessoa', 'Usuario');
    }

    public function getCorrelato()
    {
    	return $this->findParentRow('Financial_Model_Dao_AgrupadorFinanceiro', 'Correlato');
    }
}