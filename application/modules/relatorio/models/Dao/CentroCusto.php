<?php
class Relatorio_Model_Dao_CentroCusto extends App_Model_Dao_Abstract
{
    protected $_name      = "tb_centro_custo";
    protected $_primary   = "";

    public function getRegistros(){

        $select = $this->_db->select()
            ->from(array('cc'=>$this->_name), array(
                        'cc.cec_id',
                        'cc.cec_descricao',
                        'ope_nome'=> new Zend_Db_Expr("CASE ope.ope_nome WHEN  null THEN '-----' ELSE ope.ope_nome END"),
                        'valor'    => new Zend_Db_Expr("CASE SUM(fin.fin_valor) when null  THEN 0.00 ELSE SUM(fin.fin_valor) END" )
                ))
            ->joinLeft(array('fin'=>'tb_financeiro'), 'fin.cec_id = cc.cec_id',null)
            ->joinLeft(array('ope'=>'tb_operacoes'), 'ope.ope_id = fin.ope_id', null)
            ->group(array('cc.cec_id','fin.ope_id'))
            ->order('cc.cec_descricao')
            ->order('ope.ope_nome desc');
    return $this->_db->fetchAll($select, null, Zend_Db::FETCH_OBJ);
    }
}
