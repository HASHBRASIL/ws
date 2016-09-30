<?php
abstract class App_Model_Bo_Abstract
{
	/**
	 * @desc true se exigir workspace para acessar dados
	 */
	protected $_hasWorkspace = false;

    protected $_translate;
    protected $_servico;

	/**
	 * @desc true se necessário retornar registros sem workspace
	 */
	protected $_getRegistersWithoutWorkspace = true;

    /**
     * @var App_Model_Dao_Abstract
     */
    protected $_dao;

    /**
     * @var string
     */
    protected $_successDeleteMessage;

    public function __construct(){
        $this->_successDeleteMessage = "Registro removido com sucesso.";
        $this->_translate = Zend_Registry::get('Zend_Translate');
    }

    public function get($id = null)
    {
        return $this->_dao->get($id);
    }

    public function saveFromRequest($request, $object)
    {
        // @todo verificar se dado sendo editado tem permissão!
        // @todo verificar como fazer isso - regras para poder editar / salvar dado.

        $this->_dao->beginTransaction();

        $object = $object->setFromArray($request);
        //verifica se possuir o campo e se possuir verifica se é criação ou atualização
        if (isset($object->id_criacao_usuario)) {
            if (empty($object->id_criacao_usuario)) {
                $object->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->id;
            } elseif (isset($object->id_atualizacao_usuario)) {
                $object->id_atualizacao_usuario = Zend_Auth::getInstance()->getIdentity()->id;
            }
        }

        //verifica se possuir o campo e se possuir verifica se é criação ou atualização
        if (isset($object->dt_criacao)) {
            if (empty($object->dt_criacao)) {
                $object->dt_criacao = date('Y-m-d H:i:s');
            } elseif (isset($object->dt_atualizacao)) {
                $object->dt_atualizacao = date('Y-m-d H:i:s');
            }
        }

        //verifica se o BO é obrigatório o vinculo com o grupo / time, e existindo checa se o registro deve ser público
        if ($this->_grupoVinculo) {
            // @todo ver essa regra.
            if (isset($request['register_public']) && $request['register_public'] == true) {
                // @todo inicialmente nunca deve cair nessa regra.
                $object->id_grupo = null;
            } else if (empty($object->id_grupo)) {
                $identity = Zend_Auth::getInstance()->getIdentity();
                $object->id_grupo = $identity->time['id'];
            }
        }

        $uploadAdapter = new Zend_File_Transfer_Adapter_Http();


        try {
            $this->_validar($object, $uploadAdapter);

            $this->_preSave($object, $request, $uploadAdapter);
            $object->save();
            $this->_postSave($object, $request, $uploadAdapter);
//        } catch (App_Validate_Exception $e) {
//            $this->_dao->rollBack();
//            throw $e;
        } catch (Exception $e) {
            $this->_dao->rollBack();
            throw $e;
        }
    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        return true;
    }

    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
    }

    protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
    }

    /**
     * @desc pegar toda a lista
     * @param boolean $ativo default = true
     * @return array|object Zend_Db_Table_Rowset_Abstract
     */
    public function getList($ativo = true)
    {
        return $this->_dao->getList($ativo);
    }

    /**
     * @param array $criteria
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function find($criteria = null, $order = null, $count = null, $offset = null)
    {
        return $this->_dao->fetch($criteria, $order, $count, $offset);
    }


    /**
     * @param array $criteria
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findOne($criteria = null, $order = null, $count = null)
    {
        return $this->_dao->fetchOne($criteria, $order, $count);
    }

    /**
     * @param boolean $ativo
     * @return array ($key => $value)
     */
    public function getPairs($ativo = true, $chave = null, $valor = null, $ordem = null, $limit = null)
    {
        $where = array();
        if($ativo){
            $where = array("ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }

    	if ($this->_hasWorkspace) {
//
//    		$workspaceSession = new Zend_Session_Namespace('workspace');
//
//    		if (!$workspaceSession->id_workspace){
//    			$array = array();
//    			return $array ;
//    		}
//
//    		$mountWhere = array();
//
//
//	    	if ($workspaceSession->free_access != true){
//
//
//	    		if ($this->_getRegistersWithoutWorkspace){
//
//	    			$mountWhere = $mountWhere + array("id_workspace IS NULL or id_workspace = {$workspaceSession->id_workspace} " => "");
//	    		}else{
//
//	    			$mountWhere = array("id_workspace = ?" => $workspaceSession->id_workspace);
//	    		}
//
//	    		$where = $where + $mountWhere ;
//	    	}

            $identity = Zend_Auth::getInstance()->getIdentity();
            $mountWhere = array("id_grupo is null or id_grupo = ?" => $identity->time['id']);
            $where = $where + $mountWhere;
    	}


        return $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
    }

    /**
     * @desc inativa o dado a partir do id do dado. Adiciona a mensagem na classe App_Validate_MessageBroker
     * @param int $id
     */
    public function inativar($id)
    {
        if (empty($id)) {
            App_Validate_MessageBroker::addErrorMessage('Selecione um registro para ser excluído.');
            return;
        }
        $return  = $this->_dao->inativar($id);
        if($return){
            App_Validate_MessageBroker::addSuccessMessage($this->_successDeleteMessage);
        }
    }

    /**
     * @param boolean $ativo
     * @return array ($key => $value)
     */
    public function getAutocomplete($term, $limit = 10, $page = 0, $chave = null, $valor = null,
            $ordem = null, $ativo = false)
    {
        // @todo revisar regra padrão
        $where = null;

        $where = array();
        if($ativo){
            $where = array("ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }

        if ($this->_grupoVinculo) {

//        	$workspaceSession = new Zend_Session_Namespace('workspace');

//        	if (!$workspaceSession->id_workspace){
//        		$array = array();
//        		return $array ;
//        	}

//        	$mountWhere = array();
//
//        	if ($workspaceSession->free_access != true){
//
//
//        		if ($this->_getRegistersWithoutWorkspace){
//
//        			$mountWhere = $mountWhere + array("id_workspace IS NULL or id_workspace = {$workspaceSession->id_workspace} " => "");
//        		}else{
//
//        			$mountWhere = array("id_workspace = ?" => $workspaceSession->id_workspace);
//        		}
//
//        		$where = $where + $mountWhere ;
//        	}
        }

        return $this->_dao->getAutocomplete($term, $limit, $page, $chave, $valor, $ordem, $ativo);
    }

    /**
     * Gera paginacao
     * @param mixed $data
     * @param array $options
     * @return Zend_Paginator
     * @throws Exception if data type invalid
     */
    public function paginator( array $options)
    {

        if (isset($options['select']) && ($options['select'])) {
            $select = $options['select'];
        } else {
            // pesquisa padrão
            $select = $this->_dao->selectPaginator($options);
        }

        //x($select->__toString());
        $this->_dao->fetchPaginator($select, $options);
//        var_dump($select->__toString());
//        exit;
        $adapter = new Zend_Paginator_Adapter_DbSelect($select);
        if(isset($options['countGridSelect'])){
            $adapter->setRowCount($options['countGridSelect']);
        }

        $paginator = new Zend_Paginator($adapter);
        //$paginator = Zend_Paginator::factory($select);

//        var_dump($paginator);
//        exit;

        if (isset($options['orderby']) && ($options['orderby'])) {
            $select->order($options['orderby'] . " " . $options['order']);
        }

        $itensPorPagina = 50;

        if(isset($options['itens'])){
            if($options['itens'] === 'todos'){
                $itensPorPagina = $paginator->getTotalItemCount();
            }else if(!empty((int)$options['itens'])){
                $itensPorPagina = $options['itens'];
            }
        }

        $paginator->setCurrentPageNumber(
                isset($options['page'])
                ? $options['page']
                : 1
        )->setItemCountPerPage(
                $itensPorPagina
        )->setPageRange(10);

//        if( isset( $options[ "searchString" ] ) && empty( $options[ "searchString" ] ) ){
//            unset( $options[ "searchString" ] );
//            unset( $options[ "search" ] );
//            unset( $options[ "searchField" ] );
//        }

        return $paginator;
    }

    protected function _formatDecimal($value)
    {
        if(substr_count($value, ",")){
            $value = str_replace(".", "", $value);
            $value = str_replace(",", ".", $value);
        }

        return $value? $value: null;
    }

    public function dateDmy($date)
    {
    	if($date){
    		$date = new Zend_Date($date);
    		return $date->toString('dd/MM/yyyy');
    	}
    	return null;
    }
    public function dateYmd($date)
    {
    	if($date){
    		$date = new Zend_Date($date);
    		return $date->toString('yyyy/MM/dd');
    	}
    	return null;
    }

    public function date($date, $string)
    {
    	if($date){
    		$date = new Zend_Date($date);
    		return $date->toString($string);
    	}
    	return null;

    }

    public function sendFileToServer (Zend_File_Transfer_Adapter_Http $upload_adapter, $path, $options =null, $size =null/*depricated*/, $idFolder = null){

    	if(isset($options)){

    		/**
    		 * @desc Sobrescrever arquivo no servidor
    		 * @todo implementar solucao de sobrescrita de arquivo via zend_file_transfer
    		 * @since 20/08/2013
    		 * @author Carlos Vinicius Bonfim da Silva
    		 */
    		if($options['overwrite'] == true){

    			if (count($upload_adapter->getFileInfo()) > 0){

    				foreach ($upload_adapter->getFileInfo() as $key => $files) {

    					if (file_exists(APPLICATION_PATH."/..".$path."/".$files['name']) && $files['name'] != "" ) {

    						unlink(APPLICATION_PATH."/..".$path."/".$files['name']);
    					}
    				}
    			}
    		}
    		//quando mais de um upload, pode ignorar se um deles vir em branco
    		if ($options['ignoreNoFile'] == true){
    			$upload_adapter->setOptions(array("ignoreNoFile" => true));
    		}

    	}
    	//SE NECESSITAR SALVAR EM SUBPASTAS COM O ID
    	if ($idFolder){

    		if(is_dir(APPLICATION_PATH."/..".$path."/".$idFolder)){

    			$upload_adapter->setDestination(APPLICATION_PATH."/..".$path."/".$idFolder);

	    	}else{
	    		mkdir(APPLICATION_PATH."/..".$path."/".$idFolder);
	    		$upload_adapter->setDestination(APPLICATION_PATH."/..".$path."/".$idFolder);

	    	}

    	}else{

    		$upload_adapter->setDestination(APPLICATION_PATH."/..".$path);
    	}

	    try {
	    	$upload_adapter->receive();


	    	return $upload_adapter->getFileInfo();

	    } catch (Zend_File_Transfer_Exception $e) {
	    	throw new App_Validate_Exception( array( $e->getMessage() ) );
	    }
    }

    public function getBasicConfigHeader(array $servico)
    {
        $id_pessoa   = Zend_Auth::getInstance()->getIdentity()->id;
        $id_grupo    = $servico['id_grupo'];
        $id_servico  = $servico['id'];

        if(!empty($id_grupo) ){
            $selectPermissaoPessoa = $this->_dao->select() ->setIntegrityCheck(false)
                                                ->from(array('rlpp' => 'rl_permissao_pessoa'), array('rlpp.configuracao'))
                                                ->where('rlpp.dt_expiracao > CURRENT_DATE')
                                                ->where('rlpp.id_grupo = ?', $id_grupo)
                                                ->where('rlpp.id_pessoa = ?', $id_pessoa)
                                                ->where('rlpp.id_servico = ?', $id_servico);

            $configPP = $this->_dao->fetchRow($selectPermissaoPessoa);

            if(!empty($configPP)){

                $configPP = json_decode($configPP->configuracao, true);

                if(!empty($configPP['ws_colunas'])){
                    return $configPP['ws_colunas'];
                }
            }
        }



        $selectServicoMetadata = $this->_dao->select()
                                            ->setIntegrityCheck(false)
                                            ->from(array('tsm' => 'tb_servico_metadata'), array('tsm.valor'))
                                            ->where('tsm.metanome = ?', 'ws_colunas')
                                            ->where('tsm.id_servico = ?', $id_servico);

        $configSM = $this->_dao->fetchRow($selectServicoMetadata);

        if(!empty($configSM->valor)){
            return json_decode($configSM->valor, true);
        }


        if(!empty($servico['id_tib'])){
            $selectTPIB = $this->_dao   ->select()
                                        ->setIntegrityCheck(false)
                                        ->from(array('tpib' => 'tp_itembiblioteca'), array('lower(tpib.metanome) as campo', 'tpib.nome as label', 'tpib.tipo as tipo'))
                                        ->join(array('tpibm' => 'tp_itembiblioteca_metadata'), 'tpibm.id_tib = tpib.id', array())
                                        ->where('tpib.id_tib_pai = ?', $servico['id_tib'])
                                        ->where('tpibm.metanome = ?', 'ws_ordemLista')
                                        ->order('cast(tpibm.valor as integer)');

            $configTPIB = $this->_dao->fetchAll($selectTPIB);
            if(!empty($configTPIB)){
                return $configTPIB->toArray();
            }
        }
        return false;
    }

    public function upload($file, $time, $grupo)
    {

        $identity   = Zend_Auth::getInstance()->getIdentity();
        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');
        $ib     =   explode('_', key($file));
        $ext    =   explode('.', $file[ key($file) ]['name']);
        $nome   =   $ib[0] . '.' . $ext[1];
        $newFolder  =   $filedir->path . $time . '/';
        $retorno    =   $time . '/';
        if ( !file_exists($newFolder) ){
            mkdir( $newFolder, 0755 );
        }

        $newFolder  =   $newFolder . $grupo . '/';
        $retorno    =   $retorno . $grupo . '/';
        if ( !file_exists($newFolder) ){
            mkdir( $newFolder, 0755 );
        }
        move_uploaded_file($file[ key($file) ]['tmp_name'], $newFolder . $nome);

        return $retorno . $nome;

    }



    public function setServico($servico)
    {
        $this->_servico = $servico;
    }

    public function saveFile($fileContents, $fileName, $idPai = null, $ocr = null)
    {
        $identity  = Zend_Auth::getInstance()->getIdentity();

        $boIb = new Content_Model_Bo_ItemBiblioteca();
        $boTib = new Config_Model_Bo_Tib();
        $boRlGI = new Content_Model_Bo_RlGrupoItem();
        $boRlVI = new Content_Model_Bo_RlVinculoItem();
        $objGrupo = new Config_Model_Bo_Grupo();

        $dadosOcr = null;
        $arqs = array();
        $extension = substr(strrchr($fileName, "."),1);

        if (isset($this->_servico['id_grupo'])){

            $grupo = $this->_servico['id_grupo'];
        } elseif (isset($this->_servico['ws_grupo'])){
            $grupos = $objGrupo->getGruposByIDPaiByMetanome($identity->time['id'],$this->_servico['ws_grupo']);

            if(!empty($grupos)){
                $grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino não encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $grupo = $identity->grupo['id'];
        }

        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

        $id_ib = UUID::v4();
        $nome   =   $id_ib . '.' . $extension;

        $newFolder  =   $filedir->path . $identity->time['id'] . '/';

        $retorno    =   $identity->time['id'] . '/';

        if ( !file_exists($newFolder) ){
            mkdir( $newFolder, 0755 );
        }

        $newFolder  =   $newFolder . $grupo . '/';
        $retorno    =   $retorno . $grupo . '/';
        if ( !file_exists($newFolder) ) {
            mkdir($newFolder, 0755);
        }

        file_put_contents($newFolder . $nome, $fileContents);

        // @todo coloca o arquivo no google cloud!

        $filePath = $retorno . $nome;

        if (isset($this->_servico['ws_arqcampo'])) {
            $arrCampo = $boTib->getById($this->_servico['ws_arqcampo']);
            if (isset($this->_servico['ws_arqnome'])) {
                $arrNome = $boTib->getById($this->_servico['ws_arqnome']);
            }
            if (isset($this->_servico['ws_arqstatus'])) {
                $arrStatus = $boTib->getById($this->_servico['ws_arqstatus']);
            }
            if (isset($this->_servico['ws_arqdata'])) {
                $arrData = $boTib->getById($this->_servico['ws_arqdata']);
            }
            //$arrCampoMaster = $boTib->getById($arrCampo[0]['id_tib_pai']);

            $id_master = $boIb->persiste(false,$arrCampo[0]['id_tib_pai'],$identity->id,null,null);

            $id_ib = $boIb->persiste(false,$arrCampo[0]['id'],$identity->id,$id_master,null);

            if($arrNome){
                $id_nome = $boIb->persiste(false,$arrNome[0]['id'],$identity->id,$id_master, $fileName);
            }

            if($arrStatus) {
                if ($ocr == true) {
                    $status = 'OCR';
                } else {
                    $status = 'NOVO';
                }
                $id_status = $boIb->persiste(false,$arrStatus[0]['id'],$identity->id,$id_master, $status);
            }

            if($arrData) {
                $id_data = $boIb->persiste(false,$arrData[0]['id'],$identity->id,$id_master, date('d/m/Y H:i:s'));
            }

//            if ($ocr == true) {
//                // @todo faz regra OCR
//                $googleVision = new App_Model_Bo_Vision();
//
//                $retornoOcr = $googleVision->process($fileContents);
//
//                $textoOcr = $retornoOcr['responses'][0]['textAnnotations'][0]['description'];
//
//                $arrOcr = $boTib->getByMetanome('ocr');
//
//                $dadosOcr = $boIb->persiste(false,$arrOcr[0]['id'],$identity->id,$id_master,$textoOcr);
//            }

            // @todo seria aqui para gerar imagem peq para visualizar

            $boIb->persiste($id_ib,null,null,null,$filePath);

            $boRlGI->relacionaItem($grupo, $id_master);

            if ($idPai) {
                $boRlVI->relacionaItem($idPai, $id_master);
            }
        }

        $retorno = array('ib' => $id_master, 'caminho' => $filePath, 'original' => $fileName, 'ocr' => $dadosOcr);

        return $retorno;
    }
}
