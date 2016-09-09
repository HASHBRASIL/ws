<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/05/2013
 */
class Material_Model_Bo_Protocolo extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Protocolo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Protocolo();
        parent::__construct();
    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->id_tp_protocolo)){
            App_Validate_MessageBroker::addErrorMessage("Selecione um tipo de protocolo.");
            return false;
        }

        if(empty($object->id_empresa_fornecedor)){
            App_Validate_MessageBroker::addErrorMessage("Selecione quem está entregando o produto.");
            return false;
        }

        if(empty($object->id_empresa_receptora)){
            App_Validate_MessageBroker::addErrorMessage("Selecione quem está recebendo o produto.");
            return false;
        }

        $objRequest = new Zend_Controller_Request_Http();
        $request = $objRequest->getParams();

        if($object->id_tp_transportador == Material_Model_Bo_TipoTransportador::EMPRESA && empty($request['id_transp_empresa'])){
            App_Validate_MessageBroker::addErrorMessage("Selecione uma empresa tranportadora.");
            return false;
        }

        if($object->id_tp_transportador == Material_Model_Bo_TipoTransportador::FUNCIONARIO && empty($object->id_funcionario_transportador)){
            App_Validate_MessageBroker::addErrorMessage("Selecione um funcionário responsável pelo transporte.");
            return false;
        }
        return true;
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(!empty($request['id_transp_empresa'])){
            $transpBo      = new Material_Model_Bo_Transportador();
            $transportador = $transpBo->get($request['id_transportador']);
            $transpBo->saveFromRequest($request, $transportador);
            $object->id_transportador = $transportador->id_transportador;
        }
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
            $controleBo->deleteByProtocoloMaterial($idProtocolo, $tpControle);
            foreach ($arrNum as $num){
                if(!empty($num)){
                    $controle                      = $controleBo->get();
                    $controle->id_gm_protocolo     = $idProtocolo;
                    $controle->id_tp_controle      = $tpControle;
                    $controle->codigo              = $num;
                    $controle->save();
                }
            }
        }
    }
}