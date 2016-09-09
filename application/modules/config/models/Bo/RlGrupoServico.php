<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Bo_RlGrupoServico extends App_Model_Bo_Abstract
{
	/**
	 * @var Config_Model_Dao_RlGrupoServico
	 */
	protected $_dao;

	/**
	 * @var integer
	 */
	public function __construct()
	{
		$this->_dao = new Config_Model_Dao_RlGrupoServico();
		parent::__construct();
	}

    public function ativaServicosDoModulo($idPessoa, $idGrupo, array $modulos = [])
    {
        foreach ($modulos as $modulo) {
            // -- Associando o servico ao grupo da pessoa
            $this->associaGrupoServico($idGrupo, $modulo['id']);
            $this->atribuiPermissaoServicosDoModulo($idPessoa, $idGrupo, $modulo['id']);
        }
    }

    /**
     * Atribui as permissões de um módulo e seus serviços a uma pessoa em um grupo.
     *
     * @param string $idPessoa UUID de tb_pessoa.
     * @param string $idGrupo UUID de tb_grupo, geralmente o grupo time.
     * @param string $idModulo UUID de um módulo em tb_servico.
     */
    public function atribuiPermissaoServicosDoModulo($idPessoa, $idGrupo, $idModulo)
    {
        $rlPermissaoPessoaBo = new Config_Model_Bo_RlPermissaoPessoa();

        // -- Permissão do módulo
        $rlPermissaoPessoaBo->atribuiPermissao(
            $idPessoa,
            $idGrupo,
            $idModulo,
            '2099-12-31',
            $recursivo = true
        );
    }

    /**
     * Faz uma nova associação entre grupo e serviço, se ela não existir.
     *
     * @param string $idGrupo UUID de um grupo
     * @param string $idServico UUID de um serviço
     * @return string
     */
    public function associaGrupoServico($idGrupo, $idServico)
    {
        $associacao = $this->_dao->findAssociacao($idGrupo, $idServico);
        if ($associacao) {
            return $associacao->id;
        }

        $id = UUID::v4();

        $this->_dao->insert([
            'id' => $id,
            'id_grupo' => $idGrupo,
            'id_servico' => $idServico
        ]);

        return $id;
    }

    public function listGrupoServico($id_grupo = null, $id_servico = null)
    {
        return $this->_dao->listGrupoServico($id_grupo, $id_servico);
    }

    public function listGrupoModulo($id_grupo = null, $id_servico = null)
    {
        return $this->_dao->listGrupoServico($id_grupo, $id_servico, $apenasModulos = true);
    }
}
