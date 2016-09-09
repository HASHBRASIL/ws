<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  25/06/2013
 */
class Processo_Model_Bo_Status extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_Status
     */
    protected $_dao;

    const AGPRESS_AGUARDANDO_ARTE = 5;

    public function __construct()
    {
        $this->_hasWorkspace = true;
        $this->_getRegistersWithoutWorkspace = true;
        $this->_dao = new Processo_Model_Dao_Status();
        parent::__construct();
    }

    public function getPairs($ativo = true, $chave = null, $valor = null,$ordem = null, $limit = null)
    {

        $where = array();


//        if ($this->_hasWorkspace) {
//
//            $workspaceSession = new Zend_Session_Namespace('workspace');
//
//            if (!$workspaceSession->id_workspace){
//                $array = array();
//                return $array ;
//            }
//
//            if ($workspaceSession->free_access != true){
//                if ($this->_getRegistersWithoutWorkspace){
//                    $where = array("id_workspace IS NULL or id_workspace = {$workspaceSession->id_workspace} " => null);
//                }else{
//                    $where = array("id_workspace = ?" => $workspaceSession->id_workspace);
//                }
//
//            }
//        }

        return $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
    }

    public function getAutocomplete($term, $ativo = true, $chave = null, $valor = null,
    		$ordem = null, $limit = null )
    {
    	$where = null;

    	$statusList =  $this->_dao->getAutocomplete($term, $chave, $valor, $where, $ordem, $limit);

    	foreach ($statusList as $key => $status){

    		$statusList[$key]['value'] = $status['sta_numero']." - ".$status['value'];

    	}

    	return $statusList;
    }

    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $object->sta_finalizado = isset($request['sta_finalizado']) ? $request['sta_finalizado']: 0;
    }
}