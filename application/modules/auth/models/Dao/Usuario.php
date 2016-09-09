<?php
class Auth_Model_Dao_Usuario extends App_Model_Dao_Abstract
{

    protected $_name         = "tb_usuario";
    protected $_primary      = "id";
    protected $_namePairs	 = "nomeusuario";

    protected $_rowClass = 'Auth_Model_Vo_Usuario';

//    protected $_referenceMap    = array(
//    		'Empresa' => array(
//    				'columns'           => 'id_empresa',
//    				'refTableClass'     => 'Empresa_Model_Dao_Empresa',
//    				'refColumns'        => 'id'
//    		)
//
//    );


//    /**
//     * @param string $chave o campo que será usado como chave.
//     * Caso String vazia ou null, pega a chave primaria definida no atributo $_primary
//     * @param string $valor o campo que deve ser retornado no valor
//     * Caso String vazia ou null, pega a chave primaria definida no atributo $_namePairs
//     * @param string $where
//     * @param string $ordem
//     * @param string $limit
//     * @return Ambigous <multitype:, multitype:mixed >
//     */
//    public function fetchPairs($chave = null, $valor = null, $where = null, $ordem = null, $limit = null)
//    {
//    	if(empty($chave)){
//    		if(is_array($this->_primary)){
//    			$chave = $this->_primary[1];
//    		}else{
//    			$chave = $this->_primary;
//    		}
//    	}
//
//    	if(empty($valor)){
//    		$valor = $this->_namePairs;
//    	}
//
//    	$select = $this->_db
//    	->select()
//    	->from(array('tu' => $this->_name), array($chave))
//    	->joinInner(array('te' => 'tb_empresas'), 'tu.id_empresa = te.id', $valor)
//    	->order($ordem ? 'tu.'.$ordem : $valor);
//
//    	if( is_numeric( $limit) ){
//    		$select->limit( $limit );
//    	}
//
//    	if($where){
//    		if (is_array($where)){
//    			foreach ($where as $key => $value){
//    				$select->where('tu.'.$key, $value);
//    			}
//    		}else{
//    			$select->where('tu.'.$where);
//    		}
//    	}
//    	return $this->_db->fetchPairs($select);
//    }

    public function update(array $dados, $idPessoa)
    {
        $condicao = $this->getAdapter()->quoteInto('id = ?', $idPessoa);
        return parent::update($dados, $condicao);
    }

    public function validaTicketSenha($ticketSenha)
    {
        $select = $this->select()
            ->where('ticket_senha = ?', $ticketSenha);
        return $this->fetchAll($select)->toArray();
    }

    public function getUserByNomeUsuario($nome) {
        $select = $this->select()->from(array($this->_name), array('id','nomeusuario'))
            ->where('nomeusuario = ?', $nome);
            $ret = $this->fetchAll($select);
        return $ret;
    }
}