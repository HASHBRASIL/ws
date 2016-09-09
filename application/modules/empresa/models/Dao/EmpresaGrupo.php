<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Empresa_Model_Dao_EmpresaGrupo extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_empresa";
    protected $_primary       = "id";
    protected $_namePairs     = "nome_razao";

    protected $_dependentTables = array('Material_Model_Dao_Nfe', 'Material_Model_Dao_Protocolo', 'Processo_Model_Dao_Processo','Financial_Model_Dao_Financial');

    protected $_rowClass = 'Empresa_Model_Vo_EmpresaGrupo';

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