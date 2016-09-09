<?php
class Relatorio_Model_Bo_Empresa extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_Empresa
     */
    protected $_dao;
    public function __construct()
    {
        $this->_dao = new Relatorio_Model_Dao_Empresa();
        parent::__construct();

    }

        public function getAutocomplete($term, $ativo = true, $chave = null, $valor = null,
            $ordem = null, $limit = null )
    {
        $fornecedorBo = new Sis_Model_Bo_Fornecedor();
        $grupoBo      = new Sis_Model_Bo_EmpresaGrupo();

        $fornecedor   = $fornecedorBo->getAutocomplete($term, $ativo, $chave, $valor, $ordem, $limit);
        $grupo        = $grupoBo->getAutocomplete($term, $ativo, $chave, $valor, $ordem, $limit);

        $empresa = $fornecedor + $grupo;

        return $empresa;
    }

    public function getPairsEmpresa()
    {

        $registros = $this->_dao->getPairsEmpresa();

        $array = array();
        foreach ($registros as $key => $var){
              $array[$key] = $key;
        }
        return $array;
    }

    public function getProId()
    {
        return $this->_dao->getProId();
    }

}