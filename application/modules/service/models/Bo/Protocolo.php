<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/05/2013
 */
class Service_Model_Bo_Protocolo extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_Protocolo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_Protocolo();
        parent::__construct();
    }

    public function buscaProtocolo(){
    	return $this->_dao->buscaProtocolo();
    }
    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->id_tp_entrada)){
            App_Validate_MessageBroker::addErrorMessage("Selecione um tipo de entrada");
            return false;
        }

        if(empty($object->id_empresa_fornecedor)){
            App_Validate_MessageBroker::addErrorMessage("Selecione quem está entregando o produto");
            return false;
        }

        if(empty($object->id_empresa_receptora)){
            App_Validate_MessageBroker::addErrorMessage("Selecione quem está recebendo o produto");
            return false;
        }
        return true;
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(!empty($object->dt_entrada)){
            $dt_entrada         = new Zend_Date($object->dt_entrada);
            $object->dt_entrada = $dt_entrada->toString('yyyy-MM-dd');
        }

    }

    public function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $this->_saveControle($object->id_protocolo, $request['controle_fornecedor'], Sis_Model_Bo_TipoControle::FORNECEDOR);
        $this->_saveControle($object->id_protocolo, $request['controle_receptora'], Sis_Model_Bo_TipoControle::RECEPTOR);
    }

    private function _saveControle($idProtocolo, $arrNum, $tpControle)
    {
        if(count($arrNum) && !empty($arrNum)){
            $controleBo = new Sis_Model_Bo_Controle();
            $controleBo->deleteByProtocoloServico($idProtocolo, $tpControle);
            foreach ($arrNum as $num){
                if(!empty($num)){
                    $controle                      = $controleBo->get();
                    $controle->id_gs_protocolo     = $idProtocolo;
                    $controle->id_tp_controle      = $tpControle;
                    $controle->codigo              = $num;
                    $controle->save();
                }
            }
        }
    }
}