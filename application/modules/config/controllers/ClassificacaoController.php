<?php
class Config_ClassificacaoController extends App_Controller_Action
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
    	$classificacaoBo = new Config_Model_Bo_Classificacao();
        $rowset = $classificacaoBo->find(null, 'nome', 30, 0);

        $header = array();
        $header[] = array('campo' => 'nome', 'label' => 'Nome');
		/*
        $header[] = array('campo' => 'perfil', 'label' => 'Perfil');

        $data = array();
        $i = 0;
        foreach ($rowset as $k => $v){
        	$rlPfClas = new Config_Model_Dao_RlPerfilClassificacao();
        	$perfil = new Config_Model_Dao_Perfil();
        	$data[$i] = $v;
        	$data[$i]['perfil'] = $perfil->fetchRow("id = '".$rlPfClas->fetchRow("id_classificacao = '".$v->id."'")->id_perfil."'")->nome;
        	$i++;
        }
        */
        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $rowset, 'header' => $header );
    }

 	public function listAction()
    {
    	$total = $this->getParam('total');

    	$classificacaoBo = new $classificacaoBo();

    	$rowset = $classificacaoBo->find(null, 'nome', 30, $total);

    	$this->_helper->json(array('data' => $rowset->toArray()));
    }


    public function createAction(){
    	$header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

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
    			'id'            => 'perfil',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Perfil',
    			'tipo'          => 'ref_itemBiblioteca',
    			'metanome'		=> 'tipo',
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

			if(!empty($dados['nome']) && !empty($dados['metanome']) && !empty($dados['perfil_tipo'])){
		    	$idPerfil = $dados['perfil_tipo'];
		    	unset($dados['perfil_tipo']);

				$classificacao = new Config_Model_Dao_Classificacao();
		    	$classificacao->insert($dados);

		    	$relacao = array();
		    	$relacao['id'] = UUID::v4();
		    	$relacao['id_perfil'] = $idPerfil;
		    	$relacao['id_classificacao'] = $dados['id'];
		    	$rlPerfilClass = new Config_Model_Dao_RlPerfilClassificacao();
		    	$rlPerfilClass->insert($relacao);
			}else{
				//colocar mensagem de erro.
			}
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function retrieveAction(){
    	$idClassificacao = $this->getParam('id');

    	$classificacao = new Config_Model_Dao_Classificacao();
    	$dataClassificacao = $classificacao->fetchRow("id = '$idClassificacao'");

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

    	$rl = new Config_Model_Dao_RlPerfilClassificacao();
    	$relacao = $rl->fetchRow("id_classificacao = '$idClassificacao'");
    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'nome',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'nome',
    			'valor'		=> $dataClassificacao->nome,
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
    			'valor'		=> $dataClassificacao->metanome,
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
    			'id'            => 'perfil',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'valor'			=> $relacao->id_perfil,
    			'nome'          => 'Perfil',
    			'tipo'          => 'ref_itemBiblioteca',
    			'metanome'		=> 'tipo',
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
    	$this->view->data = array('id' => $idClassificacao, 'perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function updateAction(){
    	try {
	    	$dados = array();
		    foreach ($_POST as $chave => $valor){
		    	if (substr($chave,-1) === '_'){
		    		$chave = substr($chave,0,-1);
		    	}
		    	$dados[$chave] = $valor;
		    }
		    if(!empty($dados['nome']) && !empty($dados['metanome']) && !empty($dados['perfil_tipo'])){
		    	$idPerfil = $dados['perfil_tipo'];
		    	unset($dados['perfil_tipo']);

		    	$perfilclassificacao =	new Config_Model_Dao_RlPerfilClassificacao();
		    	$perfilclassificacao->delete("id_classificacao = '".$dados ['id']."'");

		    	$relacao = array();
		    	$relacao['id'] = UUID::v4();
		    	$relacao['id_perfil'] = $idPerfil;
		    	$relacao['id_classificacao'] = $dados['id'];
		    	$rlPerfilClass = new Config_Model_Dao_RlPerfilClassificacao();
		    	$rlPerfilClass->insert($relacao);

		    	$classificacao = new Config_Model_Dao_Classificacao();
		    	$condicao = $classificacao->getAdapter ()->quoteInto ( 'id = ?', $dados ['id'] );
		    	return $classificacao->update($dados, $condicao);
		    	//mensagem de sucesso
		    }else{
		    	//colocar mensagem de erro.
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
	    	$idClassificacao = $this->getParam('id');

	    	$perfilclassificacao =	new Config_Model_Dao_RlPerfilClassificacao();
    		$classificacao = new Config_Model_Dao_Classificacao();

    		$perfilclassificacao->delete("id_classificacao = '$idClassificacao'");
    		$classificacao->delete("id = '$idClassificacao'");
			//add mensagem
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function gerarServicoAction(){
    	$idClass = $this->getParam('id');
    	$c = new Config_Model_Dao_Classificacao();
    	$class = $c->fetchRow("id = '$idPerfil'");

    	try {
    		$servico = new Config_Model_Dao_Servico();
    		$servicoMeta = new Config_Model_Dao_ServicoMetadata();
    		//criar servicos
    		// Serviço index
    		$idIndx = UUID::v4();
    		$arrIndex = array();
    		$arrIndex['id'] = $idIndx;
    		$arrIndex['descricao'] = 'Index do Perfil '.$class->nome.' - Obs. Serviço gerado automáticamente pela aplicação';
    		$arrIndex['metanome'] = 'HASH_INDEX_CLASSIFICACAO_'.strtoupper($class->metanome);
    		$arrIndex['nome'] = $class->nome;
    		$arrIndex['id_pai'] = $servico->fetchRow("descricao = 'Gerenciar Pessoas'")->id;
    		$arrIndex['visivel'] = 't';
    		$servico->insert($arrIndex);
    		// Serviço Delete
    		$idDel = UUID::v4();
    		$arrDel = array();
    		$arrDel['id'] = $idDel;
    		$arrDel['descricao'] = 'Deletar '.$class->nome.' - Obs. Serviço gerado automáticamente pela aplicação';
    		$arrDel['metanome'] = 'HASH_DELETAR_'.strtoupper($class->metanome);
    		$arrDel['nome'] = 'Deletar '.$class->nome;
    		$arrDel['id_pai'] = $idIndx;
    		$arrDel['visivel'] = 'f';
    		$servico->insert($arrDel);
    		// Metadados de Delete
    		//perfil
    		$arrMeta = array();
    		$arrMeta['id'] = UUID::v4();
    		$arrMeta['metanome'] = 'ws_perfil';
    		$arrMeta['valor'] = strtoupper($class->metanome);
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
    		//show
    		$arrMeta = array();
    		$arrMeta['id'] = UUID::v4();
    		$arrMeta['metanome'] = 'ws_show';
    		$arrMeta['valor'] = 'ajax';
    		$arrMeta['id_servico'] = $idDel;
    		$servicoMeta->insert($arrMeta);
    		unset($arrMeta);
    		//arquivo
    		$arrMeta = array();
    		$arrMeta['id'] = UUID::v4();
    		$arrMeta['metanome'] = 'ws_arquivo';
    		$arrMeta['valor'] = 'deletePessoa.php';
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
    		//classificacao
    		$arrMeta = array();
    		$arrMeta['id'] = UUID::v4();
    		$arrMeta['metanome'] = 'ws_arquivo';
    		$arrMeta['valor'] = 'filterItemBiblioteca.php';
    		$arrMeta['id_servico'] = $idAjxFilterEdt;
    		$servicoMeta->insert($arrMeta);
    		unset($arrMeta);
    		//icone
    		$arrMeta = array();
    		$arrMeta['id'] = UUID::v4();
    		$arrMeta['metanome'] = 'ws_arquivo';
    		$arrMeta['valor'] = 'filterItemBiblioteca.php';
    		$arrMeta['id_servico'] = $idAjxFilterEdt;
    		$servicoMeta->insert($arrMeta);
    		unset($arrMeta);

    		// Serviço Edit
    		$idEdt = UUID::v4();
    		$arrEdt = array();
    		$arrEdt['id'] = $idEdt;
    		$arrEdt['descricao'] = 'Editar '.$class->nome.' - Obs. Serviço gerado automáticamente pela aplicação';
    		$arrEdt['metanome'] = 'HASH_EDITAR_'.strtoupper($class->metanome);
    		$arrEdt['nome'] = 'Editar '.$class->nome;
    		$arrEdt['id_pai'] = $idIndx;
    		$arrEdt['visivel'] = 'f';
    		$servico->insert($arrEdt);
    		// Serviço Ajax filtro Edit
    		$idAjxSalvarEdt = UUID::v4();
    		$arrAjxSalvarEdt = array();
    		$arrAjxSalvarEdt['id'] = $idAjxSalvarEdt;
    		$arrAjxSalvarEdt['descricao'] = 'Ajax para salvar edição do perfil '.$class->nome.' - Obs. Serviço gerado automáticamente pela aplicação';
    		$arrAjxSalvarEdt['metanome'] = 'HASH_AJAX_SALVAR_EDICAO_'.strtoupper($class->metanome);
    		$arrAjxSalvarEdt['nome'] = 'Salvar';
    		$arrAjxSalvarEdt['id_pai'] = $idEdt;
    		$arrAjxSalvarEdt['visivel'] = 'f';
    		$servico->insert($arrAjxSalvarEdt);
    		// Serviço Ajax filtro Edit
    		$idAjxFilterEdt = UUID::v4();
    		$arrAjxFilterEdt = array();
    		$arrAjxFilterEdt['id'] = $idAjxFilterEdt;
    		$arrAjxFilterEdt['descricao'] = 'Ajax filtro do perfil '.$class->nome.' - Obs. Serviço gerado automáticamente pela aplicação';
    		$arrAjxFilterEdt['metanome'] = 'HASH_AJAX_FILTRAR_'.strtoupper($class->metanome);
    		$arrAjxFilterEdt['nome'] = 'Ajax Filtrar '.$class->nome;
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
    		$arrCreate['descricao'] = 'Incuir Novo '.$class->nome.' - Obs. Serviço gerado automáticamente pela aplicação';
    		$arrCreate['metanome'] = 'HASH_CRIAR_'.strtoupper($class->metanome);
    		$arrCreate['nome'] = 'Incuir Novo '.$class->nome;
    		$arrCreate['id_pai'] = $idIndx;
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
    		$arrList['descricao'] = 'Ajax de listagem de '.$class->nome.' - Obs. Serviço gerado automáticamente pela aplicação';
    		$arrList['metanome'] = 'HASH_LIST_'.strtoupper($class->metanome);
    		$arrList['nome'] = 'AJAX Listar '.$class->nome;
    		$arrList['id_pai'] = $idIndx;
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
    }
}