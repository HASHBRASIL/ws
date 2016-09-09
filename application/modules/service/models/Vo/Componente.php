<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 03/07/2013
 */
class Service_Model_Vo_Componente extends App_Model_Vo_Row
{
    public function getOrcamento()
    {
        return $this->findParentRow('Service_Model_Dao_Orcamento', 'Orcamento');
    }
    public function getTpServico()
    {
        return $this->findParentRow('Service_Model_Dao_TipoServico', 'Tipo Servico');
    }
    public function getTpComponente()
    {
        return $this->findParentRow('Service_Model_Dao_TipoComponente', 'Tipo Componente');
    }
    public function getServico()
    {
        return $this->findParentRow('Service_Model_Dao_Servico', 'Servico');
    }
    public function getValorServico()
    {
        return $this->findParentRow('Service_Model_Dao_ValorServico', 'Valor Servico');
    }
}