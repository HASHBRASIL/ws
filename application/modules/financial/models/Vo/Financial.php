<?php
class Financial_Model_Vo_Financial extends App_Model_Vo_Row
{

    public function getStatus()
    {
        return $this->findParentRow('Financial_Model_Dao_Status', 'Status');
    }

    public function getSacadoFinanceiro()
    {
    	return $this->findDependentRowset('Financial_Model_Dao_SacadoFinanceiro', 'Financial')->current();
    }

    public function getMoeda()
    {
        return $this->findParentRow('Financial_Model_Dao_Moeda', 'Moeda');
    }

    public function getConta()
    {
        return $this->findParentRow('Financial_Model_Dao_Contas', 'Conta');
    }

    public function getDocumentoInterno()
    {
        return $this->findParentRow('Financial_Model_Dao_DocumentoInterno', 'DocumentoInterno');
    }

    public function getDocumentoExterno()
    {
        return $this->findParentRow('Financial_Model_Dao_DocumentoExterno', 'DocumentoExterno');
    }

    public function getPlanoContas()
    {
    	return $this->findParentRow('Financial_Model_Dao_PlanoContas', 'PlanoContas');
    }

    public function getCentroCusto()
    {
    	return $this->findParentRow('Financial_Model_Dao_CentroCusto', 'CentroCusto');
    }


    public function getModelo(){
        return $this->findParentRow('Rh_Model_Dao_ReferenciaFinanceiroModelo', 'ModeloSintetico');
    }

    public function getUsuario()
    {
    	return $this->findParentRow('Auth_Model_Dao_Usuario', 'Usuario');
    }

    public function getAgrupadorFinanceiro()
    {
        return $this->findParentRow('Financial_Model_Dao_AgrupadorFinanceiro', 'AgrupadorFinanceiro');
    }

    public function getCorrelato()
    {
        return $this->findParentRow('Financial_Model_Dao_Financial', 'Correlato');
    }

}