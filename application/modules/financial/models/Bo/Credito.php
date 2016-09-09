<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  04/07/2013
 */
class Financial_Model_Bo_Credito extends App_Model_Bo_Abstract
{
    /**
     * @var Financial_Model_Dao_Credito
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Financial_Model_Dao_Credito();
        parent::__construct();
    }
    
	protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
    	if(empty($object->empresas_id)){
    		App_Validate_MessageBroker::addErrorMessage('O campo de empresa está vazio.');
    		return false;
    	}
    	if(empty($object->consultado_por)){
    		App_Validate_MessageBroker::addErrorMessage('O campo consultado por está vazio.');
    		return false;
    	}
   		if(empty($object->analise_risco)){
    		App_Validate_MessageBroker::addErrorMessage('O campo análise de risco está vazio.');
    		return false;
    	}

    	return true;
    }
    
	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
    	if ($object->limite_credito){
    		$object->limite_credito = $this->_formatDecimal($object->limite_credito);
    	}
    	if ($object->data_consulta_serasa != ""){
    		$fin_vencimento = new Zend_Date($object->data_consulta_serasa);
    		$object->data_consulta_serasa = $fin_vencimento->toString('yyyy/MM/dd');
    	}
    	
    }
    
}