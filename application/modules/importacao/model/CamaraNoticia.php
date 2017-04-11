<?php

/**
 * @author Carlos Alberto Brasil Guimarães Júnior
 * @version 1.0
 */

class CamaraNoticia extends Zend_Db_Table_Abstract {
	protected $_cols = array (
			'id_noticia',
			'tx_url',
			'tx_title',
			'tx_postador',
			'tx_conteudo',
			'editorias',
                        'dt_created',
			'tx_credito',
			'tx_proposicao',
			'dt_pub'
	);
        
	protected $_name = 'hash.ing_camara_noticia';
	protected $_primary = 'id_noticia';
        /*
	private $sessao = null;
	public function init() {
		$this->sessao = new Zend_Session_Namespace ( 'sessao' );
	}*/
        
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
        
	public function addNoticia($dados) {
		return $this->insert ( $dados );
	}
        
	public function updNoticia($dados) {
		
		$condicao = $this->getAdapter ()->quoteInto ( 'id_noticia = ?', $dados ['id_noticia'] );
		unset ( $dados ['id_noticia'] );
		if ($this->update ( $dados, $condicao ) >= 0) {
			return true;
		} else {
			return false;
		}
	}
        
        public function getNoticia($id_noticia) {
		$busca = $this->select ()->where ( 'id_noticia = ?', $id_noticia );
		
		$all = $this->fetchAll ( $busca );
		
		if (empty ( $all [0] )) {
			return false;
		} else {
			return $all [0];
		}
	}
        
	public function lastInsertId() {
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