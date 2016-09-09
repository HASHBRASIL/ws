<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  28/06/2013
 */
class Service_Model_Vo_ValorServico extends App_Model_Vo_Row
{

    public function getListValorServico()
    {
        $valorDao = new Service_Model_Dao_ValorServico();
        $select = $valorDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO)->order('dt_criacao DESC');
        return $this->findDependentRowset('Service_Model_Dao_ValorServico', 'Servico', $select);
    }

    public function getEmpresa()
    {
        return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Empresa');
    }
}