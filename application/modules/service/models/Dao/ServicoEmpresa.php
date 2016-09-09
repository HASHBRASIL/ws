<?php
class Service_Model_Dao_ServicoEmpresa extends App_Model_Dao_Abstract
{
    protected $_name = "ta_gs_servico_empresas";
    protected $_primary = array('id_servico', 'id_empresas');

    public function deleteByServico($idServico)
    {
        $where = array("id_servico = ?" => $idServico);
        return $this->delete($where);
    }

    public function getFornecedorByServico($idServico)
    {
        $select = $this->_db->select();
        $select->from(array('ta' => $this->_name))
        ->joinInner(array('te' => 'tb_empresas'), 'ta.id_empresas = te.id')
        ->where('ta.id_servico = ?', $idServico);

        return $this->_db->fetchAll($select);
    }
}