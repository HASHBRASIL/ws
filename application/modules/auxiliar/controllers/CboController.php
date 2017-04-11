<?php
class Auxiliar_CboController extends App_Controller_Action
{

 	public function init()
    {
        parent::init();
        $this->_helper->layout()->setLayout('novo_hash');
    }

    public function postDispatch()
    {
        $this->view->params = $this->getAllParams();
        $this->renderScript('twig.phtml');
        return parent::postDispatch();
    }

    public function indexFamiliaAction()
    {
    	$bo = new Auxiliar_Model_Bo_Familia();
        $rowset = $bo->find();//null, 'nome', 30, 0);

        $rowset2 = array();
        $i = 0;
		foreach ($rowset as $k => $v){
			$rowset2[$i]['id'] 		= $v->codigo;
			$rowset2[$i]['codigo']	= $v->codigo;
			$rowset2[$i]['titulo']	= $v->titulo;
			$i++;
		}
        $header = array();
        $header[] = array('campo' => 'codigo', 'label' => 'Código');
        $header[] = array('campo' => 'titulo', 'label' => 'Título');
        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $rowset2, 'header' => $header );
    }

    public function indexGrandeGrupoAction()
    {
    	$bo = new Auxiliar_Model_Bo_GrandeGrupo();
        $rowset = $bo->find();//null, 'nome', 30, 0);

        $rowset2 = array();
        $i = 0;
		foreach ($rowset as $k => $v){
			$rowset2[$i]['id'] 		= $v->codigo;
			$rowset2[$i]['codigo']	= $v->codigo;
			$rowset2[$i]['titulo']	= $v->titulo;
			$i++;
		}
        $header = array();
        $header[] = array('campo' => 'codigo', 'label' => 'Código');
        $header[] = array('campo' => 'titulo', 'label' => 'Título');
        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $rowset2, 'header' => $header );
    }

    public function indexOcupacaoAction()
    {
    	$bo = new Auxiliar_Model_Bo_Ocupacao();
        $rowset = $bo->find();//null, 'nome', 30, 0);

        $rowset2 = array();
        $i = 0;
		foreach ($rowset as $k => $v){
			$rowset2[$i]['id'] 		= $v->codigo;
			$rowset2[$i]['codigo']	= $v->codigo;
			$rowset2[$i]['titulo']	= $v->titulo;
			$i++;
		}
        $header = array();
        $header[] = array('campo' => 'codigo', 'label' => 'Código');
        $header[] = array('campo' => 'titulo', 'label' => 'Título');
        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $rowset2, 'header' => $header );
    }

    public function indexSinonimoAction()
    {
    	$bo = new Auxiliar_Model_Bo_Sinonimo();
        $rowset = $bo->find();//null, 'nome', 30, 0);

        $rowset2 = array();
        $i = 0;
		foreach ($rowset as $k => $v){
			$rowset2[$i]['id'] 		= $v->ocupacao;
			$rowset2[$i]['ocupacao']	= $v->ocupacao;
			$rowset2[$i]['titulo']	= $v->titulo;
			$i++;
		}
        $header = array();
        $header[] = array('campo' => 'ocupacao', 'label' => 'Ocupação');
        $header[] = array('campo' => 'titulo', 'label' => 'Título');
        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $rowset2, 'header' => $header );
    }

    public function indexSubgrupoAction()
    {
    	$bo = new Auxiliar_Model_Bo_Subgrupo();
        $rowset = $bo->find();//null, 'nome', 30, 0);

        $rowset2 = array();
        $i = 0;
		foreach ($rowset as $k => $v){
			$rowset2[$i]['id'] 		= $v->codigo;
			$rowset2[$i]['codigo']	= $v->codigo;
			$rowset2[$i]['titulo']	= $v->titulo;
			$i++;
		}
        $header = array();
        $header[] = array('campo' => 'codigo', 'label' => 'Código');
        $header[] = array('campo' => 'titulo', 'label' => 'Título');
        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $rowset2, 'header' => $header );
    }

    public function indexSubgrupoPrincipalAction()
    {
    	$bo = new Auxiliar_Model_Bo_SubgrupoPrincipal();
        $rowset = $bo->find();//null, 'nome', 30, 0);

        $rowset2 = array();
        $i = 0;
		foreach ($rowset as $k => $v){
			$rowset2[$i]['id'] 		= $v->codigo;
			$rowset2[$i]['codigo']	= $v->codigo;
			$rowset2[$i]['titulo']	= $v->titulo;
			$i++;
		}
        $header = array();
        $header[] = array('campo' => 'codigo', 'label' => 'Código');
        $header[] = array('campo' => 'titulo', 'label' => 'Título');
        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $rowset2, 'header' => $header );
    }

    public function editarFamiliaAction()
    {
    	$id = $this->getParam('id');
    	$obj = new Auxiliar_Model_Dao_Familia();
    	$dados = $obj->fetchOne("codigo = '$id'");

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'codigo',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Código',
    			'valor'		=> $dados->codigo,
    			'tipo'		=> 'text',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'titulo',
    			'ordem'         => '0',
    			'valor'		=> $dados->titulo,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Título',
    			'tipo'          => 'text',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);

    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('id' => $id, 'perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function updateFamiliaAction()
    {
    	try {
    		$dados = array();

    		$dados['codigo'] = $_POST['codigo_'];
    		$dados['titulo'] = $_POST['titulo_'];

   			$obj = new Auxiliar_Model_Dao_Familia();
   			$condicao = $obj->getAdapter()->quoteInto ( 'codigo = ?', $dados['codigo'] );
   			return $obj->update($dados, $condicao);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function criarFamiliaAction()
    {
    	$header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'codigo',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Código',
    			//'descricao' 	=> 'Nome do servi�o',
    			'tipo'		=> 'text',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'titulo',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Título',
    			'tipo'          => 'text',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6'
    			)
    	);
    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function insertFamiliaAction()
    {
    	try {
    		unset($_POST['id']);
    		$dados = array();
    		foreach ($_POST as $chave => $valor){
   				$chave = substr($chave,0,-1);
    			$dados[$chave] = $valor;
    		}
			$obj = new Auxiliar_Model_Dao_Familia();
			$obj->insert($dados);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function deleteFamiliaAction()
    {
    	try {
    		$id = $this->getParam('id');

    		$obj =	new Auxiliar_Model_Dao_Familia();
    		$obj->delete("codigo = '$id'");
    		//add mensagem
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function editarGrandeGrupoAction()
    {
    	$id = $this->getParam('id');
    	$obj = new Auxiliar_Model_Dao_GrandeGrupo();
    	$dados = $obj->fetchOne("codigo = '$id'");

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'codigo',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Código',
    			'valor'		=> $dados->codigo,
    			'tipo'		=> 'text',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'titulo',
    			'ordem'         => '0',
    			'valor'		=> $dados->titulo,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Título',
    			'tipo'          => 'text',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);

    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('id' => $id, 'perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function updateGrandeGrupoAction()
    {
    	try {
    		$dados = array();

    		$dados['codigo'] = $_POST['codigo_'];
    		$dados['titulo'] = $_POST['titulo_'];

    		$obj = new Auxiliar_Model_Dao_GrandeGrupo();
    		$condicao = $obj->getAdapter()->quoteInto ( 'codigo = ?', $dados['codigo'] );
    		return $obj->update($dados, $condicao);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}

    }

    public function criarGrandeGrupoAction()
    {
    	$header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'codigo',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Código',
    			//'descricao' 	=> 'Nome do servi�o',
    			'tipo'		=> 'text',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'titulo',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Título',
    			'tipo'          => 'text',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6'
    			)
    	);
    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function insertGrandeGrupoAction()
    {
    	try {
    		unset($_POST['id']);
    		$dados = array();
    		foreach ($_POST as $chave => $valor){
    			$chave = substr($chave,0,-1);
    			$dados[$chave] = $valor;
    		}
    		$obj = new Auxiliar_Model_Dao_GrandeGrupo();
    		$obj->insert($dados);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function deletarGrandeGrupoAction()
    {
    	try {
    		$id = $this->getParam('id');

    		$obj =	new Auxiliar_Model_Dao_GrandeGrupo();
    		$obj->delete("codigo = '$id'");
    		//add mensagem
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}

    }

    public function editarOcupacaoAction()
    {
    	$id = $this->getParam('id');
    	$obj = new Auxiliar_Model_Dao_Ocupacao();
    	$dados = $obj->fetchOne("codigo = '$id'");

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'codigo',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Código',
    			'valor'		=> $dados->codigo,
    			'tipo'		=> 'text',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'titulo',
    			'ordem'         => '0',
    			'valor'		=> $dados->titulo,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Título',
    			'tipo'          => 'text',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);

    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('id' => $id, 'perfis' => $arrPerfis,'campos' => $arrCampos);
    }
    public function updateOcupacaoAction()
    {
    	try {
    		$dados = array();

    		$dados['codigo'] = $_POST['codigo_'];
    		$dados['titulo'] = $_POST['titulo_'];

    		$obj = new Auxiliar_Model_Dao_Ocupacao();
    		$condicao = $obj->getAdapter()->quoteInto ( 'codigo = ?', $dados['codigo'] );
    		return $obj->update($dados, $condicao);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function criarOcupacaoAction()
    {
    	$header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'codigo',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Código',
    			//'descricao' 	=> 'Nome do servi�o',
    			'tipo'		=> 'text',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'titulo',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Título',
    			'tipo'          => 'text',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6'
    			)
    	);
    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function insertOcupacaoAction()
    {
    	try {
    		unset($_POST['id']);
    		$dados = array();
    		foreach ($_POST as $chave => $valor){
    			$chave = substr($chave,0,-1);
    			$dados[$chave] = $valor;
    		}
    		$obj = new Auxiliar_Model_Dao_Ocupacao();
    		$obj->insert($dados);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }
    public function deletarOcupacaoAction()
    {
    	try {
    		$id = $this->getParam('id');

    		$obj =	new Auxiliar_Model_Dao_Ocupacao();
    		$obj->delete("codigo = '$id'");
    		//add mensagem
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}

    }
    public function editarSinonimoAction()
    {
    	$id = $this->getParam('id');
    	$obj = new Auxiliar_Model_Dao_Sinonimo();
    	$dados = $obj->fetchOne("ocupacao = '$id'");

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'ocupacao',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Ocupação',
    			'valor'		=> $dados->ocupacao,
    			'tipo'		=> 'text',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'titulo',
    			'ordem'         => '0',
    			'valor'		=> $dados->titulo,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Título',
    			'tipo'          => 'text',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);

    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('id' => $id, 'perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function updateSinonimoAction()
    {
    	try {
    		$dados = array();

    		$dados['codigo'] = $_POST['ocupacao_'];
    		$dados['titulo'] = $_POST['titulo_'];

    		$obj = new Auxiliar_Model_Dao_Sinonimo();
    		$condicao = $obj->getAdapter()->quoteInto ( 'ocupacao = ?', $dados['codigo'] );
    		return $obj->update($dados, $condicao);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}

    }

    public function criarSinonimoAction()
    {
    	$header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'ocupacao',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Ocupação',
    			//'descricao' 	=> 'Nome do servi�o',
    			'tipo'		=> 'text',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'titulo',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Título',
    			'tipo'          => 'text',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6'
    			)
    	);
    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('perfis' => $arrPerfis,'campos' => $arrCampos);
    }
    public function insertSinonimoAction()
    {
    	try {
    		unset($_POST['id']);
    		$dados = array();
    		foreach ($_POST as $chave => $valor){
    			$chave = substr($chave,0,-1);
    			$dados[$chave] = $valor;
    		}
    		$obj = new Auxiliar_Model_Dao_Sinonimo();
    		$obj->insert($dados);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function deletarSinonimoAction()
    {
    	try {
    		$id = $this->getParam('id');

    		$obj =	new Auxiliar_Model_Dao_Sinonimo();
    		$obj->delete("ocupacao = '$id'");
    		//add mensagem
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}

    }

    public function editarSubgrupoAction()
    {
    	$id = $this->getParam('id');
    	$obj = new Auxiliar_Model_Dao_Subgrupo();
    	$dados = $obj->fetchOne("codigo = '$id'");

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'codigo',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Código',
    			'valor'		=> $dados->codigo,
    			'tipo'		=> 'text',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'titulo',
    			'ordem'         => '0',
    			'valor'		=> $dados->titulo,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Título',
    			'tipo'          => 'text',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);

    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('id' => $id, 'perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function updateSubgrupoAction()
    {
    	try {
    		$dados = array();

    		$dados['codigo'] = $_POST['codigo_'];
    		$dados['titulo'] = $_POST['titulo_'];

    		$obj = new Auxiliar_Model_Dao_Subgrupo();
    		$condicao = $obj->getAdapter()->quoteInto ( 'codigo = ?', $dados['codigo'] );
    		return $obj->update($dados, $condicao);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function criarSubgrupoAction()
    {
    	$header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'codigo',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Código',
    			//'descricao' 	=> 'Nome do servi�o',
    			'tipo'		=> 'text',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'titulo',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Título',
    			'tipo'          => 'text',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6'
    			)
    	);
    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function insertSubgrupoAction()
    {
    	try {
    		unset($_POST['id']);
    		$dados = array();
    		foreach ($_POST as $chave => $valor){
    			$chave = substr($chave,0,-1);
    			$dados[$chave] = $valor;
    		}
    		$obj = new Auxiliar_Model_Dao_Subgrupo();
    		$obj->insert($dados);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function deletarSubgrupoAction()
    {
    	try {
    		$id = $this->getParam('id');

    		$obj =	new Auxiliar_Model_Dao_Subgrupo();
    		$obj->delete("codigo = '$id'");
    		//add mensagem
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}

    }

    public function editarSubgrupoPrincipalAction()
    {
    	$id = $this->getParam('id');
    	$obj = new Auxiliar_Model_Dao_SubgrupoPrincipal();
    	$dados = $obj->fetchOne("codigo = '$id'");

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'codigo',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Código',
    			'valor'		=> $dados->codigo,
    			'tipo'		=> 'text',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'titulo',
    			'ordem'         => '0',
    			'valor'		=> $dados->titulo,
    			'obrigatorio'   => 'true',
    			'nome'          => 'Título',
    			'tipo'          => 'text',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6',
    					'ws_style_object'	=> 'select2-skin'
    			)
    	);

    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('id' => $id, 'perfis' => $arrPerfis,'campos' => $arrCampos);
    }

    public function updateSubgrupoPrincipalAction()
    {
    	try {
    		$dados = array();

    		$dados['codigo'] = $_POST['codigo_'];
    		$dados['titulo'] = $_POST['titulo_'];

    		$obj = new Auxiliar_Model_Dao_SubgrupoPrincipal();
    		$condicao = $obj->getAdapter()->quoteInto ( 'codigo = ?', $dados['codigo'] );
    		return $obj->update($dados, $condicao);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function criarSubgrupoPrincipalAction()
    {
    	$header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'codigo',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Código',
    			//'descricao' 	=> 'Nome do servi�o',
    			'tipo'		=> 'text',
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'titulo',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Título',
    			'tipo'          => 'text',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6'
    			)
    	);
    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('perfis' => $arrPerfis,'campos' => $arrCampos);

    }

    public function insertSubgrupoPrincipalAction()
    {
    	try {
    		unset($_POST['id']);
    		$dados = array();
    		foreach ($_POST as $chave => $valor){
    			$chave = substr($chave,0,-1);
    			$dados[$chave] = $valor;
    		}
    		$obj = new Auxiliar_Model_Dao_SubgrupoPrincipal();
    		$obj->insert($dados);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }
    public function deletarSubgrupoPrincipalAction()
    {
    	try {
    		$id = $this->getParam('id');

    		$obj =	new Auxiliar_Model_Dao_SubgrupoPrincipal();
    		$obj->delete("codigo = '$id'");
    		//add mensagem
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}

    }
}