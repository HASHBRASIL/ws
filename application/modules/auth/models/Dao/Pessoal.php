<?php
class Auth_Model_Dao_Pessoal extends App_Model_Dao_Abstract
{
    protected $_name         = "tb_pessoal";
    protected $_primary      = "pes_id";
    protected $_namePairs    = 'pes_nome';

    protected $_rowClass = 'Auth_Model_Vo_Pessoal';
    protected $_dependentTables = array('Processo_Model_Dao_Processo');
        
    /**
     * @desc busca todos os dados da pessoa pelo cpf
     * @param int $cpf
     * @return array fetchRow
     */
    public function findPessoaByCpf($cpf)
    {
        if( is_numeric($cpf)){
            $select = $this->_db->select()->from(array('tp' => $this->_name) )
                        ->joinInner(array('tu'=>'tb_usuarios'), 'tu.pes_id = tp.pes_id', array('usu_id'))
                        ->where('pes_cpf_cnpj = ?', $cpf);
            return $this->_db->fetchRow($select, null, Zend_Db::FETCH_OBJ);
        }
    }
}