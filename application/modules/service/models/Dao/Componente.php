<?php
/**
 * @author: Ellyson de Jesus Silva
 * @since: 02/07/2013
 */
class Service_Model_Dao_Componente extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gs_componente";
    protected $_primary  = "id_componente";

    protected $_rowClass        = 'Service_Model_Vo_Componente';

    protected $_referenceMap    = array(
            'Orcamento' => array(
                    'columns'           => 'id_orcamento',
                    'refTableClass'     => 'Service_Model_Dao_Orcamento',
                    'refColumns'        => 'id_orcamento'
            ),
            'Tipo Servico' => array(
                    'columns'           => 'id_tp_servico',
                    'refTableClass'     => 'Service_Model_Dao_TipoServico',
                    'refColumns'        => 'id_tp_servico'
            ),
            'Tipo Componente' => array(
                    'columns'           => 'id_tp_componente',
                    'refTableClass'     => 'Service_Model_Dao_TipoComponente',
                    'refColumns'        => 'id_tp_componente'
            ),
            'Servico' => array(
                    'columns'           => 'id_servico',
                    'refTableClass'     => 'Service_Model_Dao_Servico',
                    'refColumns'        => 'id_servico'
            ),
            'Valor Servico' => array(
                    'columns'           => 'id_valor_servico',
                    'refTableClass'     => 'Service_Model_Dao_ValorServico',
                    'refColumns'        => 'id_valor_servico'
            ),
    );
    public function getComponenteByOrcamento($id_orcamento)
    {
        $select = $this->select();
        $select->where('id_orcamento = ?', $id_orcamento)
               ->where('ativo = ?', App_Model_Dao_Abstract::ATIVO)
               ->order('id_tp_componente');
        return $this->fetchAll($select, 'id_tp_componente DESC');
    }

    public function getPairsSumComponente($id_orcamento)
    {
        $select = $this->_db->select();
        $select->from(array('tc' => $this->_name), array('tc.id_tp_componente', new Zend_Db_Expr("sum(tvs.vl_unitario * tc.quantidade)")))
               ->joinInner(array('tvs' => 'tb_gs_valor_servico'), 'tc.id_valor_servico = tvs.id_valor_servico')
               ->where('tc.id_orcamento = ?', $id_orcamento)
               ->where('tc.ativo = ?', App_Model_Dao_Abstract::ATIVO)
               ->group('tc.id_tp_componente');

        return $this->_db->fetchPairs($select);
    }

    public function getPairsSumTpServico($id_orcamento)
    {
        $select = $this->_db->select();
        $select->from(array('tc' => $this->_name), array('tc.id_tp_componente', new Zend_Db_Expr("sum(tvs.vl_unitario * tc.quantidade)")))
               ->joinInner(array('tvs' => 'tb_gs_valor_servico'), 'tc.id_valor_servico = tvs.id_valor_servico')
               ->where('tc.id_orcamento = ?', $id_orcamento)
               ->where('tc.ativo = ?', App_Model_Dao_Abstract::ATIVO)
               ->group('tc.id_tp_servico');

        return $this->_db->fetchPairs($select);

    }
}