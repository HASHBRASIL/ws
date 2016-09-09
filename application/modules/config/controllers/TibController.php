<?php
class Config_TibController extends App_Controller_Action_Twig
{

 	public function init()
    {
        parent::init();
        $this->_helper->layout()->setLayout('novo_hash');
        $this->_bo = new Config_Model_Bo_Tib();
    }

    /**
     * configuração padrão para definir o uso de twig em todas as actions do controller
     * @todo pode-se criar um App_controller_Action_Twig para facilitar isso
     */
    public function postDispatch()
    {
//        $this->view->params = $this->getAllParams();
//        $this->renderScript('twig.phtml');
        return parent::postDispatch();
    }
    /**
     * index: esta rota deve listar todos os servi�os em ordem alfab�tica, em forma de �rvore.
     * Deve utilizar o index.twig padr�o para exibi��o.
     * Esta exibi��o � paginada e utilizar� a rota /list para completar a listagem.
     */
    public function indexAction()
    {
    	$tibBo = new Config_Model_Bo_Tib();
        $rowset1 = $tibBo->find("tipo ilike 'master'", 'nome', 200, 0);
        $rowset2 = $tibBo->find("tipo not ilike 'master'", 'nome', 10, 0);

        $i = 0;
        $rowset = array();
    	foreach ($rowset1 as $tib1){
        	$rowset[$i] =$tib1;
        	$i++;
        }
        foreach ($rowset2 as $tib2){
        	$rowset[$i] =$tib2;
        	$i++;
        }

        $header = array();
        $header[] = array('campo' => 'nome', 'label' => 'Nome');
        $header[] = array('campo' => 'metanome', 'label' => 'Metanome');
        $header[] = array('campo' => 'tipo', 'label' => 'Tipo');

        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $rowset, 'header' => $header );
    }
    
    public function gridAction(){
        
        $header = array();
        $header[] = array('campo' => 'nome', 'label' => 'Nome');
        $header[] = array('campo' => 'metanome', 'label' => 'Metanome');
        $header[] = array('campo' => 'tipo', 'label' => 'Tipo');
        
        $this->header = $header;
        
        $select = $this->_bo->getTipoItemBibliotecaGrid();
//        x($select);
        $this->_gridSelect = $select;
        
        parent::gridAction();
        
    }
    
    public function gridfieldsAction(){
        
        $idPai = $_GET['id'];
        
        $header = array();
        $header[] = array('campo' => 'nome', 'label' => 'Nome');
        $header[] = array('campo' => 'metanome', 'label' => 'Metanome');
        $header[] = array('campo' => 'tipo', 'label' => 'Tipo');
        $header[] = array('campo' => 'ws_ordem', 'label' => 'Ordem');
        
        $this->header = $header;
        
        $select = $this->_bo->getTipoItemBibliotecaGrid($idPai);
//        x($select);
        $this->_gridSelect = $select;
        
        parent::gridAction();
        
    }
    
	public function listAction()
    {
    	$total = $this->getParam('total');

    	$informacaoBo = new Config_Model_Bo_Tib();

    	$rowset = $informacaoBo->find(null, 'nome', 30, $total);

    	$this->_helper->json(array('data' => $rowset->toArray()));
    }


    public function createAction(){
    	$header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

    	$tib = new Config_Model_Dao_Tib();

    	$select = $tib->select()
    				->distinct()
    				->from(array('tib' => 'tp_itembiblioteca'), 'tipo');
		$stmt = $select->query();
		$result = $stmt->fetchAll();

		$arrOpcTipo	= array();
		$i	= 0;
		foreach ($result as $chave_tib => $valor_tib){
			if ( $valor_tib['tipo'] !== '' ){
				$arrOpcTipo[$i]['id']	= $valor_tib['tipo'];
				$arrOpcTipo[$i]['valor'] = $valor_tib['tipo'];
				$i++;
			}
		}

		$masters = $tib->fetchAll("tipo = 'Master'");
		$arrOpcMaster = array();
		$i = 0;
		foreach ($masters as $chave_master => $valor_master){
			$arrOpcMaster[$i]['id'] = $valor_master->id;
			$arrOpcMaster[$i]['valor'] = $valor_master->nome;
			$i++;
		}

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'nome',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'nome',
    			//'descricao' 	=> 'Nome do servi�o',
    			'tipo'		=> 'text',
    			'perfil'	=> 'servico',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-2',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'metanome',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Metanome',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][2] = array(
    			'id'            => 'descricao',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Descrição',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][3] = array(
    			'id'            => 'tamanho',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tamanho',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][4] = array(
    			'id'            => 'tipo',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tipo',
    			'tipo'          => 'ref_itemBiblioteca',
    			'metanome'		=> 'tipo',
    			'perfil'        => 'servico',
    			'items'			=> $arrOpcTipo,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][5] = array(
    			'id'            => 'id_tib',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tib Pai',
    			'tipo'          => 'ref_itemBiblioteca',
    			'perfil'        => 'servico',
    			'metanome'		=> 'pai',
    			'items'			=> $arrOpcMaster,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);

        
    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function createfieldAction() {
        $this->_helper->layout->disableLayout();
        $idTib = $this->getRequest()->getParam('field');
        
        $header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

    	$tib = new Config_Model_Dao_Tib();

    	$select = $tib->select()
    				->distinct()
    				->from(array('tib' => 'tp_itembiblioteca'), 'tipo');
		$stmt = $select->query();
		$result = $stmt->fetchAll();

		$arrOpcTipo	= array();
		$i	= 0;
		foreach ($result as $chave_tib => $valor_tib){
			if ( $valor_tib['tipo'] !== '' ){
				$arrOpcTipo[$i]['id']	= $valor_tib['tipo'];
				$arrOpcTipo[$i]['valor'] = $valor_tib['tipo'];
				$i++;
			}
		}

		$masters = $tib->fetchAll("tipo = 'Master'");
		$arrOpcMaster = array();
		$i = 0;
		foreach ($masters as $chave_master => $valor_master){
			$arrOpcMaster[$i]['id'] = $valor_master->id;
			$arrOpcMaster[$i]['valor'] = $valor_master->nome;
			$i++;
		}

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'nome',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'nome',
    			//'descricao' 	=> 'Nome do servi�o',
    			'tipo'		=> 'text',
    			'perfil'	=> 'servico',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-2',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'metanome',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Metanome',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][2] = array(
    			'id'            => 'descricao',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Descrição',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][3] = array(
    			'id'            => 'tamanho',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tamanho',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][4] = array(
    			'id'            => 'tipo',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tipo',
    			'tipo'          => 'ref_itemBiblioteca',
    			'metanome'		=> 'tipo',
    			'perfil'        => 'servico',
    			'items'			=> $arrOpcTipo,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][5] = array(
    			'id'            => 'id_tib',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tib Pai',
    			'valor'         => '$idTib',
    			'tipo'          => 'ref_itemBiblioteca',
    			'perfil'        => 'servico',
    			'metanome'		=> 'pai',
    			'readonly'		=> 'readonly',
    			'items'			=> $arrOpcMaster,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);

        
    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('perfis' => $arrPerfis,'campos' => $arrCampos);
        
    }
    
    public function insertAction() {        
    	try {
            $svcBo = new Config_Model_Bo_Servico();
            $target = "";
            if (isset($this->servico['ws_target']) && $this->servico['ws_target']) {
                $target = current($svcBo->getServicoByMetanome($this->servico['ws_target']))['id'];
            } else {
                $target = $this->servico['id_pai'];
            }
	    	$dados = array();
	    	foreach ($_POST as $chave => $valor){
	    		if (substr($chave,-1) === '_'){
	    			$chave = substr($chave,0,-1);
	    		} elseif ( $chave == 'tipo_tipo' ) {
	    			$chave = 'tipo';
	    		}
	    		$dados[$chave] = $valor;
	    	}
	    	$dados['id'] = UUID::v4();
	    	$dados['dt_criacao'] = date("Y-m-d h:i:s.B");

	    	if (strtoupper ($dados['tipo']) == 'MASTER' && empty($dados['id_tib_pai'])){
	    		unset($dados['id_tib_pai']);
	    	}

	    	$tib = new Config_Model_Dao_Tib();
	    	$tib->insert($dados);
            $response = array(
                'success' => true,
                'msg' => $this->_translate->translate("Tipo Item Biblioteca inserido com sucesso"),
                'data' => array('target' => array('servico' => $target))
            );
            $this->_helper->json($response);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function retrieveAction(){
    	$idTib = $this->getParam('id');

    	$tib = new Config_Model_Dao_Tib();
    	$dataTib = $tib->fetchRow("id = '$idTib'");
    	$select = $tib->select()
    	->distinct()
    	->from(array('tib' => 'tp_itembiblioteca'), 'tipo');
    	$stmt = $select->query();
    	$result = $stmt->fetchAll();

    	$arrOpcTipo	= array();
    	$i	= 0;
    	foreach ($result as $chave_tib => $valor_tib){
    		if ( $valor_tib['tipo'] !== '' ){
    			$arrOpcTipo[$i]['id']	= $valor_tib['tipo'];
    			$arrOpcTipo[$i]['valor'] = $valor_tib['tipo'];
    			$i++;
    		}
    	}

    	$masters = $tib->fetchAll("tipo = 'Master'");
    	$arrOpcMaster = array();
    	$i = 0;
    	foreach ($masters as $chave_master => $valor_master){
    		$arrOpcMaster[$i]['id'] = $valor_master->id;
    		$arrOpcMaster[$i]['valor'] = $valor_master->nome;
    		$i++;
    	}

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'nome',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'nome',
    			'valor'		=> $dataTib->nome,
    			'tipo'		=> 'text',
    			'perfil'	=> 'servico',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-2',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'metanome',
    			'ordem'         => '0',
    			'valor'		=> $dataTib->metanome,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Metanome',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][2] = array(
    			'id'            => 'descricao',
    			'ordem'         => '0',
    			'valor'		=> $dataTib->descricao,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Descrição',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][3] = array(
    			'id'            => 'tamanho',
    			'ordem'         => '0',
    			'valor'		=> $dataTib->tamanho,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tamanho',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][4] = array(
    			'id'            => 'tipo',
    			'ordem'         => '0',
    			'valor'		=> $dataTib->tipo,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tipo',
    			'tipo'          => 'ref_itemBiblioteca',
    			'metanome'		=> 'tipo',
    			'perfil'        => 'servico',
    			'items'			=> $arrOpcTipo,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][5] = array(
    			'id'            => 'id_tib',
    			'ordem'         => '0',
    			'valor'		=> $dataTib->id_tib_pai,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tib Pai',
    			'tipo'          => 'ref_itemBiblioteca',
    			'perfil'        => 'servico',
    			'metanome'		=> 'pai',
    			'items'			=> $arrOpcMaster,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);

    	$tibMeta	= new Config_Model_Dao_TibMetadata();
    	$arMeta		= $tibMeta->fetchAll("id_tib = '$idTib'");
    	$im = 0;
    	foreach ($arMeta as $chave_meta => $valor_meta){
    		$arrCampos['metadatas'][$im] = array(
    				'valor'		=>	$valor_meta->valor,
    				'id' 		=> $valor_meta->id,
    				'ordem' 	=> '0',
    				'obrigatorio' 	=> 'true',
    				'nome' 		=> $valor_meta->metanome,
    				'tipo'		=> 'text',
    				'perfil'	=> 'servico',
    				'metanome'	=> 'meta',
    				'metadatas'	=> array(
    						'ws_ordemLista'		=> '0',
    						'ws_style'		=> 'col-md-3',
    				)
    		);
    		$im++;
    	}

    	$arrPerfis = array();
    	$arrPerfis[0] = 'dados';
    	if ( $im > 0 ) {
    		$arrPerfis[1] = 'metadatas';
    	}
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('id' => $idTib, 'perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function updateAction(){
    	try {
	    	$dados = array();
		    foreach ($_POST as $chave => $valor){
		    	if (substr($chave,-1) === '_'){
		    		$chave = substr($chave,0,-1);
		    	} elseif ( $chave == 'tipo_tipo' ) {
		    		$chave = 'tipo';
		    	}
		    	$dados[$chave] = $valor;
		    }
	    	$tib = new Config_Model_Dao_Tib();
	    	$condicao = $tib->getAdapter ()->quoteInto ( 'id = ?', $dados ['id'] );
	    	return $tib->update($dados, $condicao);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function deleteAction(){
    	try {
	    	$idTib = $this->getParam('id');

	    	$tib =	new Config_Model_Dao_Tib();
	    	$filhos = $tib->fetchAll("id_tib_pai = '$idTib'");

	    	if($filhos->count() === 0 ){
	    		$tib->delete("id = '$idTib'");
	    	}
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function gerarServicoAction(){
    	$idTib = $this->getParam('id');
    	$tib = new Config_Model_Dao_Tib();
    	if(strtoupper ($tib->fetchRow("id = '$idTib'")->tipo) == 'MASTER'){
    		try {
	    		$tibModelo = $tib->fetchRow("id = '$idTib'");
	    		$servico = new Config_Model_Dao_Servico();
	    		$servicoMeta = new Config_Model_Dao_ServicoMetadata();
	    		//criar servicos
	    		// Serviço index
	    		$idIndx = UUID::v4();
	    		$arrIndex = array();
	    		$arrIndex['id'] = $idIndx;
	    		$arrIndex['descricao'] = 'Index da TIB '.$tibModelo->nome.' id: '.$tibModelo->id . ' - Obs. Serviço gerado automáticamente pela aplicação';
	    		$arrIndex['metanome'] = 'HASH_INDEX_'.strtoupper($tibModelo->metanome);
	    		$arrIndex['nome'] = $tibModelo->nome;
	    		$arrIndex['id_pai'] = $servico->fetchRow("descricao = 'Gestão HASH'")->id;
	    		$arrIndex['id_tib'] = $tibModelo->id;
	    		$arrIndex['visivel'] = 't';
	    		$servico->insert($arrIndex);
	    			// Serviço Delete
		    		$idDel = UUID::v4();
		    		$arrDel = array();
		    		$arrDel['id'] = $idDel;
		    		$arrDel['descricao'] = 'Deletar da TIB '.$tibModelo->nome.' id: '.$tibModelo->id. ' - Obs. Serviço gerado automáticamente pela aplicação';
		    		$arrDel['metanome'] = 'HASH_DELETAR_'.strtoupper($tibModelo->metanome);
		    		$arrDel['nome'] = 'Deletar '.$tibModelo->nome;
		    		$arrDel['id_pai'] = $idIndx;
		    		$arrDel['visivel'] = 'f';
		    		$servico->insert($arrDel);
			    		//Metadata de Delete
			    		//arquivo
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_arquivo';
			    		$arrMeta['valor'] = 'deleteItemBiblioteca.php';
			    		$arrMeta['id_servico'] = $idDel;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
			    		//show
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_show';
			    		$arrMeta['valor'] = 'ajax';
			    		$arrMeta['id_servico'] = $idDel;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
			    		//comportamento
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_comportamento';
			    		$arrMeta['valor'] = 'listaction';
			    		$arrMeta['id_servico'] = $idDel;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
			    		//icone
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_icon';
			    		$arrMeta['valor'] = 'glyphicon glyphicon-trash';
			    		$arrMeta['id_servico'] = $idDel;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
			    		//itemremove
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_itemremove';
			    		$arrMeta['valor'] = '1';
			    		$arrMeta['id_servico'] = $idDel;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
			    		//confirmação
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_confirm';
			    		$arrMeta['valor'] = 'Você deseja apagar o registro selecionado?';
			    		$arrMeta['id_servico'] = $idDel;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);

		    		// Serviço Edit
		    		$idEdt = UUID::v4();
		    		$arrEdt = array();
		    		$arrEdt['id'] = $idEdt;
		    		$arrEdt['descricao'] = 'Edição da TIB '.$tibModelo->nome.' id: '.$tibModelo->id. ' - Obs. Serviço gerado automáticamente pela aplicação';
		    		$arrEdt['metanome'] = 'HASH_EDITAR_'.strtoupper($tibModelo->metanome);
		    		$arrEdt['nome'] = 'Editar '.$tibModelo->nome;
		    		$arrEdt['id_pai'] = $idIndx;
		    		$arrEdt['visivel'] = 'f';
		    		$servico->insert($arrEdt);
		    			// Serviço Ajax filtro Edit
			    		$idAjxFilterEdt = UUID::v4();
			    		$arrAjxFilterEdt = array();
			    		$arrAjxFilterEdt['id'] = $idAjxFilterEdt;
			    		$arrAjxFilterEdt['descricao'] = 'Ajax filtro da TIB '.$tibModelo->nome.' id: '.$tibModelo->id. ' - Obs. Serviço gerado automáticamente pela aplicação';
			    		$arrAjxFilterEdt['metanome'] = 'HASH_AJAXFILTRAR_'.strtoupper($tibModelo->metanome);
			    		$arrAjxFilterEdt['nome'] = 'Ajax Filtrar '.$tibModelo->nome;
			    		$arrAjxFilterEdt['id_pai'] = $idEdt;
			    		$arrAjxFilterEdt['visivel'] = 'f';
			    		$servico->insert($arrAjxFilterEdt);
				    		//Metadata de Ajax filtro Edit
				    		//arquivo
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_arquivo';
				    		$arrMeta['valor'] = 'filterItemBiblioteca.php';
				    		$arrMeta['id_servico'] = $idAjxFilterEdt;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
				    		//campo
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_campo';
				    		$arrMeta['valor'] = '.nome';
				    		$arrMeta['id_servico'] = $idAjxFilterEdt;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
				    		//comportamento
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_comportamento';
				    		$arrMeta['valor'] = 'filter';
				    		$arrMeta['id_servico'] = $idAjxFilterEdt;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
				    		//show
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_show';
				    		$arrMeta['valor'] = 'ajax';
				    		$arrMeta['id_servico'] = $idAjxFilterEdt;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
			    		// Serviço Update
			    		$idUpdt = UUID::v4();
			    		$arrUpdt = array();
			    		$arrUpdt['id'] = $idUpdt;
			    		$arrUpdt['descricao'] = 'Update da TIB '.$tibModelo->nome.' id: '.$tibModelo->id. ' - Obs. Serviço gerado automáticamente pela aplicação';
			    		$arrUpdt['metanome'] = 'HASH_UPDATE_'.strtoupper($tibModelo->metanome);
			    		$arrUpdt['nome'] = 'Salvar';
			    		$arrUpdt['id_pai'] = $idEdt;
			    		$arrUpdt['visivel'] = 'f';
			    		$servico->insert($arrUpdt);
				    		//Metadata de Update
				    		//arquivo
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_arquivo';
				    		$arrMeta['valor'] = 'updateItemBiblioteca.php';
				    		$arrMeta['id_servico'] = $idUpdt;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
				    		//target
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_target';
				    		$arrMeta['valor'] = $idIndx;
				    		$arrMeta['id_servico'] = $idUpdt;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
				    		//comportamento
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_comportamento';
				    		$arrMeta['valor'] = 'formaction';
				    		$arrMeta['id_servico'] = $idUpdt;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
				    		//show
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_show';
				    		$arrMeta['valor'] = 'ajax';
				    		$arrMeta['id_servico'] = $idUpdt;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
			    		//Metadata de Edit
			    		//arquivo
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_arquivo';
			    		$arrMeta['valor'] = 'retrieveItemBiblioteca.php';
			    		$arrMeta['id_servico'] = $idEdt;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
			    		//show
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_show';
			    		$arrMeta['valor'] = 'reload';
			    		$arrMeta['id_servico'] = $idEdt;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
			    		//comportamento
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_comportamento';
			    		$arrMeta['valor'] = 'listaction';
			    		$arrMeta['id_servico'] = $idEdt;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
			    		//icone
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_icon';
			    		$arrMeta['valor'] = 'glyphicon glyphicon-edit';
			    		$arrMeta['id_servico'] = $idEdt;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
		    		// Serviço Create
		    		$idCreate = UUID::v4();
		    		$arrCreate = array();
		    		$arrCreate['id'] = $idCreate;
		    		$arrCreate['descricao'] = 'Servico de criação da TIB '.$tibModelo->nome.' id: '.$tibModelo->id . ' - Obs. Serviço gerado automáticamente pela aplicação';
		    		$arrCreate['metanome'] = 'HASH_CRIAR_'.strtoupper($tibModelo->metanome);
		    		$arrCreate['nome'] = 'Inserir '.$tibModelo->nome;
		    		$arrCreate['id_pai'] = $idIndx;
		    		$arrCreate['id_tib'] = $tibModelo->id;
		    		$arrCreate['visivel'] = 'f';
		    		$servico->insert($arrCreate);
			    		// Serviço Ajax filtro Create
			    		$idAjxFilterInsrt = UUID::v4();
			    		$arrAjxFilterInsrt = array();
			    		$arrAjxFilterInsrt['id'] = $idAjxFilterInsrt;
			    		$arrAjxFilterInsrt['descricao'] = 'Ajax filtro da TIB '.$tibModelo->nome.' id: '.$tibModelo->id. ' - Obs. Serviço gerado automáticamente pela aplicação';
			    		$arrAjxFilterInsrt['metanome'] = 'HASH_AJAXFILTRAR_'.strtoupper($tibModelo->metanome);
			    		$arrAjxFilterInsrt['nome'] = 'Ajax Filtrar '.$tibModelo->nome;
			    		$arrAjxFilterInsrt['id_pai'] = $idCreate;
			    		$arrAjxFilterInsrt['visivel'] = 'f';
			    		$servico->insert($arrAjxFilterInsrt);
				    		//Metadata de Ajax filtro Create
				    		//arquivo
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_arquivo';
				    		$arrMeta['valor'] = 'filterItemBiblioteca.php';
				    		$arrMeta['id_servico'] = $idAjxFilterInsrt;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
				    		//campo
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_campo';
				    		$arrMeta['valor'] = '.nome';
				    		$arrMeta['id_servico'] = $idAjxFilterInsrt;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
				    		//comportamento
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_comportamento';
				    		$arrMeta['valor'] = 'filter';
				    		$arrMeta['id_servico'] = $idAjxFilterInsrt;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
				    		//show
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_show';
				    		$arrMeta['valor'] = 'ajax';
				    		$arrMeta['id_servico'] = $idAjxFilterInsrt;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
			    		// Serviço Insert
			    		$idInsert = UUID::v4();
			    		$arrInsert = array();
			    		$arrInsert['id'] = $idInsert;
			    		$arrInsert['descricao'] = 'Insert da TIB '.$tibModelo->nome.' id: '.$tibModelo->id. ' - Obs. Serviço gerado automáticamente pela aplicação';
			    		$arrInsert['metanome'] = 'HASH_INSERT_'.strtoupper($tibModelo->metanome);
			    		$arrInsert['nome'] = 'Inserir';
			    		$arrInsert['id_pai'] = $idCreate;
			    		$arrInsert['id_tib'] = $tibModelo->id;
			    		$arrInsert['visivel'] = 'f';
			    		$servico->insert($arrInsert);
				    		//Metadata de Insert
				    		//arquivo
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_arquivo';
				    		$arrMeta['valor'] = 'insertItemBiblioteca.php';
				    		$arrMeta['id_servico'] = $idInsert;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
				    		//target
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_target';
				    		$arrMeta['valor'] = $idIndx;
				    		$arrMeta['id_servico'] = $idInsert;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
				    		//comportamento
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_comportamento';
				    		$arrMeta['valor'] = 'formaction';
				    		$arrMeta['id_servico'] = $idInsert;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
				    		//show
				    		$arrMeta = array();
				    		$arrMeta['id'] = UUID::v4();
				    		$arrMeta['metanome'] = 'ws_show';
				    		$arrMeta['valor'] = 'ajax';
				    		$arrMeta['id_servico'] = $idInsert;
				    		$servicoMeta->insert($arrMeta);
				    		unset($arrMeta);
			    		//Metadata de Create
			    		//arquivo
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_arquivo';
			    		$arrMeta['valor'] = 'createItemBiblioteca.php';
			    		$arrMeta['id_servico'] = $idCreate;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
			    		//show
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_show';
			    		$arrMeta['valor'] = 'reload';
			    		$arrMeta['id_servico'] = $idCreate;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
			    		//comportamento
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_comportamento';
			    		$arrMeta['valor'] = 'action';
			    		$arrMeta['id_servico'] = $idCreate;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
		    		// Serviço List
		    		$idList = UUID::v4();
		    		$arrList = array();
		    		$arrList['id'] = $idList;
		    		$arrList['descricao'] = 'Listagem da TIB '.$tibModelo->nome.' id: '.$tibModelo->id. ' - Obs. Serviço gerado automáticamente pela aplicação';
		    		$arrList['metanome'] = 'HASH_LIST_'.strtoupper($tibModelo->metanome);
		    		$arrList['nome'] = 'Listar '.$tibModelo->nome;
		    		$arrList['id_pai'] = $idIndx;
		    		$arrList['id_tib'] = $tibModelo->id;
		    		$arrList['visivel'] = 'f';
		    		$servico->insert($arrList);
			    		//arquivo
			    		$arrMeta = array();
			    		$arrMeta['id'] = UUID::v4();
			    		$arrMeta['metanome'] = 'ws_arquivo';
			    		$arrMeta['valor'] = 'listItemBiblioteca.php';
			    		$arrMeta['id_servico'] = $idList;
			    		$servicoMeta->insert($arrMeta);
			    		unset($arrMeta);
			    	//Metadata de Index
			    	//arquivo
			    	$arrMeta = array();
			    	$arrMeta['id'] = UUID::v4();
			    	$arrMeta['metanome'] = 'ws_arquivo';
			    	$arrMeta['valor'] = 'indexItemBiblioteca.php';
			    	$arrMeta['id_servico'] = $idIndx;
			    	$servicoMeta->insert($arrMeta);
			    	unset($arrMeta);
			    	die('funcionou');
		    } catch(Zend_Exception $ex) {
		    	var_dump($dados);
		    	echo '<Br>-----------<Br>';
		    	var_dump($ex->getMessage());
		    	exit;
		    }
    	}else{
    		//futuramente colocar mensagem
    		die('não é uma master, não pode ter um serviço');
    	}
    }
}