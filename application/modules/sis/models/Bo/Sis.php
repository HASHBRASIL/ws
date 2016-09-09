<?php
/**
 * @author Carlos Vincius Bonfim da Silva
 * @since  20/08/2013
 */
class Sis_Model_Bo_Sis extends App_Model_Bo_Abstract
{
	/**
	 * @var Sis_Model_Dao_Sis
	 */
	protected $_dao;
	
	public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_Sis();
        parent::__construct();
    }
    
    protected function _validar($object, Zend_File_Transfer_Adapter_Http $upload_adapter = null){
    	
    	$proprietarioList = $this->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_workspace = ?" => $object->id_workspace)); 

    	if(count($proprietarioList)>0 && empty($object->id_proprietario)){
    		App_Validate_MessageBroker::addErrorMessage("Já existe um proprietário cadastrado no sistema");
    		return false;
    	}
    	
    	
    	$upload_adapter->addValidator('Extension', false, array('jpeg','JPEG', 'jpg', 'png', 'gif', 'JPG', 'GIF', 'PNG'));
    	
    	//validando tamanho da imagem
    	$upload_adapter->addValidator('ImageSize', false,
			array(	'minwidth' => 40,
				'maxwidth'  => 300,
				'minheight'  => 50,
				'maxheight'  => 200)
    		);
    	
    	foreach ($upload_adapter->getFileInfo() as $key => $file) {
    		
    		if ($upload_adapter->isUploaded ($key)){
    		
    			$upload_adapter->isValid($key);
    		
    			if (count($upload_adapter->getMessages())>0){
    				 
    				foreach ($upload_adapter->getMessages()as $keyMessage => $message){
    					
    					if($keyMessage == 'fileExtensionFalse'){
    						App_Validate_MessageBroker::addErrorMessage("A extensão do arquivo {$file["name"]} é inválido.");
    						return false;
    					}
    					if($keyMessage == 'fileImageSizeNotDetected'){
    						App_Validate_MessageBroker::addErrorMessage("Não pude reconhecer o tamanho do arquivo {$file["name"]}.");
    						return false;
    					}
    					if(keyMessage == 'fileImageSizeWidthTooBig'){
    						App_Validate_MessageBroker::addErrorMessage("A largura do arquivo {$file["name"]} é muito grande.");
    						return false;
    					}
    					if($keyMessage == 'fileImageSizeHeightTooBig'){
    						App_Validate_MessageBroker::addErrorMessage("A altura do arquivo {$file["name"]} é muito grande.");
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
    	$options['overwrite'] = true;
    	$options['ignoreNoFile'] = true;
    	
    	if ($upload_adapter->isUploaded ( 'logo' ) || $upload_adapter->isUploaded ( 'logo_report' )){
    		$path = "/public/uploads/logos";
    		$arquivos = $this->sendFileToServer($upload_adapter, $path, $options, null, null);
    		
    		foreach ($arquivos as $key => $arquivo){
    			if ($arquivo['name'] != ""){
    				$object->$key = $arquivo['name'];
    			}
    		}
    	}
    	
    }
    
    public function deleteImageAjaxAction($request, $object){
    	
    	if($request['type'] == "logo"){
    		
    		$object->logo = "";
    		
    	}else if($request['type'] == "logo-report"){
    		
    		$object->logo_report = "";
    		
    	}
    	try {
    		$object->save();
    	} catch (Exception $e) {
    		App_Validate_MessageBroker::addErrorMessage("O sistema se encontra fora do ar no momento. Caso persista entre em contato com o Administrador.".$e->getMessage());
    		throw new App_Validate_Exception();
    	}
    	
    	return true;
    }
    
}