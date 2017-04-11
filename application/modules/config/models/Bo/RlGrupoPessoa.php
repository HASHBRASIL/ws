<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Bo_RlGrupoPessoa extends App_Model_Bo_Abstract
{
    /**
     * Indica permissão de dono do canal.
     */
    const PERMISSAO_DONO = 'x';
    /**
     * Indica permissão de leitura e escrita.
     */
    const PERMISSAO_ESCRITA = 'w';
    /**
     * Indica permissão de leitura.
     */
    const PERMISSAO_LEITURA = 'r';

    /**
     * @var Config_Model_Dao_RlGrupoPessoa
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
            $this->_dao = new Config_Model_Dao_RlGrupoPessoa();
            parent::__construct();
    }

    /**
     * Relaciona uma pessoa a um grupo e, opcionalmente, adiciona um nome ao relacionamento.
     *
     * Quando for criar os grupo "pessoal" e "geral", deve-se definir o nomehash,
     * para que possam aparecer no front mobile.
     *
     * @param string $idPessoa uuid de tb_pessoa
     * @param string $idGrupo uuid do grupo
     * @param string $nomeHash nomehash do relacionamento
     */
    public function addPessoaAoGrupo($idPessoa, $idGrupo, $permissao = self::PERMISSAO_LEITURA, $nomeHash = null)
    {
        $this->_dao->insert(array(
            'id' => UUID::v4(),
            'id_pessoa' => $idPessoa,
            'id_grupo' => $idGrupo,
            'nomehash' => $nomeHash,
            'permissao' => $permissao
        ));
    }

    public function getPessoasPorGrupo ($idGrupo) {

    }

    public function getGruposPorPessoa($idPessoa) {

    }

    public function listGrupoPessoa($id_grupo = null, $id_pessoa = null)
    {
        $this->_dao->listGrupoPessoa($id_grupo, $id_pessoa);
    }

    public function getGrupoPessoalNaInstalacao($idPessoa)
    {
        return $this->_dao->getGrupoPessoalNaInstalacao($idPessoa);
    }
}
