<?php
class Config_InformacaoController extends App_Controller_Action_Twig
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
    	$informacaoBo = new Config_Model_Bo_Informacao();

        $rowset1 = $informacaoBo->find("tipo ilike 'master'", 'nome', 30, 0);
        $rowset2 = $informacaoBo->find("tipo not ilike 'master'", 'nome', 30, 0);

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
        $header[] = array('campo' => 'metanome', 'label' => 'Meta Nome');
        $header[] = array('campo' => 'tipo', 'label' => 'Tipo');

        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $rowset, 'header' => $header );
    }

    public function listAction()
    {
    	$total = $this->getParam('total');

    	$informacaoBo = new Config_Model_Bo_Informacao();

    	$rowset = $informacaoBo->find(null, 'nome', 30, $total);

    	$this->_helper->json(array('data' => $rowset->toArray()));
    }


    public function createAction(){

    	$header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

    	$informacao = new Config_Model_Dao_Informacao();
    	$select = $informacao->select()
    			->distinct()
    			->from(array('tp_info' => 'tp_informacao'), 'tipo');
    	$stmt = $select->query();
    	$result = $stmt->fetchAll();

    	$optionsTipo = array();
    	$i = 0;
    	foreach ($result as $chave_tipo => $valor_tipo){
    		if ( !is_null($valor_tipo['tipo'])){
	    		$optionsTipo[$i]['id'] = $valor_tipo['tipo'];
	    		$optionsTipo[$i]['valor'] = $valor_tipo['tipo'];
	    		$i++;
    		}
    	}

    	$arrInformacao = $informacao->fetchAll();
    	$i = 0;
    	$optionsInfo = array();
    	foreach ($arrInformacao as $chave_info => $valor_info){
    		$optionsInfo[$i]['id']	=	$valor_info->id;
    		$optionsInfo[$i]['valor']	=	$valor_info->nome;
    		$i++;
    	}

    	$pf = new Config_Model_Dao_Perfil();

    	$select = $pf->select()
    	->distinct()
    	->from(array('perfil' => 'tb_perfil'), 'nome');
    	$stmt = $select->query();
    	$result = $stmt->fetchAll();

    	$arrOpcPerfil = array();
    	$i = 0;
    	foreach ($result as $k => $v){
    		$perfil = $pf->fetchRow("nome = '".$v['nome']."'");
    		$arrOpcPerfil[$i]['id'] = $perfil->id;
    		$arrOpcPerfil[$i]['valor'] = $v['nome'];
    		$i++;
    	}

    	$optionsVisivel = array();
    	$optionsVisivel[0]['id'] 	= 't';
    	$optionsVisivel[0]['valor'] = 'Sim';
    	$optionsVisivel[1]['id'] 	= 'f';
    	$optionsVisivel[1]['valor'] = 'Não';

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
    	$arrCampos['dados'][2] = array(
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
    	$arrCampos['dados'][3] = array(
    			'id'            => 'ordem',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Ordem',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-1',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][4] = array(
    			'id'            => 'mascara',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Mascara',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][5] = array(
    			'id'            => 'tamanho',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tamanho',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-3',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][6] = array(
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
    	$arrCampos['dados'][7] = array(
    			'id'            => 'tipo',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tipo',
    			'metanome'		=> 'tipo',
    			'tipo'          => 'ref_itemBiblioteca',
    			'perfil'        => 'servico',
    			'items'			=> $optionsTipo,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][8] = array(
    			'id'            => 'id',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Informação Pai',
    			'metanome'		=> 'pai',
    			'tipo'          => 'ref_itemBiblioteca',
    			'perfil'        => 'servico',
    			'items'			=> $optionsInfo,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-3',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][9] = array(
    			'id'            => 'id',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Perfil',
    			'tipo'          => 'ref_itemBiblioteca',
    			'metanome'		=> 'perfil',
    			'perfil'        => 'servico',
    			'items'			=> $arrOpcPerfil,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-3',
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

    		if (!empty($_POST['id_perfil'])){
    			$idPerfil = $_POST['id_perfil'];
    		}
    		unset($_POST['id_perfil']);
	    	$dados = array();
	    	foreach ($_POST as $chave => $valor){
	    		if (substr($chave,-1) === '_'){
	    			$chave = substr($chave,0,-1);
	    		} elseif ( $chave == 'tipo_tipo' ) {
	    			$chave = 'tipo';
	    		} elseif ( $chave == 'visivel_visivel' ){
	    			$chave = 'visivel';
	    		}
	    		$dados[$chave] = $valor;
	    	}
	    	$dados['id'] = UUID::v4();

    		if (strtoupper ($dados['tipo']) == 'MASTER' && empty($dados['id_pai'])){
	    		unset($dados['id_pai']);
	    	}

	    	$informacao = new Config_Model_Dao_Informacao();
	    	$informacao->insert($dados);

	    	if (isset($idPerfil)){
		    	$rl = array();
		    	$rl['id'] = UUID::v4();
		    	$rl['id_perfil'] = $idPerfil;
		    	$rl['id_informacao'] = $dados['id'];
		    	$rl['obrigatorio'] = 'f';
		    	$rl['multiplo'] = 'f';
		    	$rl['pesquisa'] = 'f';
		    	$rl['filtro'] = 'f';
		    	$rl['lista'] = 'f';
		    	$relacao = new Config_Model_Dao_RlPerfilInformacao();
		    	$relacao->insert($rl);
	    	}

    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function retrieveAction(){
    	$idInfo = $this->getParam('id');

    	$informacao = new Config_Model_Dao_Informacao();
    	$dataInformacao = $informacao->fetchRow("id = '$idInfo'");

    	$informacao = new Config_Model_Dao_Informacao();
    	$select = $informacao->select()
    			->distinct()
    			->from(array('tp_info' => 'tp_informacao'), 'tipo');
    	$stmt = $select->query();
    	$result = $stmt->fetchAll();

    	$optionsTipo = array();
    	$i = 0;
    	foreach ($result as $chave_tipo => $valor_tipo){
    		if ( !is_null($valor_tipo['tipo'])){
	    		$optionsTipo[$i]['id'] = $valor_tipo['tipo'];
	    		$optionsTipo[$i]['valor'] = $valor_tipo['tipo'];
	    		$i++;
    		}
    	}

    	$arrInformacao = $informacao->fetchAll();
    	$i = 0;
    	$optionsInfo = array();
    	foreach ($arrInformacao as $chave_info => $valor_info){
    		$optionsInfo[$i]['id']	=	$valor_info->id;
    		$optionsInfo[$i]['valor']	=	$valor_info->nome;
    		$i++;
    	}

    	$pf = new Config_Model_Dao_Perfil();

    	$select = $pf->select()
    	->distinct()
    	->from(array('perfil' => 'tb_perfil'), 'nome');
    	$stmt = $select->query();
    	$result = $stmt->fetchAll();

    	$arrOpcPerfil = array();
    	$i = 0;
    	foreach ($result as $k => $v){
    		$perfil = $pf->fetchRow("nome = '".$v['nome']."'");
    		$arrOpcPerfil[$i]['id'] = $perfil->id;
    		$arrOpcPerfil[$i]['valor'] = $v['nome'];
    		$i++;
    	}

    	$optionsVisivel = array();
    	$optionsVisivel[0]['id'] 	= 't';
    	$optionsVisivel[0]['valor'] = 'Sim';
    	$optionsVisivel[1]['id'] 	= 'f';
    	$optionsVisivel[1]['valor'] = 'Não';

    	if ( $dataInformacao->visivel ){
    		$visivel = 't';
    	} else {
    		$visivel = 'f';
    	}

    	$rl = new Config_Model_Dao_RlPerfilInformacao();
    	$relacao = $rl->fetchRow("id_informacao = '$idInfo'");
    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'nome',
    			'ordem' 	=> '0',
    			'valor'		=> $dataInformacao->nome,
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
    			'id'            => 'metanome',
    			'ordem'         => '0',
    			'valor'			=> $dataInformacao->metanome,
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
    	$arrCampos['dados'][2] = array(
    			'id'            => 'descricao',
    			'ordem'         => '0',
    			'valor'			=> $dataInformacao->descricao,
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
    	$arrCampos['dados'][3] = array(
    			'id'            => 'ordem',
    			'ordem'         => '0',
    			'valor'			=> $dataInformacao->ordem,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Ordem',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-1',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][4] = array(
    			'id'            => 'mascara',
    			'ordem'         => '0',
    			'valor'			=> $dataInformacao->mascara,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Mascara',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][5] = array(
    			'id'            => 'tamanho',
    			'ordem'         => '0',
    			'valor'			=> $dataInformacao->tamanho,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tamanho',
    			'tipo'          => 'text',
    			'perfil'        => 'servico',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-3',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][6] = array(
    			'id'            => 'visivel',
    			'ordem'         => '0',
    			'valor'			=> $visivel,
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
    	$arrCampos['dados'][7] = array(
    			'id'            => 'tipo',
    			'ordem'         => '0',
    			'valor'			=> $dataInformacao->tipo,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Tipo',
    			'metanome'		=> 'tipo',
    			'tipo'          => 'ref_itemBiblioteca',
    			'perfil'        => 'servico',
    			'items'			=> $optionsTipo,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-2',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][8] = array(
    			'id'            => 'id',
    			'ordem'         => '0',
    			'valor'			=> $dataInformacao->id_pai,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Informação Pai',
    			'metanome'		=> 'pai',
    			'tipo'          => 'ref_itemBiblioteca',
    			'perfil'        => 'servico',
    			'items'			=> $optionsInfo,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-3',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);
    	$arrCampos['dados'][9] = array(
    			'id'            => 'id',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Perfil',
    			'tipo'          => 'ref_itemBiblioteca',
    			'metanome'		=> 'perfil',
    			'perfil'        => 'servico',
    			'items'			=> $arrOpcPerfil,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-3',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);

    	if (!is_null($relacao)){
    		$arrCampos['dados'][9]['valor']	= $relacao->id_perfil;
    	}

    	$infoMeta	= new Config_Model_Dao_InformacaoMetadata();
    	$arMeta		= $infoMeta->fetchAll("id_tpinfo = '".$dataInformacao->id."'");
    	$i = 0;
    	foreach ($arMeta as $chave_meta => $valor_meta){
    		$arrCampos['metadatas'][$i] = array(
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
    		$i++;
    	}

    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	if (isset($arrCampos['metadatas'])){
    		$arrPerfis[] = 'metadatas';
    	}
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('id' => $idInfo, 'perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function updateAction(){
    	try {

    		if (!empty($_POST['id_perfil'])){
    			$idPerfil = $_POST['id_perfil'];
    		}
    		unset($_POST['id_perfil']);

    		$meta = new Config_Model_Dao_InformacaoMetadata();
    		$dadosMeta = array();
    		foreach ($_POST as $chave => $valor){
    			if (substr($chave,-4) === 'meta'){
    				$dadosMeta['valor'] = $valor;
    				$condicao = $meta->getAdapter ()->quoteInto ( 'id = ?', substr($chave,0,-5) );
    				$meta->update($dadosMeta, $condicao);
    				unset($_POST[$chave]);
    			}
    		}

	    	$dados = array();
		    foreach ($_POST as $chave => $valor){
		    if (substr($chave,-1) === '_'){
	    			$chave = substr($chave,0,-1);
	    		} elseif ( $chave == 'tipo_tipo' ) {
	    			$chave = 'tipo';
	    		} elseif ( $chave == 'visivel_visivel' ){
	    			$chave = 'visivel';
	    		}
	    		if(!empty($valor)){
		    		$dados[$chave] = $valor;
	    		}
		    }

		    if (strtoupper ($dados['tipo']) == 'MASTER' && empty($dados['id_pai'])){
		    	unset($dados['id_pai']);
		    }

	    	$informacao = new Config_Model_Dao_Informacao();
	    	$condicao = $informacao->getAdapter ()->quoteInto ( 'id = ?', $dados ['id'] );
	    	$informacao->update($dados, $condicao);

	    	if (isset($idPerfil)){

	    		$relacao = new Config_Model_Dao_RlPerfilInformacao();
	    		$relacao->delete("id_informacao = '".$dados ['id']."'");

	    		$rl = array();
	    		$rl['id'] = UUID::v4();
	    		$rl['id_perfil'] = $idPerfil;
	    		$rl['id_informacao'] = $dados['id'];
	    		$rl['obrigatorio'] = 'f';
	    		$rl['multiplo'] = 'f';
	    		$rl['pesquisa'] = 'f';
	    		$rl['filtro'] = 'f';
	    		$rl['lista'] = 'f';
	    		$relacao->insert($rl);
	    	}
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function deleteAction(){
    	try {
	    	$idInformacao = $this->getParam('id');

	    	$perfilInformacao =	new Config_Model_Dao_RlPerfilInformacao();
	    	$perfilInformacao->delete("id_informacao = '$idInformacao'");

    		$informacao = new Config_Model_Dao_Informacao();
    		$informacao->delete("id = '$idInformacao'");

    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }
}