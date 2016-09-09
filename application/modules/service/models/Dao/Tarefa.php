<?php
class Service_Model_Dao_Tarefa extends App_Model_Dao_Abstract
{
    protected $_name = "tb_gs_tarefa";
    protected $_primary = "id_tarefa";

    public function getListTarefa($idServico)
    {
        $select = $this->_db->select();
        $select->from($this->_name)
               ->where('ativo = ?', App_Model_Dao_Abstract::ATIVO )
               ->where('id_servico = ?', $idServico);

        return $this->_db->fetchAll($select);
    }

    public function inativarAll($idServico)
    {
        $criteria = array('id_servico = ?' => $idServico);
        $listTarefa = $this->fetch($criteria);
        if(count($listTarefa) > 0){
            foreach ($listTarefa as $tarefa){
                $row = $this->find($tarefa->id_tarefa)->current();
                $row->ativo = self::INATIVO;
                $row->save();
            }
        }
    }

}