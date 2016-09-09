<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  13/11/2013
 */
class Processo_Model_Dao_LoteProducao extends App_Model_Dao_Abstract
{
    protected $_name     = "pro_tb_gp_lote_producao";
    protected $_primary  = "id_lote_producao";

    protected $_rowClass = 'Processo_Model_Vo_LoteProducao';


    protected $_referenceMap    = array(
            'Processo' => array(
                    'columns'           => 'id_processo',
                    'refTableClass'     => 'Processo_Model_Dao_Processo',
                    'refColumns'        => 'pro_id'
            ),
            'Empresa' => array(
                    'columns'           => 'id_empresa',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            )
    );

    public function idMaxByProcesso($idProcesso)
    {
        $select = $this->_db->select();
        $select->from($this->_name, 'cod_lote')
               ->where('id_processo = ?', $idProcesso)
               ->order('cod_lote desc')
               ->limit(1);

        return $this->_db->fetchOne($select);
    }

    public function inativarByProcesso($idProcesso)
    {
        try {
            $data     = array('ativo' => parent::INATIVO);
            $where    = array('id_processo = ?' => $idProcesso);
            $this->update($data, $where);
            return true;
        }catch (Exception $e){
            return false;
        }
    }
}