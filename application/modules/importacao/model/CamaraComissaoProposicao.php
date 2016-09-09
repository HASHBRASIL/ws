<?php

/**
 * @author Carlos Alberto Brasil Guimarães Júnior
 * @version 1.0
 */

class CamaraComissaoProposicao extends Zend_Db_Table_Abstract {
    
    protected $_cols = array (
                    'id_comissao_proposicao',
                    'id_comissao',
                    'id_proposicao',
                    'dt_created'
    );

    protected $_name = 'hash.ing_comissao_proposicao';
    protected $_primary = 'id_comissao_proposicao';

    public function getProposicaoByCodigo($tx_sigla)
    {
        $busca = $this->select ()->where ( 'tx_sigla = ?', $tx_sigla );

        $all = $this->fetchAll ( $busca );

        if (empty ( $all [0] )) {
                return false;
        } else {
            return $all [0];
        }
    }

    public function addProposicao($dados)
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