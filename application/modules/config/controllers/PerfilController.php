<?php
class Config_PerfilController extends App_Controller_Action
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
    	$perfilBo = new Config_Model_Bo_Perfil();
        $rowset = $perfilBo->find(null, 'nome', 30, 0);

        $header = array();
        $header[] = array('campo' => 'nome', 'label' => 'Nome');

        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $rowset, 'header' => $header );
    }

    public function paginacaoAction()
    {
    	$total = $this->getParam('total');

    	$perfilBo = new Config_Model_Bo_Perfil();

    	$rowset = $perfilBo->find(null, 'nome', 30, $total);

    	$this->_helper->json(array('data' => $rowset->toArray()));
    }


    public function createAction(){
    	$header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

	   	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'nome',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'nome',
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

	    	$perfi = new Config_Model_Dao_Perfil();
	    	$perfi->insert($dados);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function retrieveAction(){
    	$idPerfil = $this->getParam('id');

    	$perfil = new Config_Model_Dao_Perfil();
    	$dataPerfil = $perfil->fetchRow("id = '$idPerfil'");

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'nome',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'nome',
    			'valor'		=> $dataPerfil->nome,
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
    			'valor'		=> $dataPerfil->metanome,
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
    			'valor'		=> $dataPerfil->descricao,
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

    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('id' => $idPerfil, 'perfis' => $arrPerfis,'campos' => $arrCampos);
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
	    	$perfil = new Config_Model_Dao_Perfil();
	    	$condicao = $perfil->getAdapter ()->quoteInto ( 'id = ?', $dados ['id'] );
	    	return $perfil->update($dados, $condicao);
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function deleteAction(){
    	try {
	    	$idPerfil = $this->getParam('id');

	    	$perfilInfo =	new Config_Model_Dao_RlPerfilInformacao();
	    	$filhosInfo = $perfilInfo->fetchAll("id_perfil = '$idPerfil'");

	    	$perfilClass = new Config_Model_Dao_RlPerfilClassificacao();
	    	$filhosClass = $perfilClass->fetchAll("id_perfil = '$idPerfil'");

	    	if($filhos->count() === 0 && $filhosClass->count()){
	    		$perfil = new Config_Model_Dao_Perfil();
	    		$perfil->delete("id = '$idPerfil'");
	    	}

    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }
}