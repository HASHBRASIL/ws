<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  29/05/2013
 */
class Material_Model_Vo_EstoqueMovimento extends App_Model_Vo_Row
{

    public function getEstoque()
    {
        return $this->findParentRow('Material_Model_Dao_Estoque', 'Estoque');
    }

    public function getMovimento()
    {
        return $this->findParentRow('Material_Model_Dao_Movimento', 'Movimento');
    }

    public function getDeWorkspaceTransfer()
    {
        if($this->getMovimento()->id_tp_movimento == Material_Model_Bo_TipoMovimento::SAIDA){
            return $this->getEstoque()->getWorkspace()->nome;
        }elseif ($this->getMovimento()->id_tp_movimento == Material_Model_Bo_TipoMovimento::ENTRADA && $this->getMovimento()->transferencia == 1){
            $estoqueMovimentoDao = new Material_Model_Dao_EstoqueMovimento();
            $select = $estoqueMovimentoDao->select()->where('id_movimento = ?', $this->id_movimento-1);
            $row = $estoqueMovimentoDao->fetchRow($select);
            return $row->getEstoque()->getWorkspace()->nome;
        }
    }

    public function getParaWorkspaceTransfer()
    {
        if($this->getMovimento()->id_tp_movimento == Material_Model_Bo_TipoMovimento::ENTRADA && $this->getMovimento()->transferencia == 1){
            return $this->getEstoque()->getWorkspace()->nome;
        }elseif ($this->getMovimento()->id_tp_movimento == Material_Model_Bo_TipoMovimento::SAIDA && $this->getMovimento()->transferencia == 1){
            $estoqueMovimentoDao = new Material_Model_Dao_EstoqueMovimento();
            $select = $estoqueMovimentoDao->select()->where('id_movimento = ?', $this->id_movimento-1);
            $row = $estoqueMovimentoDao->fetchRow($select);
            return $row->getEstoque()->getWorkspace()->nome;
        }
    }
}