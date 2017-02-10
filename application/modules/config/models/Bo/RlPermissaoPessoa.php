<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Bo_RlPermissaoPessoa extends App_Model_Bo_Abstract
{
	/**
	 * @var Config_Model_Dao_RlPermissaoPessoa
	 */
	protected $_dao;

	/**
	 * @var integer
	 */
	public function __construct()
	{
		$this->_dao = new Config_Model_Dao_RlPermissaoPessoa();
		parent::__construct();
	}

    public function atribuiPermissao($idPessoa, $idGrupo, $idServico, $dataExpiracao = null, $recursivo = false)
    {

//        $permissao = $this->_dao->findPermissao($idPessoa, $idGrupo, $idServico);
//        if ($permissao) {
//            return $permissao->id;
//        }

        $id = UUID::v4();
        $this->_dao->insert([
            'id' => $id,
            'id_pessoa' => $idPessoa,
            'id_grupo' => $idGrupo,
            'id_servico' => $idServico,
            'dt_expiracao' => $dataExpiracao
        ]);

        if ($recursivo) {
            $servicoBo = new Config_Model_Bo_Servico();

            foreach ($servicoBo->getServicosFilhos($idServico) as $servico) {
                $this->atribuiPermissao(
                    $idPessoa,
                    $idGrupo,
                    $servico['id'],
                    $dataExpiracao,
                    $recursivo
                );
            }
        }

        return $id;
    }

    public function apagaPermissoesDoServico($idServico){
        $this->_dao->apagaPermissoesDoServico($idServico);
    }
}
