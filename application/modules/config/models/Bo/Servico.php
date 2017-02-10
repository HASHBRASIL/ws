<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Bo_Servico extends App_Model_Bo_Abstract
{
    /**
     * Metanome do serviço de gestão de pessoas.
     */
    const META_GESTAOPESSOAS = 'HASH_MODULO_GESTAO_PESSOAS';
    /**
     * Metanome do serviço de gestão de entidades.
     */
    const META_GESTAOENTIDADES = 'HASH_MODULO_GESTAO_ENTIDADES';

	/**
	 * @var Config_Model_Dao_Servico
	 */
	public $_dao;

	/**
	 * @var integer
	 */
	public function __construct()
	{
		$this->_dao = new Config_Model_Dao_Servico();
		parent::__construct();
	}

	public function getServicoByMetanome($metanome) {
		return $this->_dao->getServicoByMetanome($metanome);
	}

	public function getServicosFilhos($idPai, $recursivo = false) {
		return $this->_dao->getServicosFilhos($idPai, $recursivo);
	}

    public function getModulosObrigatorios()
    {
        return $this->getServicoByMetanome([
            self::META_GESTAOENTIDADES,
            self::META_GESTAOPESSOAS
        ]);
    }
    
    public function getServicoEmFerramentas($metanome)
    {
        return $this->_dao->getServicoEmFerramentas($metanome);
    }

    public function getServicoEmUmaArvore($id_servico, $metanome)
    {
        return $this->_dao->getServicoEmUmaArvore($id_servico, $metanome);
    }
    
    public function getModulosByGrupo($idGrupo)
    {
        return $this->_dao->getModulosByGrupo($idGrupo);
    }

    public function salvarServico(array $dados)
    {
        if (empty($dados['id'])) {

            $dados['id'] = UUID::v4();
            $this->_dao->insert($dados);
        } else {

            $condicao = $this->_dao->getAdapter()->quoteInto( 'id = ?', $dados['id']);
            $this->_dao->update($dados, $condicao);
        }

        return $dados;
    }

    public function delServico($id)
    {
        return $this->_dao->delServico($id);
    }

    public function getServico($id)
    {
        return $this->_dao->getServico($id);
    }

    public function copiarServico($servicoOrigem, $servicoPaiDestino)
    {
        $servicosCopiados = []; // -- [id_servico_original => novo_servico]
        $corMetanomes = []; // -- [metanome_original => metanome_novo]
        $servicosParaCopiar = $this->getServicosFilhos($servicoOrigem, true);

        // -- Copiando os servicos e guardando referencias de ids e metanomes criados
        foreach ($servicosParaCopiar as $servico) {
            $dados = $servico;
            unset($dados['id'], $dados['dt_criacao']);

            $dados['metanome'] = "{$servico['metanome']}_" . str_replace('-', '', UUID::v4());
            $dados['visivel'] = empty($servico['visivel'])?'f':'t';

            if ($servicoOrigem == $servico['id']) {
                $dados['nome'] = "{$dados['nome']} - cópia";
            }

            // -- atualizando listas de referencias
            $servicosCopiados[$servico['id']] = $this->salvarServico($dados);
            $corMetanomes[$servico['metanome']] = $dados['metanome'];
        }

        // -- Atualizando id_pais
        foreach ($servicosCopiados as $servCopiado) {
            $idPaiAntigo = $servCopiado['id_pai'];
            $idPaiNovo = array_key_exists($idPaiAntigo, $servicosCopiados)
                ?$servicosCopiados[$idPaiAntigo]['id']
                :$servicoPaiDestino;

            $dados = [
                'id' => $servCopiado['id'],
                'id_pai' => $idPaiNovo
            ];

            $this->salvarServico($dados);
        }

        // -- Copiando e modificando metadados
        $servicoMetaBo = new Config_Model_Bo_ServicoMetadata();
        foreach ($servicosCopiados as $idServicoOriginal => $servCopiado) {
            $metadados = $servicoMetaBo->getByServico($idServicoOriginal);

            if (empty($metadados)) {
                continue;
            }

            foreach ($metadados as $metadado) {

                unset($metadado['id'], $metadado['dt_criacao']);

                $metadado['id_servico'] = $servCopiado['id'];
                switch ($metadado['metanome']) {
                    case Config_Model_Bo_ServicoMetadata::METANOME_TARGET:
                        $metadado['valor'] = $corMetanomes[$metadado['valor']];
                        break;
                }

                $servicoMetaBo->salvarMetanome($metadado);
            }
        }
    }
    
        
    /*
     * @TODO PASSAR PARA O CMS.
     */
    public function listServicosPermitidos($id_pesssoa, $id_grupo, $params = array())
    {
        return $this->_dao->listServicosPermitidos($id_pesssoa, $id_grupo, $params);
    }
}
