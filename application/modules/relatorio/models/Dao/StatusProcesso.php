<?php
/**
 * @author Alexandre Nascimetnto Barbosa
 * @since  07/06/2013
 */
class Relatorio_Model_Dao_StatusProcesso extends App_Model_Dao_Abstract
{
    protected $_name = "vw_status";
    protected $_primary = "sta_id";
    protected $_namePairs = 'status';


    protected $_dependentTables = array('Relatorio_Model_Dao_ProcessoS');

    public function getPairsOrdemDesc()
    {
        $select = $this->_db->select();
        $select->from(array('sta' => 'tb_status'), array('sta_id', "concat(sta_numero, ' - ' , sta_descricao) as status"))
        ->order('sta_descricao');

        return $this->_db->fetchPairs($select);
    }
}