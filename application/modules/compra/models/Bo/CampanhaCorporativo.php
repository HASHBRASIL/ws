<?php
/**
 * @author Vinicius Leônidas
 * @since 21/10/2013
 */
class Compra_Model_Bo_CampanhaCorporativo extends App_Model_Bo_Abstract
{
	/**
	 * @var Compra_Model_Dao_CampanhaCorporativa
	 */
	protected $_dao;

	public function __construct()
	{
		$this->_dao = new Compra_Model_Dao_CampanhaCorporativo();
		parent::__construct();
	}

	public function getCampanhasCorporativa(){
		return $this->_dao->getCampanhasCorporativa();
	}
	
	public function saveFromRequestByCampanha( $request)
	{
	    $resultValidationRequest = $this->_validarByRequest($request);
	    if(!$resultValidationRequest){
	        throw new App_Validate_Exception();
	    }
	    foreach ($request['consultor'] as $id_corporativo){
	        //populando o objeto
	        $object                      = $this->get();
	        $object->id_corporativa      = $id_corporativo;
	        $object->id_criacao_usuario  = Zend_Auth::getInstance()->getIdentity()->usu_id;
	        $object->dt_criacao          = date('Y-m-d H:i:s');
	        $object->id_campanha         = $request['id_campanha'];

	        $resultValidation = $this->_validar($object);
	        if(!$resultValidation){
	            throw new App_Validate_Exception();
	        }
	        try {
	            $this->_preSave($object, $request);
	            $object->save();
	            $this->_postSave($object, $request);
	        } catch (Exception $e) {
	            App_Validate_MessageBroker::addErrorMessage("O sistema se encontra fora do ar no momento. Caso persista entre em contato com o Administrador.".$e->getMessage());
	            throw new App_Validate_Exception();
	        }
	    }
	}

	protected function _validarByRequest($request)
	{
	    if(count($request['consultor']) == 0){
	        App_Validate_MessageBroker::addErrorMessage('Selecione ao menos um consultor.');
	        return false;
	    }
	    if(empty($request['id_campanha'])){
	        App_Validate_MessageBroker::addErrorMessage('Escolha uma campanha.');
	        return false;
	    }
	    foreach ($request['consultor'] as $idCorporativo){
	        $criteria = array(
	                'id_corporativa = ?'     => $idCorporativo,
	                'id_campanha = ?' => $request['id_campanha'],
	                'ativo = ?'       => App_Model_Dao_Abstract::ATIVO
	        );
	        $campanhaCorporativo = $this->find($criteria);
	        if (count($campanhaCorporativo) > 0){
	            /**
	             * @todo não esquecer de colocar o nome do consultor
	             */
	            $nomeConsultor = $campanhaCorporativo->current()->getCorporativo()->nome_razao;
	            App_Validate_MessageBroker::addErrorMessage('O consultor(a) "'.$nomeConsultor.'" já existe para essa campanha.');
	            return false;
	            break;
	        }
	    }
	    return true;
	}

	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
	    $object->vl_max_compra = $this->_formatDecimal($object->vl_max_compra);
	}

}