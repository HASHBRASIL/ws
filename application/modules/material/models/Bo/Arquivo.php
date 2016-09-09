<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  11/10/2013
 */
class Material_Model_Bo_Arquivo extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Arquivo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Arquivo();
        parent::__construct();
    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $upload_adapter = null){


        $upload_adapter->addValidator('Extension', false, array('jpeg','JPEG', 'jpg', 'png', 'gif', 'JPG', 'GIF', 'PNG'));


        foreach ($upload_adapter->getFileInfo() as $key => $file) {

            if ($upload_adapter->isUploaded ($key)){

                $upload_adapter->isValid($key);

                if (count($upload_adapter->getMessages())>0){

                    foreach ($upload_adapter->getMessages()as $keyMessage => $message){

                        if($keyMessage == 'fileExtensionFalse'){
                            App_Validate_MessageBroker::addErrorMessage("A extensão do arquivo {$file["name"]} é inválido.");
                            return false;
                        }
                    }
                }
            }

        }

        return true;
    }

    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $upload_adapter = null)
    {
        $options = array();

        $options['ignoreNoFile']    = true;
        $options['overwrite']       = false;

        $nomeArquivo        = explode('.', $upload_adapter->getFileName(null, false));
        $object->nome       = $nomeArquivo[0];
        $object->extensao   = $nomeArquivo[1];
        $object->nome_md5   = date('H_i').$upload_adapter->getHash('md5');

        $upload_adapter->addFilter('Rename', $object->nome_md5);

        if ($upload_adapter->isUploaded ( 'arquivo' )){
            $path    = "/public/uploads/item/";
            $arquivo = $this->sendFileToServer($upload_adapter, $path, $options, null, $object->id_item);
        }

    }
}