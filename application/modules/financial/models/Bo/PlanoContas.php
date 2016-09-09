<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Bo_PlanoContas extends App_Model_Bo_Abstract
{
    /**
     * @var Financial_Model_Dao_PlanoContas
     */
    protected $_dao;

    public $fields =  array(
//        'plc_id' => 'Código',
        'plc_descricao' => 'Descricão',
        'plc_id_pai' => 'Plano de Conta Pai',
        'plc_oculta' => 'Oculto',
        'plc_transferencia' => 'Transferência',
        'plc_resultado' => 'Resultado',
        'plc_contabil' => 'Contábil',
        'nome' => 'Time');

    /**
     * @var integer
     */
    public function __construct()
    {
    	$this->_grupoVinculo = true;
    	$this->_getRegistersWithoutWorkspace = true;
        $this->_dao = new Financial_Model_Dao_PlanoContas();
        parent::__construct();
    }

    public function getPairsPerType($type = null)
    {
    	$planoContasList = $this->_dao->getListPlanoContas($type);
    	$planoContas = array();
    	foreach ($planoContasList as $key => $planoConta) {
    		$planoContas[$planoConta->plc_id] = $planoConta->plc_cod_contabil." ".$planoConta->plc_descricao;
    	}

    	return $planoContas;
    }

    public function getPairs($ativo = true, $chave = null, $valor = null,
                            $ordem = null, $limit = null )
    {
//    	$workspaceSession = new Zend_Session_Namespace('workspace');
//        $planoContasList = $this->find("id_workspace = {$workspaceSession->id_workspace} or id_workspace IS NULL");
//
//        if ($workspaceSession->free_access){
//
//        	$planoContasList = $this->find();
//
//        }else{
//
//        	$planoContasList = $this->find("id_workspace = {$workspaceSession->id_workspace} or id_workspace IS NULL");
//        }

        $identity = Zend_Auth::getInstance()->getIdentity();
        $planoContasList = $this->find(array("id_grupo = ?" => $identity->time['id']));

//        $planoContasList = $this->find("id_workspace = {$workspaceSession->id_workspace} or id_workspace IS NULL");


        $planoContas = array();
        foreach ($planoContasList as $key => $planoConta) {
        	$planoContas[$planoConta->plc_id] = $planoConta->plc_cod_contabil." ".$planoConta->plc_descricao;
        }
        asort($planoContas);
        return $planoContas;

    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
    	if(empty($object->plc_descricao)){
    		App_Validate_MessageBroker::addErrorMessage('O campo de descrição está vazio.');
    		return false;
    	}
    	if(empty($object->grc_id)){
    		App_Validate_MessageBroker::addErrorMessage('Selecione o grupo de contas.');
    		return false;
    	}

    	return true;
    }

     public function getListPlanoWithAgrupadorAndWorkspacePerTransacao($plcId = null ,$workspace = null){

     	return $this->_dao->getListPlanoWithAgrupadorAndWorkspacePerTransacao($plcId, $workspace);

     }

     public function getListPlanoWithAgrupadorAndWorkspacePerTicket($plcId = null ,$workspace = null){

     	return $this->_dao->getListPlanoWithAgrupadorAndWorkspacePerTicket($plcId, $workspace);

     }
}