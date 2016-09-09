<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  29/04/2013
 */
class Sis_Model_Bo_Endereco extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_Endereco
     */
    protected $_dao;


    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_Endereco();
    }

    protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(isset($request['tipoEndereco'])){
            $tipoEnderecoBo   = new Sis_Model_Bo_TipoEnderecoRef();
            $tipoEnderecoBo->deleteByEndereco($object->id);
            $this->_saveAssociativa($tipoEnderecoBo, $request['tipoEndereco'], 'tie_id', $object->id);
        }

    }

    private function _saveAssociativa($bo, $value, $name, $id_endereco)
    {
        if(isset($value)){
            foreach ($value as $id){
                $tpEnderecoRef                 = $bo->get();
                $tpEnderecoRef->id_endereco    = $id_endereco;
                $tpEnderecoRef->$name          = $id;
                $tpEnderecoRef->save();
            }
        }
    }

}