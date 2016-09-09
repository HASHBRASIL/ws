<?php
class Relatorio_Model_Dao_Fornecedor extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_empresas";
    protected $_primary       = "id";
    protected $_namePairs     = "fantasia";

    public function getAutocomplete($term, $chave = null, $valor = null, $where = null, $ordem = null, $limit = null){
        $select = $this->_db->select()
        ->from( array('te' => $this->_name), array('value'      => new Zend_Db_Expr("concat( concat('(', count(rf.tb_financeiro_fin_id), ')'), ' - ', {$this->_namePairs} )"),
                                                   'id'         => $this->_primary,
                                                   'label'      =>  new Zend_Db_Expr("concat( concat('(', count(rf.tb_financeiro_fin_id), ')'), ' - ', {$this->_namePairs} )")
                                                  )
               )
        ->joinLeft(array('rf'=>'rel_sacado_financeiro'), 'te.id = rf.empresas_id', null)
        ->order($ordem ? $ordem : $valor);

        if( is_numeric( $limit) ){
            $select->limit( $limit );
        } else {
            $select->limit(1000);
        }

        if($where){
            if (is_array($where)){
                foreach ($where as $key => $value){
                    $select->where($key, $value);
                }
            }else{
                $select->where($where);
            }
        }
        $select->where($this->_namePairs.' like "%'.$term.'%"')
                ->where('tif_id in(?)', array(1, 4, 5))
               ->group('te.id');
        return $this->_db->fetchAll($select);

    }
}