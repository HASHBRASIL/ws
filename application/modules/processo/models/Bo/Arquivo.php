<?php
/**
 * @author Carlos Vinicius Bonfim
 * @since  08/07/2013
 */
class Processo_Model_Bo_Arquivo extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_Arquivo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_Arquivo();
        parent::__construct();
    }

    public function getArquivo($id){

    	$arquivoObj = $this->get($id);
    	$arquivo = APPLICATION_PATH.'/../data/upload/processo/'.$arquivoObj->pro_id."/";

		set_time_limit(0);

		$aquivoNome = $arquivoObj->nome_md5; // nome do arquivo que será enviado p/ download
		$arquivoLocal = $arquivo.$aquivoNome; // caminho absoluto do arquivo

		// Verifica se o arquivo não existe
		if (!file_exists($arquivoLocal)) {
			echo "Arquivo não encontrado...";
		exit;
		}

		// Definimos o novo nome do arquivo
		$novoNome = $arquivoObj->nome.".".$arquivoObj->extensao;

		// Configuramos os headers que serão enviados para o browser
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename="'.$novoNome.'"');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($arquivoLocal));
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Expires: 0');

		// Envia o arquivo para o cliente
		readfile($arquivoLocal);
		echo "Fazendo Download...";
		exit;
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $upload_adapter = null)
    {

        //upload de arquivos
        $options = array();

        $options['ignoreNoFile']    = true;
        $options['overwrite']       = false;


        if ($upload_adapter->isUploaded ( 'arquivo' )){
            $path = "/data/upload/processo";
            $idFolder = $object->pro_id;
            $arquivos = $this->sendFileToServer($upload_adapter, $path, $options, null, $idFolder);

            $nameExtension = explode('.', $upload_adapter->getFileName(null, false));

            $arquivoBo				= new Processo_Model_Bo_Arquivo();
            $processoBo				= new Processo_Model_Bo_Processo();

            $processo	 			= $processoBo->find(array("pro_id = ?"=>$object->pro_id))->current();
            $empresa = $processo->getEmpresa();

            $arquivoList 			= $arquivoBo->find(array("pro_id = ?"=>$object->pro_id));
            $arquivoListCount 		= count($arquivoList);

            $nameMd5 = md5($processo->pro_codigo."_".$empresa->id."_".$nameExtension[0]."_".date("d-m-Y")."_v".($arquivoListCount+1));

            $object->pro_id = $object->pro_id;
            $object->nome = $processo->pro_codigo."_".$empresa->id."_".$nameExtension[0]."_".date("d-m-Y")."_v".($arquivoListCount+1);
            $object->nome_md5 = $nameMd5;
            $object->extensao =$nameExtension[1];


            rename(APPLICATION_PATH."/..".$path."/".$idFolder."/".$upload_adapter->getFileName(null, false), APPLICATION_PATH."/..".$path."/".$idFolder."/".$nameMd5);

        }
    }

}