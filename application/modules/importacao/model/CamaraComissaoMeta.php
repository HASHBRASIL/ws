<?php

/**
 * @author Carlos Alberto Brasil Guimarães Júnior
 * @version 1.0
 */

class CamaraComissaoMeta extends Zend_Db_Table_Abstract{
    protected $_cols = array (
                    'id_meta',
                    'tx_comissao',
                    'tx_meta',
                    'tx_value'
    );

    protected $_name = 'hash.ing_camara_comissao_meta';
    protected $_primary = 'id_meta';

    public function addMeta($dados)
    {
        return $this->insert ( $dados );
    }
    
    public function listaComissaoMetaByMeta($meta)
    {
        echo $busca = $this  ->select ($this->_name)
                        ->setIntegrityCheck ( false )
                        ->join ( array('cc' => 'hash.ing_camara_comissao'), $this->_name.'.tx_comissao = cc.tx_sigla', array ('*') )
                        ->where ( 'tx_meta = ?', $meta )
                        ->order ( 'id_comissao DESC' );
exit;
        return $all = $this->fetchAll ( $busca );
    }
    
    public function listaComissoes()
    {
        $busca = $this->select ();
        return $this->fetchAll ( $busca );
    }
}