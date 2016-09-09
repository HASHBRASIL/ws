<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  25/06/2013
 */
class Processo_Model_Dao_Status extends App_Model_Dao_Abstract
{
    protected $_name      = "pro_tb_status";
    protected $_primary   = "sta_id";
    protected $_namePairs = "sta_descricao";

    protected $_rowClass = 'Processo_Model_Vo_Status';


    protected $_dependentTables = array('Processo_Model_Dao_Processo');


    protected $_referenceMap    = array(
            'Workspace' => array(
                    'columns'           => 'id_workspace',
                    'refTableClass'     => 'Auth_Model_Dao_Workspace',
                    'refColumns'        => 'id_workspace'
            )
    );

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

    	$select = $this->_db
    	->select()
    	->from($this->_name, array('value' => $valor,'id'=>$chave, 'sta_numero' => 'sta_numero'))
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
    	$select->where($valor.' like "%'.$term.'%"')
    	->orWhere('sta_numero like "%'.$term.'%"');

    	return $this->_db->fetchAll($select);

    }

    public function fetchPairs($chave = null, $valor = null, $where = null, $ordem = null, $limit = null)
    {
        if(empty($chave)){
            if(is_array($this->_primary)){
                $chave = $this->_primary[1];
            }else{
                $chave = $this->_primary;
            }
        }

        if(empty($valor)){
            $valor = "sta_numero::text || ' - ' || {$this->_namePairs}";
        }

        $select = $this->_db
        ->select()
        ->from($this->_name, array($chave, new Zend_Db_Expr($valor)))
        ->order($ordem ? $ordem : new Zend_Db_Expr($valor));

        if( is_numeric( $limit) ){
            $select->limit( $limit );
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

        return $this->_db->fetchPairs($select);
    }

}