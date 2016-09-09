<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  01/08/2013
 */
class Processo_Model_Dao_PcpTimer extends App_Model_Dao_Abstract
{
    protected $_name     = "pro_tb_pcp_timer";
    protected $_primary  = "id_timer";


    public function getPessoaByProcesso($cod_pro)
    {
        $select = $this->_db->select();
        $select->from(array('pcp' => $this->_name), 'pes_id')
               ->where('pro_id = ?', $cod_pro)
            ->joinLeft(array('te' => 'tb_pessoa'), 'pcp.empresas_id = te.id', array("nome_razao" => "nome"))

            ->group('pcp.pes_id');

        return $this->_db->fetchAll($select);
    }

    public function getTimeByProcesso($idProcesso)
    {
        $select = $this->_db->select();
        $select->from(array('pcp'=>$this->_name), array('total_hora' => new Zend_Db_Expr('
        sum(DATE_PART(\'day\', fim_work::timestamp - inicio_work::timestamp) * 24 +
              DATE_PART(\'hour\', fim_work::timestamp - inicio_work::timestamp))')))

            ->joinLeft(array('te' => 'tb_pessoa'), 'pcp.empresas_id = te.id', array("nome_razao" => "nome"))

//               ->joinInner(array('te' => 'tb_empresas'), 'pcp.empresas_id = te.id', array('pes_nome'=>'nome_razao'))
               ->where('pcp.pro_id = ?', $idProcesso)
               ->group(array('pcp.empresas_id', "te.nome"));

        return $this->_db->fetchAll($select);
    }

    public function getSumTimeByProcesso($idProcesso)
    {
        $select = $this->_db->select();
        $select->from(array('pcp'=>$this->_name), array('total_hora' => new Zend_Db_Expr('sum(DATE_PART(\'day\', fim_work::timestamp - inicio_work::timestamp) * 24 +
              DATE_PART(\'hour\', fim_work::timestamp - inicio_work::timestamp))')))
                ->where('pcp.pro_id = ?', $idProcesso);

        return $this->_db->fetchOne($select);
    }


}