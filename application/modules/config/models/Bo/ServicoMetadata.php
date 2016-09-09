<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Bo_ServicoMetadata extends App_Model_Bo_Abstract
{
    const METANOME_FILTRO = 'ws_filtro';
    const METANOME_TARGET = 'ws_target';

	/**
	 * @var Config_Model_Dao_ServicoMetadata
	 */
	protected $_dao;

	/**
	 * @var integer
	 */
	public function __construct()
	{
		$this->_dao = new Config_Model_Dao_ServicoMetadata();
		parent::__construct();
	}

    public function getByMetanome($idservico, $metanome)
    {
        return $this->_dao->getByMetanome($idservico, $metanome);
    }

    public function getByServico($idservico)
    {
        return $this->_dao->getByServico($idservico);
    }

    public function salvarMetanome(array $dados)
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

    public function apagaMetadadosDoServico($idServico){
        return $this->_dao->apagaMetadadosDoServico($idServico);
    }

    public function apagaMetadado($idMetadado)
    {
        return $this->_dao->apagaMetadado($idMetadado);
    }
}
