<?php
class Config_ServiceController extends App_Controller_Action
{

 public function init()
    {
        parent::init();
        $this->_helper->layout()->setLayout('novo_hash');
    }

    /**
     * configuração padrão para definir o uso de twig em todas as actions do controller
     * @todo pode-se criar um App_controller_Action_Twig para facilitar isso
     */
    public function postDispatch()
    {
        $this->view->params = $this->getAllParams();
        $this->renderScript('twig.phtml');
        return parent::postDispatch();
    }
    /**
     * index: esta rota deve listar todos os servi�os em ordem alfab�tica, em forma de �rvore.
     * Deve utilizar o index.twig padr�o para exibi��o.
     * Esta exibi��o � paginada e utilizar� a rota /list para completar a listagem.
     */
    public function indexAction()
    {
    	//x($this->id_servico);
        $servicoBo = new Config_Model_Bo_Servico();
        $rowset = $servicoBo->find(null, 'nome', 30, 0);

        $header = array();
        $header[] = array('campo' => 'nome', 'label' => 'Nome');
        $header[] = array('campo' => 'metanome', 'label' => 'Metanome');

        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $rowset, 'header' => $header );
    }

    public function paginacaoAction()
    {
    	$total = $this->getParam('total');

    	$servicoBo = new Config_Model_Bo_Servico();

    	$rowset = $servicoBo->find(null, 'nome', 30, $total);
    	$this->_helper->json(array('data' => $rowset->toArray()));
    }

    /**
     * retorna o json da pagina��o da lista. Conforme funcionamento padr�o de um servi�o de pagina��o
     */
    public function listAction() {
    	//configurar pra pegar o servi�o
    	$identity = Zend_Auth::getInstance()->getIdentity();

    	$servicoBo	= new Config_Model_Bo_Servico();
    	$r			= $servicoBo->find("id_pai = '719cd4a3-4368-4b28-9b29-c21dee651540'");

    	$paginacao	= array();
    	foreach ($r->toArray() as $chave => $valor){
    		if ($valor['metanome'] === 'hash_listAction'){
    			$paginacao = $valor;

    			$servicoMetadataBo = new Config_Model_Bo_ServicoMetadata();
    			$row = $servicoMetadataBo->find("id_servico = '".$valor['id']."'");
    			foreach ($row->toArray() as $chaveMeta => $valorMeta) {
    				if ($valorMeta['metanome'] === 'ws_comportamento' && $valorMeta['valor'] === 'paginacao'){
    					$paginacao['metadata'] = $valorMeta;
    				}
    			}
    		}
    	}

    	return $paginacao;
    }

    /**
     * Fornece o formul�rio vazio para montagem pelo form.twig.html.
     * Todo o c�digo necess�rio para montagem da tela, exceto o twig j� est� escrito no arquivo /includes/createServico.php
     */
    public function createAction() {
		$header = array();
        $header[] = array('campo' => 'nome', 'label' => 'Nome');

        //$itemType = new Controller_Item_Type();
        //x($itemType->createServiceCampo('pessoa', '0', 'true', 'false', 'nome', 'Nome do Servi�o', 'NOME', 'text', 'HASH', array('ws_ordemLista' => '0', 'ws_style' => 'col-md-8')));

        $servicoDao = new Config_Model_Dao_Servico();
        $servicos 	=	$servicoDao-> fetch();
        $optionsServicoPai = array();
        foreach ($servicos as $chave_servico => $valor_servico){
        	$optionsServicoPai[$chave_servico]['id'] = $valor_servico['id'];
        	$optionsServicoPai[$chave_servico]['valor'] = $valor_servico['nome'];
        }

        $tib	 =	new Config_Model_Dao_Tib();
        $tibs	 =	$tib->fetch();
        $arrTibs	=	array();
        foreach ($tibs as $chave_tib => $valor_tib){
        		$arrTibs[$chave_tib]['id'] 	=  $valor_tib['id'];
        		$arrTibs[$chave_tib]['valor'] =  $valor_tib['nome'];
        }

        $tpMeta = new Config_Model_Dao_MetadataTemplate();
        $template	=	$tpMeta->fetchAll("aplicacao = 'servico'");

        $optionsTemplate = array();
        $i = 0;
        foreach ( $template as $chave_tp => $valor_tp){
        	$optionsTemplate[$i]['id'] = $valor_tp->metanome;
        	$optionsTemplate[$i]['valor'] = $valor_tp->nome;
        	$i++;
        }

        $optionsVisivel = array();
        $optionsVisivel[0]['id'] 	= 't';
        $optionsVisivel[0]['valor'] = 'Sim';
        $optionsVisivel[1]['id'] 	= 'f';
        $optionsVisivel[1]['valor'] = 'Não';

        $optionsComportamento = array();
        $optionsComportamento[0]['id']		=	'tab';
        $optionsComportamento[0]['valor']	=	'Tab';
        $optionsComportamento[1]['id']		=	'action';
        $optionsComportamento[1]['valor']	=	'Action';
        $optionsComportamento[2]['id']		=	'listaction';
        $optionsComportamento[2]['valor']	=	'List Action';
        $optionsComportamento[3]['id']		=	'formaction';
        $optionsComportamento[3]['valor']	=	'Form Action';
        $optionsComportamento[4]['id']		=	'pagination';
        $optionsComportamento[4]['valor']	=	'Pagination';


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
        				'ws_style'		=> 'col-md-3',
        		)
        );
        $arrCampos['dados'][1] = array(
        		'id'            => 'descricao',
        		'ordem'         => '0',
        		'obrigatorio'   => 'true',
        		'nome'          => 'Descrição',
        		'tipo'          => 'text',
        		'perfil'        => 'servico',
        		'metadatas'     => array(
        				'ws_ordemLista'         => '1',
        				'ws_style'              => 'col-md-3',
        				'ws_style_object'	=> 'select2-skin'
        		)
        );
        $arrCampos['dados'][2] = array(
        		'id'            => 'metanome',
        		'ordem'         => '0',
        		'obrigatorio'   => 'true',
        		'nome'          => 'Metanome',
        		'tipo'          => 'text',
        		'perfil'        => 'servico',
        		'metadatas'     => array(
        				'ws_ordemLista'         => '1',
        				'ws_style'              => 'col-md-3',
        				'ws_style_object'	=> 'select2-skin'
        		)
        );
        $arrCampos['dados'][3] = array(
        		'id' 		=> 'ordem',
        		'ordem' 	=> '0',
        		'obrigatorio' 	=> 'true',
        		'nome' 		=> 'Ordem',
        		'tipo'		=> 'text',
        		'perfil'	=> 'servico',
        		'metadatas'	=> array(
        				'ws_ordemLista'		=> '0',
        				'ws_style'		=> 'col-md-1',
        		)
        );
        $arrCampos['dados'][4] = array(
        		'id'            => 'visivel',
        		'ordem'         => '0',
        		'obrigatorio'   => 'true',
        		'nome'          => 'Visivel',
        		'metanome'		=> 'visivel',
        		'tipo'          => 'ref_itemBiblioteca',
        		'perfil'        => 'servico',
        		'items'			=> $optionsVisivel,
        		'metadatas'     => array(
        				'ws_ordemLista'         => '1',
        				'ws_style'              => 'col-md-1',
        				'ws_style_object'	=> 'select2-skin'
        		)
        );
        $arrCampos['dados'][5] = array(
        		'id'            => 'id',
        		'ordem'         => '0',
        		'obrigatorio'   => 'true',
        		'nome'          => 'Escolha a TIB',
        		'metanome'		=> 'tib',
        		'tipo'          => 'ref_itemBiblioteca',
        		'perfil'        => 'servico',
        		'items'			=> $arrTibs,
        		'metadatas'     => array(
        				'ws_ordemLista'         => '1',
        				'ws_style'              => 'col-md-3',
        				'ws_style_object'	=> 'select2-skin'
        		)
        );
        $arrCampos['dados'][6] = array(
        		'id'            => 'id',
        		'ordem'         => '0',
        		'obrigatorio'   => 'true',
        		'metanome'		=> 'pai',
        		'nome'          => 'Escolha um serviço pai',
        		'tipo'          => 'ref_itemBiblioteca',
        		'perfil'        => 'servico',
        		'items'			=> $optionsServicoPai,
        		'metadatas'     => array(
        				'ws_style'              => 'col-md-3',
        				'ws_style_object'	=> 'select2-skin'
        		)
        );
        $arrCampos['dados'][7] = array(
        		'id' 		=> 'rota',
        		'ordem' 	=> '0',
        		'obrigatorio' 	=> 'true',
        		'nome' 		=> 'Rota',
        		'tipo'		=> 'text',
        		'perfil'	=> 'servico',
        		'metadatas'	=> array(
        				'ws_ordemLista'		=> '0',
        				'ws_style'		=> 'col-md-3',
        		)
        );
        $arrCampos['dados'][8] = array(
        		'id'            => 'id',
        		'ordem'         => '0',
        		'obrigatorio'   => 'true',
        		'metanome'		=> 'template',
        		'nome'          => 'Escolha um Template',
        		'tipo'          => 'ref_itemBiblioteca',
        		'perfil'        => 'servico',
        		'items'			=> $optionsTemplate,
        		'metadatas'     => array(
        				'ws_style'              => 'col-md-3',
        				'ws_style_object'	=> 'select2-skin'
        		)
        );

        $arrPerfis = array();
        $arrPerfis[] = 'dados';
        $this->view->file = 'form.html.twig';
        $this->view->data = array('perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    /**
     * Salva o servi�o e os metadados gerados no create. A p�gina /includes/insertServico.php
     * faz esse processo completo, para refer�ncia. Final com flashmessage de sucesso.
     */
    public function insertAction() {
    	try {
	    	$dados	=	array();
	    	foreach ($_POST as $chave => $valor){
	    		if(!empty($valor)){
		    		if (substr($chave,-1) === '_'){
		    			$chave = substr($chave,0,-1);
		    		} elseif ($chave === 'visivel_visivel') {
		    			$chave = 'visivel';
		    		}

		    		if ( $chave !== 'id_template'){
		    			$dados[$chave] = $valor;
		    		}
	    		}
	    	}
	    	$dados['id'] = UUID::v4();

			if ( isset($dados['nome']) && isset($dados['descricao']) && isset($dados['visivel'])){
		    	$servico = new Config_Model_Dao_Servico();
		    	$servico->insert ( $dados );

		    	if ( !empty($_POST['id_template'])){
		    		$tp = new Config_Model_Dao_MetadataTemplate();
		    		$template = $tp->fetchOne("metanome = '".$_POST['id_template']."'");
		    		$rlTpMt = new Config_Model_Dao_RlMetadataTemplate();
		    		$relacao = $rlTpMt->fetchAll("id_template = '$template->id'");

		    		$servMeta	= new Config_Model_Dao_ServicoMetadata();
		    		$dadosMeta = array();
		    		foreach ( $relacao as $chave_rl => $valor_rl){
		    			$dadosMeta['id'] = UUID::v4();
		    			$dadosMeta['metanome'] = $valor_rl->id_metadata;
		    			$dadosMeta['id_servico'] = $dados['id'];
		    			if ( !is_null($valor_rl->padrao) ){
		    				$dadosMeta['valor']	   = $valor_rl->padrao;
		    			}
		    				$servMeta->insert ($dadosMeta);
		    		}
		    	}
		    	$this->_helper->FlashMessenger('Salvo com sucesso.');
			    print("<script> window.location=/config/service/retrieve/id/".$dados['id'].";</script>");
			} else {
				$this->_helper->FlashMessenger('Não salvo, os campos Nome, Descrição e Visível são obrigatórios.');
			}
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}

    }

    /**
     * Exibe o servi�o selecionado em index, com todos os metadata listados.
     * Pode incluir, remover ou modificar qualquer metadata.
     */
	public function retrieveAction() {

		$idServico = $this->getParam('id');

    	$servicoDao = new Config_Model_Dao_Servico();
        $dataService =	$servicoDao->fetchOne("id = '$idServico'");
        $optionsServicoPai = array();
        foreach ($servicos as $chave_servico => $valor_servico){
        	$optionsServicoPai[$chave_servico]['id'] = $valor_servico['id'];
        	$optionsServicoPai[$chave_servico]['valor'] = $valor_servico['nome'];
        }

        $tib	 =	new Config_Model_Dao_Tib();
        $tibs	 =	$tib->fetch();
        $arrTibs	=	array();
        foreach ($tibs as $chave_tib => $valor_tib){
        		$arrTibs[$chave_tib]['id'] 	=  $valor_tib['id'];
        		$arrTibs[$chave_tib]['valor'] =  $valor_tib['nome'];
        }

        $optionsVisivel = array();
        $optionsVisivel[0]['id'] 	= 't';
        $optionsVisivel[0]['valor'] = 'Sim';
        $optionsVisivel[1]['id'] 	= 'f';
        $optionsVisivel[1]['valor'] = 'Não';

        $optionsComportamento = array();
        $optionsComportamento[0]['id']		=	'tab';
        $optionsComportamento[0]['valor']	=	'Tab';
        $optionsComportamento[1]['id']		=	'action';
        $optionsComportamento[1]['valor']	=	'Action';
        $optionsComportamento[2]['id']		=	'listaction';
        $optionsComportamento[2]['valor']	=	'List Action';
        $optionsComportamento[3]['id']		=	'formaction';
        $optionsComportamento[3]['valor']	=	'Form Action';
        $optionsComportamento[4]['id']		=	'pagination';
        $optionsComportamento[4]['valor']	=	'Pagination';

        if ($dataService->visivel){
        	$visivel = 't';
        } else {
        	$visivel = 'f';
        }

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'valor'		=>	$dataService->nome,
    			'id' 		=> 'nome',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'nome',
    			//'descricao' 	=> 'Nome do servi�o',
    			'tipo'		=> 'text',
    			'perfil'	=> 'servico',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-3',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'valor'		=>	$dataService->descricao,
    			'id'            => 'descricao',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Descrição',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-3',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][2] = array(
    			'valor'		=>	$dataService->metanome,
    			'id'            => 'metanome',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Metanome',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-3',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][3] = array(
    			'valor'		=>	$dataService->ordem,
    			'id' 		=> 'ordem',
    			    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Ordem',
    			'tipo'		=> 'text',
    			'perfil'	=> 'servico',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-1',
    			)
    	);
    	$arrCampos['dados'][4] = array(
    			'valor'		=>	$visivel,
    			'id'            => 'visivel',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Visivel',
    			'metanome'		=> 'visivel',
    			'tipo'          => 'ref_itemBiblioteca',
    			'perfil'        => 'servico',
    			'items'			=> $optionsVisivel,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-1',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][5] = array(
    			'valor'		=>	$dataService->id_tib,
    			'id'            => 'id',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Escolha a TIB',
    			'metanome'		=> 'tib',
    			'tipo'          => 'ref_itemBiblioteca',
    			'perfil'        => 'servico',
    			'items'			=> $arrTibs,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-3',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][6] = array(
    			'valor'		=>	$dataService->id_pai,
    			'id'            => 'id',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'metanome'		=> 'pai',
    			'nome'          => 'Escolha um serviço pai',
    			'tipo'          => 'ref_itemBiblioteca',
    			'perfil'        => 'servico',
    			'items'			=> $optionsServicoPai,
    			'metadatas'     => array(
    					'ws_style'              => 'col-md-3',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][7] = array(
    			'valor'		=>	$dataService->rota,
    			'id' 		=> 'rota',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Rota',
    			'tipo'		=> 'text',
    			'perfil'	=> 'servico',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-3',
    			)
    	);

    	$servMeta	= new Config_Model_Dao_ServicoMetadata();
    	$arMeta		= $servMeta->fetchAll("id_servico = '$idServico'");
    	$is = 0;
    	foreach ($arMeta as $chave_meta => $valor_meta){
    		$arrCampos['metadatas'][$is] = array(
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
    		$is++;
    	}

    	$arrPerfis = array();
    	$arrPerfis[0] = 'dados';
    	if( $is > 0){
    		$arrPerfis[1] = 'metadatas';
    	}
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('id' => $idServico, 'perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    /**
     * Salva o servi�o e os metadados gerados no retrieve. Final com flashmessage de sucesso.
     */
    public function updateAction() {
    	try {
    		$meta = new Config_Model_Dao_ServicoMetadata();
    		$dadosMeta = array();
    		foreach ($_POST as $chave => $valor){
    			if (substr($chave,-4) === 'meta'){
    				$dadosMeta['valor'] = $valor;
    				$condicao = $meta->getAdapter ()->quoteInto ( 'id = ?', substr($chave,0,-5) );
    				$meta->update($dadosMeta, $condicao);
    				unset($_POST[$chave]);
    			}
    		}

	    	$dados	=	array();
	    	foreach ($_POST as $chave => $valor){
	    		if (substr($chave,-1) === '_'){
	    			$chave = substr($chave,0,-1);
	    		} elseif ($chave === 'visivel_visivel') {
	    			$chave = 'visivel';
	    		} elseif ( $chave === 'id_pai' && $valor === '') {
	    			$valor = NULL;
	    		} elseif ( $chave === 'id_tib' && $valor === '') {
	    			$valor = NULL;
	    		}

	    		$dados[$chave] = $valor;
	    	}
	    	$servico = new Config_Model_Dao_Servico();
	    	$condicao = $servico->getAdapter ()->quoteInto ( 'id = ?', $dados ['id'] );

    		return $servico->update ( $dados, $condicao );
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    /**
     * Ap�s utilizar o ws_confirm e ter autoriza��o do usu�rio, pode ser feita a remo��o do servi�o.
     * O servi�o n�o pode ser apagado se tiver filhos.
     * Todas as permiss�es do servi�o (rl_permissao_pessoa) devem ser apagados para aquele servi�o.
     */
    public function deleteAction() {
    	try {
	    	$idServico = $this->getParam('id');
	    	$rlPermissaoPessoa = new Config_Model_Dao_RlPermissaoPessoa();
	    	$rlGrupoServico		= new Config_Model_Dao_RlGrupoServico();
	    	$servico			= new Config_Model_Dao_Servico();
	    	$servicoMeta		= new Config_Model_Dao_ServicoMetadata();

	    	$filhos	= $servico->fetchAll("id_pai = '$idServico'");
	    	if( $filhos->count() == 0 ){
	    		$servicoMeta->delete("id_servico = '$idServico'");
	    		$servico->delete("id = '$idServico'");
	    	}else{
	    		foreach ($filhos as $chave_filho => $valor_filho){
	    			$rlGrupoServico->delete("id_servico = '".$valor_filho->id."'");
	    			$rlPermissaoPessoa->delete("id_servico = '".$valor_filho->id."'");
	    		}
	    	}

	    	$rlGrupoServico->delete("id_servico = '$idServico'");
	    	$rlPermissaoPessoa->delete("id_servico = '$idServico'");
    	}catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}

    }

    /**
     * deve permitir a pesquisa por AJAX do nome ou metanome do servi�o para retorno ao Select2.
     */
    public function filterAction() {

    }

    public function createMetadataAction(){

    	$tpMeta = new Config_Model_Dao_MetadataTemplate();
    	$template = $tpMeta->fetchOne("nome = 'create'");

    	$rlTpMeta = new Config_Model_Dao_RlMetadataTemplate();
    	$relacao  = $rlTpMeta->fetchAll("id_template = '".$template->id."'");

    	$meta	= new Config_Model_Dao_Metadata();

    	$arrCampos = array();
    	$i = 0;
    	foreach ( $relacao as $chave => $valor){
    		$metaForm = $meta->fetchOne("id = '$valor->id_metadata'");

    		$arrCampos['Metadatas'][$i] = array(
    			'valor'		=>	$valor->padrao,
    			'id' 		=> $metaForm->id,
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> $metaForm->id,
    			//'descricao' 	=> 'Nome do servi�o',
    			'tipo'		=> 'text',
    			'perfil'	=> 'servico',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-3',
    			)
    		);
    		$i++;
    	}
    	$arrCampos['Metadatas'][$i] = array(
    			'valor'		=>	'sou a chave',
    			'id' 		=> 'id_servico',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'meta_nome' 		=> 'servico',
    			//'descricao' 	=> 'Nome do servi�o',
    			'tipo'		=> 'hidden',
    			'perfil'	=> 'servico',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-3',
    			)
    	);

    	$arrPerfis = array();
    	$arrPerfis[] = 'Metadatas';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function insertMetadatasAction(){
    	x($_POST);
    	$meta	= new Config_Model_Dao_ServicoMetadata();
    	$dados	=	array();
    	foreach ($_POST as $chave => $valor){
    		if (substr($chave,-1) === '_'){
    			$chave = substr($chave,0,-1);
    		}

    		if (substr($chave,0,2) === 'ws'){
    			$dados['id']	= UUID::v4();
    			$dados['metanome'] = $chave;
    			$dados['valor']	=$valor;
    			$dados['id_servico'] = $_POST['id_servico_'];

    			$meta->createRow($dados);
    		}
    	}

    }
}