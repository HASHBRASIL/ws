<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  12/08/2013
 */
class Processo_Model_Bo_MaterialProcesso extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_MaterialProcesso
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_MaterialProcesso();
        parent::__construct();
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $object->quantidade    = $this->_formatDecimal($object->quantidade);
        $object->vl_unitario   = $this->_formatDecimal($object->vl_unitario);
        $object->total         = $this->_formatDecimal($object->total);

        if($object->id_tp_material == Processo_Model_Bo_TipoMaterial::PROPRIO && empty($object->id_status_material)){
            $object->id_status_material = Processo_Model_Bo_StatusMaterial::ESTIMADO;
        }elseif($object->id_tp_material == Processo_Model_Bo_TipoMaterial::PROPRIO && $object->id_status_material == Processo_Model_Bo_StatusMaterial::ESTIMADO){
            $object->id_status_material = Processo_Model_Bo_StatusMaterial::VALIDADO;
        }
    }

    protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $object->toArray();
        $historicoMaterialBo = new Processo_Model_Bo_HistoricoMaterial();
        $historicoMaterial = $historicoMaterialBo->get();
        $historicoMaterialBo->saveFromRequest($object->toArray(), $historicoMaterial);


        //Salvando o item com suas opções
        if( isset($request['id_opcao']) && count($request['id_opcao']) > 0 ){
            $materialOpcaoBo = new Processo_Model_Bo_MaterialOpcao();
            $materialOpcaoBo->deleteByMaterial($object->id_material_processo);

            foreach ($request['id_opcao'] as $idOpcao){
                $materialOpcao = $materialOpcaoBo->get();

                $materialOpcao->id_material_processo     = $object->id_material_processo;
                $materialOpcao->id_opcao                 = $idOpcao;
                $materialOpcao->save();
            }
        }
    }


    public function saveByMovimento($objectMovimento, $request)
    {
        $object = $this->get($request['id_material_processo']);
        $object->qtd_baixado = floatval($object->qtd_baixado) + floatval($objectMovimento->quantidade);
        if(bccomp($object->qtd_baixado, $object->quantidade) == 0){
            $object->id_status_material = Processo_Model_Bo_StatusMaterial::BAIXA_TOTAL;
        }else{
            $object->id_status_material = Processo_Model_Bo_StatusMaterial::BAIXA_PARCIAL;
        }
        try {
            $object->save();
            $this->_postSave($object, $request);
        } catch (Exception $e) {
            App_Validate_MessageBroker::addErrorMessage("O sistema se encontra fora do ar no momento. Caso persista entre em contato com o Administrador.".$e->getMessage());
            throw new App_Validate_Exception();
        }
    }

}