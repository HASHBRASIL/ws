<?php
class Sis_Model_dao_EmpresaGrupo extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_empresas_grupo";
    protected $_primary       = "id";
    protected $_namePairs     = "fantasia";


    public function getAutocomplete($term, $chave = null, $valor = null, $where = null, $ordem = null, $limit = null){
        if(empty($chave)){
            if(is_array($this->_primary)){
                $chave = $this->_primary[1];
            }else{
                $chave = $this->_primary;
            }
        }

        if(empty($valor)){
            $valor = $this->_namePairs;
        }

        $select = $this->_db->select()
        ->from($this->_name, array('value' => $valor,'id'=>$chave,
                                   'label' => $valor,
                                   'interno' => new Zend_Db_Expr('if(true, 1,0)'))
                                  )
        ->order($ordem ? $ordem : $valor);

        if( is_numeric( $limit) ){
            $select->limit( $limit );
        }else {
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
        $select->where($valor.' like "%'.$term.'%"');

        return $this->_db->fetchAll($select);

    }
}