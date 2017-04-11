<?php

class Config_Model_Bo_TipoInformacao extends App_Model_Bo_Abstract
{
    /**
     * Identificação do tipo de informação e-mail.
     */
    const META_EMAIL = 'EMAIL';
    const META_PRODUTOINICIAL = 'PRODUTOINICIAL';
    const META_AVATAR = 'AVATAR';
    /**
     * Identificação do tipo de informação número do telefone.
     */
    const META_NUMTEL = 'NUMTEL';
    /**
     * Identificação do tipo de informação De acordo com termo de serviço
     */
    const META_CONCORDATSERVICO = 'CONCORDATSERVICO';
    /**
     * Identificação do tipo de informação De acordo com termo de privacidade
     */
    const META_CONCORDATPRIVACIDADE = 'CONCORDATPRIVACIDADE';
    /**
     * Identificação do tipo de informação de Lembrete de Senha
     */
    const META_LEMBRETESENHA = 'LEMBRETESENHA';

    /**
     * @var Config_Model_Dao_Servico
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Config_Model_Dao_TipoInformacao();
        parent::__construct();
    }

    public function getById($id) {
        return $this->_dao->getById($id);
    }

    public function getByMetanome($metanome) {
        return $this->_dao->getByMetanome($metanome);
    }

    public function getTpInformacaoByPerfisByPessoaByGrupo($perfis, $idPessoa, $grupo, $check=false) {
        return $this->_dao->getTpInformacaoByPerfisByPessoaByGrupo($perfis, $idPessoa, $grupo, $check=false);
    }

}