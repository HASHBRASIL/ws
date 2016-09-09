<?php

/**
 * @author Carlos Alberto Brasil Guimarães Júnior
 * @version 1.0
 */

class ComissaoNoticia extends Zend_Db_Table_Abstract {
    
    protected $_cols = array (
                    'id_noticia',
                    'tx_comissao',
                    'tx_title',
                    'tx_lead',
                    'tx_conteudo',
                    'tx_url',
                    'tx_credito',
                    'tx_proposicao',
                    'dt_pub',
                    'dt_created'
    );

    protected $_name = 'hash.ing_camara_comissao_noticia';
    
    protected $_primary = 'id_noticia';

    public function addNoticia($dados)
    {
        return $this->insert ( $dados );
    }

    public function getNoticiaByUrl($url)
    {
        $busca = $this->select ()->where ( 'tx_url = ?', $url );

        $all = $this->fetchAll ( $busca );

        if (empty ( $all [0] )) {
                return false;
        } else {
                return $all [0];
        }
    }
        
    public function getUltimaPaginaNoticia($tx_comissao)
    {
        $busca = $this->select ()->where ( 'tx_comissao = ?', $tx_comissao );

        $all = $this->fetchAll ( $busca );

        if (empty ( $all [0] )) {
                return false;
        } else {
                return $all [0];
        }
    }
    
    public function getNoticia($id_noticia)
    {
        $busca = $this->select ()->where ( 'id_noticia = ?', $id_noticia );

        $all = $this->fetchAll ( $busca );

        if (empty ( $all [0] )) {
                return false;
        } else {
                return $all [0];
        }
    }
}