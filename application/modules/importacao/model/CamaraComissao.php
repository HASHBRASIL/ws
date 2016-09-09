<?php

/**
 * @author Carlos Alberto Brasil Guimarães Júnior
 * @version 1.0
 */

class CamaraComissao extends Zend_Db_Table_Abstract {
    
    protected $_cols = array (
                    'id_comissao',
                    'id_status',
                    'tx_nome',
                    'tx_url',
                    'dt_check',
                    'dt_created',
                    'tx_sigla'
    );

    protected $_name = 'hash.ing_camara_comissao';
    protected $_primary = 'id_comissao';

    public function getComisaoBySigla($tx_sigla)
    {
        $busca = $this->select ()->where ( 'tx_sigla = ?', $tx_sigla );

        $all = $this->fetchAll ( $busca );

        if (empty ( $all [0] )) {
                return false;
        } else {
            return $all [0];
        }
    }

    public function addComissao($dados)
    {
        return $this->insert ( $dados );
    }

    public function updComissao($dados)
    {
        $condicao = $this->getAdapter ()->quoteInto ( 'id_comissao = ?', $dados ['id_comissao'] );
        unset ( $dados ['id_comissao'] );
        if ($this->update ( $dados, $condicao ) >= 0) {
                return true;
        } else {
                return false;
        }
    }

    public function getComissao($id_comissao)
    {
        $busca = $this->select ()->where ( 'id_comissao = ?', $id_comissao );

        $all = $this->fetchAll ( $busca );

        if (empty ( $all [0] )) {
                return false;
        } else {
                return $all [0];
        }
    }
    
    public function listaComissoes()
    {
        $busca = $this->select ();
        return $this->fetchAll ( $busca );
    }

    public function lastInsertId()
    {
        $primaria = 'id_evento';
        $query = $this->select ()->order ( $primaria . ' DESC' )->limit ( 1, 0 );

        $resultado = $this->fetchAll ( $query );

        if (isset ( $resultado [0] [$primaria] )) {
                return $resultado [0] [$primaria];
        } else {
                return 1;
        }
    }
}