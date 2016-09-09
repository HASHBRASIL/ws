<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  19/06/2013
 */
class Empresa_Model_Bo_GrupoOperacoes extends App_Model_Bo_Abstract
{
    /**
     * @var Empresa_Model_Bo_GrupoOperacoes
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Empresa_Model_Dao_GrupoOperacoes();
        parent::__construct();
        $this->_hasWorkspace = true;
        $this->_getRegistersWithoutWorkspace = false;
    }

    public function getPairs($ativo = true, $chave = null, $valor = null,
    		$ordem = null, $limit = null )
    {
    	$where = null;
    	if($ativo){
    		$where = array("ope_ativo = ?" => App_Model_Dao_Abstract::ATIVO);
    	}


//    	if ($this->_hasWorkspace) {
//
//    	    $workspaceSession = new Zend_Session_Namespace('workspace');
//
//    	    if (!$workspaceSession->id_workspace){
//    	        $array = array();
//    	        return $array ;
//    	    }
//
//    	    $mountWhere = array();
//
//
//    	    if ($workspaceSession->free_access != true){
//
//
//    	        if ($this->_getRegistersWithoutWorkspace){
//
//    	            $mountWhere = $mountWhere + array("id_workspace IS NULL or id_workspace = {$workspaceSession->id_workspace} " => "");
//    	        }else{
//
//    	            $mountWhere = array("id_workspace = ?" => $workspaceSession->id_workspace);
//    	        }
//
//    	        $where = $where + $mountWhere ;
//    	    }

//    	}


        if ($this->_hasWorkspace) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            $mountWhere = array("id_grupo is null or id_grupo = ?" => $identity->grupo['id']);
            $where = $where + $mountWhere;
        }



    	return $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $object->ope_telefone1 = str_replace(array('(', ')', '-', ' ', '/'), '', $object->ope_telefone1);
        $object->ope_telefone2 = str_replace(array('(', ')', '-', ' ', '/'), '', $object->ope_telefone2);
        $object->ope_telefone3 = str_replace(array('(', ')', '-', ' ', '/'), '', $object->ope_telefone3);
        $object->ope_cpf_cnpj = str_replace(array('(', ')', '-', ' ', '/'), '', $object->ope_cpf_cnpj);

    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {


    if(empty($object->ope_nome)){
    		App_Validate_MessageBroker::addErrorMessage('O campo de nome está vazio.');
    		return false;
    }
    if(empty($object->empresas_grupo_id)){
    	    App_Validate_MessageBroker::addErrorMessage('O campo emrpesas grupo está vazio.');
    	    return false;
    }
   if(empty($object->empresas_grupo_id)){
    	    App_Validate_MessageBroker::addErrorMessage('O campo emrpesas grupo está vazio.');
    	    return false;
   }



        return true;
    }
	 public function getSelect(){
	      return $this->_dao->getSelectGrupoEmpresas();
	 }

	 public function getListGrupoWithAgrupadorAndWorkspacePerTransacao($ope_id = null, $workspace = null){

	 	return $this->_dao->getListGrupoWithAgrupadorAndWorkspacePerTransacao($ope_id , $workspace);
	 }

	 public function getListGrupoWithFinanceiroAndWorkspacePerTicket($ope_id = null, $workspace = null){

	 	return $this->_dao->getListGrupoWithFinanceiroAndWorkspacePerTicket($ope_id , $workspace);
	 }





}