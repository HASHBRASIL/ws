<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Content_Model_Bo_TpItemBiblioteca extends App_Model_Bo_Abstract
{
    /**
     * Metanome do tip correspondente ao cracha.
     */
    const META_CRACHA = 'TPCRACHACOMITE';

    const META_NOMEGUERRA = 'nomeGuerra';
    const META_NUMERO = 'numero';
    const META_CPF = 'cpf';
    const META_EMAIL = 'email';
    const META_NOME = 'nome';
    const META_IDPESSOA = 'idpessoa';

    /**
     * @var Content_Model_Dao_TpItemBiblioteca
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Content_Model_Dao_TpItemBiblioteca();
        parent::__construct();
    }

    public function getHeaderGrid($id_tib) {
        $data = $this->_dao->getHeaderGrid($id_tib);
        return $data;
    }

    public function getTipoConteudo($time) {
        $data = $this->_dao->getTipoConteudo($time);
        return $data;
    }

    public function getTipoById($id_tib) {
        $data = $this->_dao->getTipoById($id_tib);
        return $data;
    }

    public function getTipoByIdSelect($idTib) {
        $rowset = $this->_dao->fetchAll(array( 'id_tib_pai = ?' => $idTib ));
        return $rowset;
    }

    public function getTipoByMetanome($metanome)
    {
        return $this->_dao->fetchAll(array('metanome = ?'=>$metanome));
    }

    public function getTipoByIdPai($idPai)
    {
        return $this->_dao->fetchAll(array('id_tib_pai = ?' => $idPai));
    }

    public function getTipoByIdPaiByMetanome($idPai,$metanome)
    {
        return $this->_dao->fetchAll(array('metanome = ?' => $metanome, 'id_tib_pai = ?' => $idPai));
    }
}