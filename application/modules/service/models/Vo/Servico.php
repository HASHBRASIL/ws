<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  28/06/2013
 */
class Service_Model_Vo_Servico extends App_Model_Vo_Row
{
    public function getListValorServico()
    {
        if($this->id_servico){
            $valorDao = new Service_Model_Dao_ValorServico();
            $select = $valorDao->select()->where('ativo = ?', App_Model_Dao_Abstract::ATIVO)->order('dt_criacao DESC');
            return $this->findDependentRowset('Service_Model_Dao_ValorServico', 'Servico', $select);
        }
        return null;
    }
}