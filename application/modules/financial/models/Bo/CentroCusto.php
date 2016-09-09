<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Bo_CentroCusto extends App_Model_Bo_Abstract
{
    /**
     * @var Financial_Model_Dao_CentroCusto
     */
    protected $_dao;

    public $fields = array(
        'cec_codigo' => 'Codigo',
        'cec_descricao' => 'Descrição',
        'cec_descricao_pai' => 'Centro Custo Pai',
        'cec_operacional' => 'Operacional',
        'cec_oculta' => 'Oculta Registro',
        'nome' => 'Time'
    );


    /**
     * @var integer
     */
    public function __construct()
    {
    	$this->_grupoVinculo = true;
        $this->_dao = new Financial_Model_Dao_CentroCusto();
        parent::__construct();
    }

    public function getListCentroCustoWithFinanceiroAndWorkspacePerTransacao($cecId = null, $workspace = null){

    	return $this->_dao->getListCentroCustoWithFinanceiroAndWorkspacePerTransacao($cecId , $workspace);
    }

    public function getListCentroCustoWithFinanceiroAndWorkspacePerTicket($cecId = null, $workspace = null){

    	return $this->_dao->getListCentroCustoWithFinanceiroAndWorkspacePerTicket($cecId , $workspace);
    }


}