<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  13/05/2013
 */
class Material_Model_Bo_Entrega extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Entrega
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Entrega();
        parent::__construct();
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->id_empresa)){
            $empresa = Zend_Auth::getInstance()->getIdentity();
            $object->id_empresa = $empresa->id;
        }

        if(empty($object->id_status)){
            $object->id_status  = Material_Model_Bo_Status::PREP_ENVIO;
        }

        $object->telefone   = str_replace(array('(', ')', '-', '.', ','), "", $object->telefone);
        $object->celular    = str_replace(array('(', ')', '-', '.', ','), "", $object->celular);
        $object->fax        = str_replace(array('(', ')', '-', '.', ','), "", $object->fax);
        $object->cep        = str_replace(array('(', ')', '-', '.', ','), "", $object->cep);
     }

    public function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $itemEntregaBo = new Material_Model_Bo_ItemEntrega();
        if(isset($request['id_item'])){
            foreach ($request['id_item'] as $id_item){
                if(!empty($request['quantidade_'.$id_item]) && $request['quantidade_'.$id_item] != '0,00'){
                    $itemEntrega                     = $itemEntregaBo->get();
                    $itemEntrega->id_item            = $id_item;
                    $itemEntrega->id_entrega         = $object->id_entrega;
                    $itemEntrega->quantidade         = str_replace(".", "",$request['quantidade_'.$id_item]);
                    $itemEntrega->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
                    $itemEntrega->dt_criacao         = date('Y-m-d H:i:s');

                    $itemEntrega->save();
                }
            }
        }


        if(isset($request['atualizacao'])){
            //aparece a mensagem assim que a pessoa ser redirecionada
            App_Validate_MessageBroker::addSuccessMessage("Entrega atualizado com sucesso!");
        }else{
            //aparece a mensagem assim que a pessoa ser redirecionada
            App_Validate_MessageBroker::addSuccessMessage("Pedido Cadastrado com sucesso!");
        }
    }

    public function getItemEntrega()
    {
        $empresa = Zend_Auth::getInstance()->getIdentity();
        $estoqueBo         = new Material_Model_Bo_Estoque();
        $itemEntregaBo     = new Material_Model_Bo_ItemEntrega();
        $estoqueMovBo      = new Material_Model_Bo_EstoqueMovimento();

        $listEstoque       = $estoqueMovBo->getEstoqueByEntrega();
        foreach ($listEstoque as $key => &$estoque){
            $lisEntrega        = $itemEntregaBo->getItemPedido($empresa->id, $estoque['id_item']);
            if(count($lisEntrega)){
                foreach ($lisEntrega as $entrega){
                    $estoque['quantidade'] = floatval($estoque['quantidade']) - floatval($entrega['quantidade']);
                }
                if(bccomp($estoque['quantidade'], 0, 2 ) == 0 ){
                    unset($listEstoque[$key]);
                }
            }
        }
         return $listEstoque;
    }

}