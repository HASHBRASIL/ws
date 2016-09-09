<?php

class Config_Model_Bo_Informacao extends App_Model_Bo_Abstract
{
	/**
	 * @var Config_Model_Dao_Informacao
	 */
	protected $_dao;

	/**
	 * @var integer
	 */
	public function __construct()
	{
		$this->_dao = new Config_Model_Dao_Informacao();
		parent::__construct();
	}

	public function persiste($uuid,$idTinfo,$idPessoa,$idPai,$valor,$idCriador = null) {
        $rowPai = null;
        if($uuid){
            $rowPai = $this->_dao->find($uuid);
            if($rowPai->count()==0) {
                throw new Exception("Informação não encontrada");
            }
            $rowPai = $rowPai->current();
        } else {
            $rowPai  = $this->_dao->createRow();
            $uuid = UUID::v4();
            $rowPai->id = $uuid;
        }
        if($idPessoa){
            $rowPai->id_pessoa = $idPessoa;
        }
        if($idTinfo){
            $rowPai->id_tinfo = $idTinfo;
        }
        if($idPai){
            $rowPai->id_pai = $idPai;
        }
        if($idCriador){
            $rowPai->id_criador = $idCriador;
        }
        $rowPai->valor = $valor;
        $rowPai->save();
        return $uuid;
    }

    /**
     * Armazena as informações de "de acordo" do usuário/pessoa e os associa ao seu grupo pessoal.
     *
     * @param string $idPessoa uuid de tb_pessoa.
     * @param string $idGrupo uuid do grupo pessoal associado a $idPessoa.
     * @throws Exception
     * @todo Migrar para self::addInformacao()
     */
    public function salvaDeAcordo($idPessoa, $idGrupo)
    {
        $tpInfo = new Config_Model_Bo_TipoInformacao();

        // -- De acordo termo de serviço
        $idInfo = $this->persiste(
            null,
            current($tpInfo->getByMetanome(Config_Model_Bo_TipoInformacao::META_CONCORDATSERVICO))['id'],
            $idPessoa,
            null,
            'true',
            $idPessoa
        );
        $rlGrupoInfo = new Config_Model_Bo_RlGrupoInformacao();
        $rlGrupoInfo->relacionaGrupoInformacao($idGrupo, $idInfo, $idPessoa);

        // -- De acordo termo de privacidade
        $idInfo = $this->persiste(
            null,
            current($tpInfo->getByMetanome(Config_Model_Bo_TipoInformacao::META_CONCORDATSERVICO))['id'],
            $idPessoa,
            null,
            'true',
            $idPessoa
        );
        $rlGrupoInfo->relacionaGrupoInformacao($idGrupo, $idInfo, $idPessoa);
    }

    /**
     * Adiciona uma informação identificada por um metanome e a relaciona a uma pessoa.
     *
     * Opcionalmente, associada aquela informação a um grupo.
     *
     * @param type $idPessoa
     * @param type $metanome
     * @param type $valor
     * @param type $idGrupo
     * @param type $idPai
     * @return type
     * @throws App_Validate_Exception
     */
    public function addInformacao($idPessoa, $metanome, $valor, $idGrupo = null, $idPai = null, $idInfo = null)
    {
        $idTpInfo = current(
            (new Config_Model_Bo_TipoInformacao())->getByMetanome($metanome)
        )['id'];

        if (empty($idTpInfo)) {
            throw new App_Validate_Exception('metadata_tipo_informacao_nao_cadastrado');
        }

        $idInfo = $this->persiste($idInfo, $idTpInfo, $idPessoa, $idPai, $valor, $idPessoa);

        // -- Associando ao grupo, qdo necessário
        if (!is_null($idGrupo)) {
            (new Config_Model_Bo_RlGrupoInformacao())
                ->relacionaGrupoInformacao($idGrupo, $idInfo, $idPessoa);
        }

        return $idInfo;
    }

    /**
     * Retorna informações de uma pessoa, consultando pelo metanome da informação.
     * @param string $idPessoa UUID de tb_pessoa.
     * @param string $metanome Metanome da informação que será buscada.
     * @return mixed[]
     */
    public function getInfoPessoaByMetanome($idPessoa, $metanome)
    {
        return $this->_dao->getInfoPessoaByMetanome($idPessoa, $metanome);
    }

    public function getInfoByMetanomeEValor($metanome, $valor)
    {
        return $this->_dao->getInfoByMetanomeEValor($metanome, $valor);
    }
}
