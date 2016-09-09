<?php
class Service_Model_Dao_ServicoCentroCusto extends App_Model_Dao_Abstract
{
    protected  $_name = "ta_gs_servico_centro_custo";
    protected $_primary = array("id_servico", "cec_id");

    public function deleteByServico($idServico)
    {
        $where = array("id_servico = ?" => $idServico);
        return $this->delete($where);
    }

    public function getCentroCustoByServico($idServico)
    {
        $select = $this->_db->select();
        $select->from(array('tsc' => $this->_name))
               ->joinInner(array('tcc'=>'tb_centro_custo'), 'tcc.cec_id = tsc.cec_id')
                ->where('tsc.id_servico = ?', $idServico);

        return $this->_db->fetchAll($select);
    }
}