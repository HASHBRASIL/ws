<?php

/**
 * @author Carlos Alberto Brasil Guimarães Júnior
 * @version 1.0
 * 
 */

class Proposicoes extends Zend_Db_Table_Abstract{
    protected $_cols = array (
                    'id_proposicao',
                    'tx_codigo',
                    'tx_autor',
                    'tx_url',
                    'tx_conteudo',
                    'nr_pagina',
                    'dt_apresentacao',
                    'dt_created'
    );

    protected $_name = 'hash.ing_camara_proposicao';
    protected $_primary = 'id_proposicao';

    public function addProposicao($dados)
    {
        return $this->insert ( $dados );
    }
    
    public function getProposicao($id_proposicao)
    {
        $busca = $this->select ()->where ( 'id_proposicao = ?', $id_proposicao );

        $all = $this->fetchAll ( $busca );

        if (empty ( $all [0] )) {
                return false;
        } else {
                return $all [0];
        }
    }
}