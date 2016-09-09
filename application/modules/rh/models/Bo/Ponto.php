<?php
/**
 * @author Vinicius Leônidas
 * @since 29/01/2014
 */
class Rh_Model_Bo_Ponto extends App_Model_Bo_Abstract
{
	protected $_dao;
	
	public function __construct(){
		$this->_dao = new Rh_Model_Dao_Ponto();
		parent::__construct();
	}
	
	protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		if(empty($object->local)){
			App_Validate_MessageBroker::addErrorMessage("O campo local está vazio.");
			return false;
		}
		
		if(empty($object->descricao)){
			App_Validate_MessageBroker::addErrorMessage("O campo descrição está vazio.");
			return false;
		}
		
		$uploadAdapter->addValidator('Extension', false, array('txt', 'TXT' ));
		
		foreach ($uploadAdapter->getFileInfo() as $key => $file) {
		
			if ($uploadAdapter->isUploaded ($key)){
		
				$uploadAdapter->isValid($key);
		
				if (count($uploadAdapter->getMessages())>0){
		
					foreach ($uploadAdapter->getMessages()as $keyMessage => $message){
		
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
	
	public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		
		if (!empty($request['id_ponto'])){
			$path1    = "uploads/pontos/";
			$img = $this->find(array('id_rh_registro_ponto = ?' => $request["id_ponto"]))->current();
			if (!empty($img['arquivo'])){
				unlink($path1.$img['arquivo']);
			}
		}
		
		$options = array();
		
		$options['ignoreNoFile']    = false;
		$options['overwrite']       = true;
	
		if ($uploadAdapter->isUploaded ('arquivo')){
			
            $path    = "/public/uploads/pontos/";
		echo '1';
			$arquivos = $this->sendFileToServer($uploadAdapter, $path,$options);
		echo '2';
			$nameExtension = explode('.', $uploadAdapter->getFileName(null, false));
		echo '3';
			$nameMd5 = md5($nameExtension[0]."_".date('Y-m-d H:i:s')).'.'.$nameExtension[1];
		echo '4';
			rename(APPLICATION_PATH."/..".$path.$uploadAdapter->getFileName(null, false), APPLICATION_PATH."/..".$path.$nameMd5);
		echo '5';
			$object->arquivo = $nameMd5;
		echo '6';
			$object->arquivo_original = $nameExtension[0]." ".date('d/m/Y H:i:s');
		}
	}
	
	public function converterHora($total_segundos){
			
		$hora = sprintf("%02s",floor($total_segundos / (60*60)));
		$total_segundos = ($total_segundos % (60*60));
	
		$minuto = sprintf("%02s",floor ($total_segundos / 60 ));
		$total_segundos = ($total_segundos % 60);
	
		$hora_minuto = $hora.":".$minuto;
		return $hora_minuto;
	
	}
}
