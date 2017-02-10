<?php
class Auxiliar_NatjurController extends App_Controller_Action
{

	public function prepararMetanome($string, $slug = false) {
		$string = strtolower($string);
		// Código ASCII das vogais
		$ascii['a'] = range(224, 230);
		$ascii['e'] = range(232, 235);
		$ascii['i'] = range(236, 239);
		$ascii['o'] = array_merge(range(242, 246), array(240, 248));
		$ascii['u'] = range(249, 252);
		// Código ASCII dos outros caracteres
		$ascii['b'] = array(223);
		$ascii['c'] = array(231);
		$ascii['d'] = array(208);
		$ascii['n'] = array(241);
		$ascii['y'] = array(253, 255);
		foreach ($ascii as $key=>$item) {
			$acentos = '';
			foreach ($item AS $codigo) $acentos .= chr($codigo);
			$troca[$key] = '/['.$acentos.']/i';
		}
		$string = preg_replace(array_values($troca), array_keys($troca), $string);
		$string = str_replace(' ', '', $string);
		// Slug?
		if ($slug) {
			// Troca tudo que não for letra ou número por um caractere ($slug)
			$string = preg_replace('/[^a-z0-9]/i', $slug, $string);
			// Tira os caracteres ($slug) repetidos
			$string = preg_replace('/' . $slug . '{2,}/i', $slug, $string);
			$string = trim($string, $slug);
		}
		return strtoupper($string);
	}

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

    public function indexAction()
    {
    	set_time_limit('1200');
    	$tibDao = new Config_Model_Dao_Tib();
    	$tibBo = new Config_Model_Bo_Tib();
    	$NATJIRIDIC = $tibDao->fetchRow("metanome = 'NATJIRIDIC'");
    	$CODNATJIRIDIC = $tibDao->fetchRow("metanome = 'CODNATJIRIDIC'");
    	$DESCNATJIRIDIC = $tibDao->fetchRow("metanome = 'DESCNATJIRIDIC'");
    	$DTCRIACAONATJIRIDIC = $tibDao->fetchRow("metanome = 'DTCRIACAONATJIRIDIC'");

    	$ibBo = new Content_Model_Bo_ItemBiblioteca();
        $rowset = $ibBo->find("id_tib = '".$NATJIRIDIC->id."'");//, 'valor', 30, 0);

        $data = array();
        $i = 0;
        foreach ($rowset as $k => $v){
        	$ibBo2 = new Content_Model_Bo_ItemBiblioteca();
        	$rowset2 = $ibBo2->find("id_ib_pai = '".$v->id."'");

        	$data[$i]['id'] = $v->id;
        	foreach ($rowset2 as $k2 => $v2){
        		switch ($v2->id_tib) {
        			case $CODNATJIRIDIC->id;
        				$data[$i]['CODNATJIRIDIC'] = $v2->valor;
        			break;
        			case $DESCNATJIRIDIC->id;
        				$data[$i]['DESCNATJIRIDIC'] = $v2->valor;
        			break;
        		}
        	}
        	$i++;
        }

        $header = array();
        $header[] = array('campo' => 'CODNATJIRIDIC', 'label' => $CODNATJIRIDIC->nome);
        $header[] = array('campo' => 'DESCNATJIRIDIC', 'label' => $DESCNATJIRIDIC->nome);

        $this->view->file = 'paginator.html.twig';//'index.html.twig';
        $this->view->data = array('data' => $data, 'header' => $header );
    }

 	public function criarPerfilAction()
 	{
 		$id = $this->getParam('id');
 		$tibDao = new Config_Model_Dao_Tib();
 		$CODNATJIRIDIC = $tibDao->fetchRow("metanome = 'CODNATJIRIDIC'");
 		$DESCNATJIRIDIC = $tibDao->fetchRow("metanome = 'DESCNATJIRIDIC'");


 		$ibBo = new Content_Model_Bo_ItemBiblioteca();
 		$rowset = $ibBo->find("id_ib_pai = '$id'");

 		$data = array();
 		foreach ($rowset as $k => $v) {
 			switch ($v->id_tib) {
        			case $CODNATJIRIDIC->id;
        				$data['CODNATJIRIDIC'] = $v->valor;
        			break;
        			case $DESCNATJIRIDIC->id;
        				$data['DESCNATJIRIDIC'] = $v->valor;
        			break;
        	}
 		}

 		$header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'codigo',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Código da Natureza',
    			'tipo'		=> 'text',
    			'valor'		=> $data['CODNATJIRIDIC'],
    			'metadatas'	=> array(
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-6',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'nome',
    			'ordem'         => '0',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Nome do novo Perfil',
    			'tipo'          => 'text',
    			'valor'		=> $data['DESCNATJIRIDIC'],
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-6'
    			)
    	);
    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('id' => $id,'perfis' => $arrPerfis,'campos' => $arrCampos);
 	}

 	public function gerarPerfilAction(){
 		$objTib = new Config_Model_Dao_Tib();
 		$dadosWsReferencia = array();
 		$dadosWsReferencia['TIB'] = $objTib->fetchRow("metanome = 'CODNATJIRIDIC'")->id;
 		$dadosWsReferencia['VALOR_IB'] = $_POST['codigo_'];

 		$objClassificacaoMeta = new Auxiliar_Model_Dao_ClassificacaoMetadata();
 		if( !is_null( $objClassificacaoMeta->fetchRow("metanome = 'ws_referencia_classificacao' and valor = '".json_encode($dadosWsReferencia)."'") ) ){
 			die('Classe já existente, não pode ser gerada novamente');
 		}

 		try {
	 		$idClassificacao = UUID::v4();
	 		$dadosClassificacao = array();
	 		$dadosClassificacao['id']	=	$idClassificacao;
	 		$dadosClassificacao['nome'] = $_POST['nome_'];
	 		$dadosClassificacao['metanome'] = $this->prepararMetanome(utf8_decode($_POST['nome_']));

	 		$objClassificacao = new Auxiliar_Model_Dao_Classificacao();
	 		$objClassificacao->insert($dadosClassificacao);

	 		$objTib = new Config_Model_Dao_Tib();

	 		$dadosWsReferencia = array();
	 		$dadosWsReferencia['TIB'] = $objTib->fetchRow("metanome = 'CODNATJIRIDIC'")->id;
	 		$dadosWsReferencia['VALOR_IB'] = $_POST['codigo_'];

	 		$idClassificacaoMeta = UUID::v4();
	 		$dadosClassificacaoMeta = array();
	 		$dadosClassificacaoMeta['id']	=	$idClassificacaoMeta;
	 		$dadosClassificacaoMeta['id_classificacao'] = $idClassificacao;
	 		$dadosClassificacaoMeta['metanome'] = 'ws_referencia_classificacao';
	 		$dadosClassificacaoMeta['valor'] = json_encode($dadosWsReferencia);
	 		$dadosClassificacaoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objClassificacaoMeta = new Auxiliar_Model_Dao_ClassificacaoMetadata();
	 		$objClassificacaoMeta->insert($dadosClassificacaoMeta);

	 		$objPerfilDao = new Auxiliar_Model_Dao_Perfil();
	 		$arrPerfis = array('ENTIDADE', 'GERAL', 'ENDERECO', 'CONTATOS');
	 		$arrCodigosPerfis = array();
	 		foreach ($arrPerfis as $nomePerfil){
	 			$arrCodigosPerfis[] = $objPerfilDao->fetchRow("metanome = '$nomePerfil'")->id;
	 		}

	 		$dadosRlPerfilClassificacao = array();
	 		$ObjRelacaoPC = new Auxiliar_Model_Dao_RlPerfilClassificacao();
	 		foreach ($arrCodigosPerfis as $codigoPerfil){
	 			$dadosRlPerfilClassificacao['id'] = UUID::v4();
	 			$dadosRlPerfilClassificacao['id_perfil'] = $codigoPerfil;
	 			$dadosRlPerfilClassificacao['id_classificacao'] = $idClassificacao;
	 			$dadosRlPerfilClassificacao['dt_criacao'] = date("Y-m-d h:i:s.B");
	 			$ObjRelacaoPC->insert($dadosRlPerfilClassificacao);
	 		}

	 		$objServico = new Config_Model_Dao_Servico();
	 		$objServicoMeta = new Config_Model_Dao_ServicoMetadata();

	 		$arrServico = array();
	 		$idIndex = UUID::v4();
	 		$arrServico['id'] = $idIndex;
	 		$arrServico['descricao'] = 'INDEX - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['metanome'] = 'index_entidade_'.strtolower($dadosClassificacao['metanome']);
	 		$arrServico['nome'] = $dadosClassificacao['nome'];
	 		$arrServico['id_pai'] = 'c07dd115-5bfa-4c44-83b1-268036dde79b';
	 		$arrServico['visivel'] = 't';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_perfil';
	 		$arrServicoMeta['valor'] = 'GERAL,ENTIDADE,ENDERECO,CONTATOS';
	 		$arrServicoMeta['id_servico'] = $idIndex;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'reload';
	 		$arrServicoMeta['id_servico'] = $idIndex;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'indexPessoa.php';
	 		$arrServicoMeta['id_servico'] = $idIndex;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_classificacao';
	 		$arrServicoMeta['valor'] = $dadosClassificacao['metanome'].',ENTIDADE';
	 		$arrServicoMeta['id_servico'] = $idIndex;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);

	 		//"a8af49a2-8144-4e38-b342-4fa968ef9981"
	 		$arrServico = array();
	 		$idAjaxLista = UUID::v4();
	 		$arrServico['id'] = $idAjaxLista;
	 		$arrServico['descricao'] = 'AJAX LISTA '.$dadosClassificacao['metanome'].' - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'AJAX Lista '.$dadosClassificacao['nome'];
	 		$arrServico['id_pai'] = $idIndex;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_perfil';
	 		$arrServicoMeta['valor'] = 'GERAL,ENTIDADE,ENDERECO,CONTATOS';
	 		$arrServicoMeta['id_servico'] = $idAjaxLista;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idAjaxLista;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'listPessoa.php';
	 		$arrServicoMeta['id_servico'] = $idAjaxLista;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_classificacao';
	 		$arrServicoMeta['valor'] = $dadosClassificacao['metanome'].',ENTIDADE';
	 		$arrServicoMeta['id_servico'] = $idAjaxLista;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'paginacao';
	 		$arrServicoMeta['id_servico'] = $idAjaxLista;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);

	 		//"c549e4a7-647a-4250-875e-ae77a7d7a132"
	 		$arrServico = array();
	 		$idApagar = UUID::v4();
	 		$arrServico['id'] = $idApagar;
	 		$arrServico['descricao'] = 'APAGAR '.$dadosClassificacao['metanome'].' - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'Apagar '.$dadosClassificacao['nome'];
	 		$arrServico['id_pai'] = $idIndex;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_perfil';
	 		$arrServicoMeta['valor'] = 'GERAL,ENTIDADE,ENDERECO,CONTATOS';
	 		$arrServicoMeta['id_servico'] = $idApagar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_itemremove';
	 		$arrServicoMeta['valor'] = '1';
	 		$arrServicoMeta['id_servico'] = $idApagar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_confirm';
	 		$arrServicoMeta['valor'] = 'Você deseja apagar o registro selecionado?';
	 		$arrServicoMeta['id_servico'] = $idApagar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idApagar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'deletePessoa.php';
	 		$arrServicoMeta['id_servico'] = $idApagar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'listaction';
	 		$arrServicoMeta['id_servico'] = $idApagar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_classificacao';
	 		$arrServicoMeta['valor'] = $dadosClassificacao['metanome'].',ENTIDADE';
	 		$arrServicoMeta['id_servico'] = $idApagar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_icon';
	 		$arrServicoMeta['valor'] = 'glyphicon glyphicon-trash';
	 		$arrServicoMeta['id_servico'] = $idApagar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_target';
	 		$arrServicoMeta['valor'] = 'index_entidade_'.strtolower($dadosClassificacao['metanome']);
	 		$arrServicoMeta['id_servico'] = $idApagar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);

	 		//"d6b5fe5c-5df3-4c26-9846-6a5f6fab5c9c"
	 		$arrServico = array();
	 		$idEditar = UUID::v4();
	 		$arrServico['id'] = $idEditar;
	 		$arrServico['descricao'] = 'EDITAR '.$dadosClassificacao['metanome'].' - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'Editar '.$dadosClassificacao['nome'];
	 		$arrServico['id_pai'] = $idIndex;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'reload';
	 		$arrServicoMeta['id_servico'] = $idEditar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'retrievePessoa.php';
	 		$arrServicoMeta['id_servico'] = $idEditar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'listaction';
	 		$arrServicoMeta['id_servico'] = $idEditar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_classificacao';
	 		$arrServicoMeta['valor'] = $dadosClassificacao['metanome'].',ENTIDADE';
	 		$arrServicoMeta['id_servico'] = $idEditar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_perfil';
	 		$arrServicoMeta['valor'] = 'GERAL,ENTIDADE,ENDERECO,CONTATOS,FATURAMENTO';
	 		$arrServicoMeta['id_servico'] = $idEditar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_icon';
	 		$arrServicoMeta['valor'] = 'fa fa-edit';
	 		$arrServicoMeta['id_servico'] = $idEditar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		//filhos de d6b5fe5c-5df3-4c26-9846-6a5f6fab5c9c
	 		//"8d94ed1c-0c78-425a-a6e0-35451d82e265"
	 		$arrServico = array();
	 		$idAjaxEditar = UUID::v4();
	 		$arrServico['id'] = $idAjaxEditar;
	 		$arrServico['descricao'] = 'AJAX EDITAR '.$dadosClassificacao['metanome'].' - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'Salvar';
	 		$arrServico['id_pai'] = $idEditar;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_perfil';
	 		$arrServicoMeta['valor'] = 'GERAL,ENTIDADE,ENDERECO,CONTATOS';
	 		$arrServicoMeta['id_servico'] = $idAjaxEditar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idAjaxEditar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'updatePessoa.php';
	 		$arrServicoMeta['id_servico'] = $idAjaxEditar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'formaction';
	 		$arrServicoMeta['id_servico'] = $idAjaxEditar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_classificacao';
	 		$arrServicoMeta['valor'] = $dadosClassificacao['metanome'].',ENTIDADE';
	 		$arrServicoMeta['id_servico'] = $idAjaxEditar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_target';
	 		$arrServicoMeta['valor'] = 'index_entidade_'.strtolower($dadosClassificacao['metanome']);
	 		$arrServicoMeta['id_servico'] = $idAjaxEditar;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		//"a5b60d3c-b3ff-44c0-9ef2-36d89e973884"
	 		$arrServico = array();
	 		$idFilterCNAE = UUID::v4();
	 		$arrServico['id'] = $idFilterCNAE;
	 		$arrServico['descricao'] = 'AJAX FILTER CNAE - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'AJAX Filter CNAE';
	 		$arrServico['id_pai'] = $idEditar;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['id_tib'] = $objTib->fetchRow("metanome = 'TPCNAE'")->id;
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_campo';
	 		$arrServicoMeta['valor'] = '.cnaefiscal';
	 		$arrServicoMeta['id_servico'] = $idFilterCNAE;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'filter';
	 		$arrServicoMeta['id_servico'] = $idFilterCNAE;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'filterItemBiblioteca.php';
	 		$arrServicoMeta['id_servico'] = $idFilterCNAE;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idFilterCNAE;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_selecttags';
	 		$arrServicoMeta['valor'] = 'false';
	 		$arrServicoMeta['id_servico'] = $idFilterCNAE;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comboform';
	 		$arrServicoMeta['valor'] = '(CNAECODSUBCLAS) CNAEDESCSUBCLAS';
	 		$arrServicoMeta['id_servico'] = $idFilterCNAE;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comboordem';
	 		$arrServicoMeta['valor'] = 'CNAECODSUBCLAS';
	 		$arrServicoMeta['id_servico'] = $idFilterCNAE;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);

	 		//"336d3205-8ff4-4643-b238-989b4f2477a0"
	 		$arrServico = array();
	 		$idIncluirNovo = UUID::v4();
	 		$arrServico['id'] = $idIncluirNovo;
	 		$arrServico['descricao'] = 'INCLUIR NOVO '.$dadosClassificacao['metanome'].' - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'Incluir Novo '.$dadosClassificacao['nome'];
	 		$arrServico['id_pai'] = $idIndex;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'reload';
	 		$arrServicoMeta['id_servico'] = $idIncluirNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'createPessoa.php';
	 		$arrServicoMeta['id_servico'] = $idIncluirNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'action';
	 		$arrServicoMeta['id_servico'] = $idIncluirNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_classificacao';
	 		$arrServicoMeta['valor'] = $dadosClassificacao['metanome'].',ENTIDADE';
	 		$arrServicoMeta['id_servico'] = $idIncluirNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_perfil';
	 		$arrServicoMeta['valor'] = 'GERAL,ENTIDADE,ENDERECO,CONTATOS';
	 		$arrServicoMeta['id_servico'] = $idIncluirNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
			//"f27cd644-d0ff-4a13-85a5-e54c3d95926a"
	 		$arrServico = array();
	 		$idSalvarNovo = UUID::v4();
	 		$arrServico['id'] = $idSalvarNovo ;
	 		$arrServico['descricao'] = 'SALVAR NOVO '.$dadosClassificacao['metanome'].' - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'Salvar';
	 		$arrServico['id_pai'] = $idIncluirNovo;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idSalvarNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'insertPessoa.php';
	 		$arrServicoMeta['id_servico'] = $idSalvarNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'formaction';
	 		$arrServicoMeta['id_servico'] = $idSalvarNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_classificacao';
	 		$arrServicoMeta['valor'] = $dadosClassificacao['metanome'].',ENTIDADE';
	 		$arrServicoMeta['id_servico'] = $idSalvarNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_perfil';
	 		$arrServicoMeta['valor'] = 'GERAL,ENTIDADE,ENDERECO,CONTATOS';
	 		$arrServicoMeta['id_servico'] = $idSalvarNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_target';
	 		$arrServicoMeta['valor'] = 'index_entidade_'.strtolower($dadosClassificacao['metanome']);
	 		$arrServicoMeta['id_servico'] = $idSalvarNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		//"39f28894-562d-411f-99b2-9b35b3ed1256"
	 		$arrServico = array();
	 		$idFiltrarNovo = UUID::v4();
	 		$arrServico['id'] = $idFiltrarNovo;
	 		$arrServico['descricao'] = 'AJAX FILTRAR NOVO '.$dadosClassificacao['metanome'].' - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'AJAX Filtrar Novo '.$dadosClassificacao['nome'];
	 		$arrServico['id_pai'] = $idIncluirNovo;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idFiltrarNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'filterPessoa.php';
	 		$arrServicoMeta['id_servico'] = $idFiltrarNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'filter';
	 		$arrServicoMeta['id_servico'] = $idFiltrarNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_classificacao';
	 		$arrServicoMeta['valor'] = $dadosClassificacao['metanome'].',ENTIDADE';
	 		$arrServicoMeta['id_servico'] = $idFiltrarNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_perfil';
	 		$arrServicoMeta['valor'] = 'GERAL,ENTIDADE,ENDERECO,CONTATOS';
	 		$arrServicoMeta['id_servico'] = $idFiltrarNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_campo';
	 		$arrServicoMeta['valor'] = '.nomepessoa';
	 		$arrServicoMeta['id_servico'] = $idFiltrarNovo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		//"e931b3da-3693-430a-b611-a47c2b80489b"
	 		$arrServico = array();
	 		$idAjaxNomeDaMae = UUID::v4();
	 		$arrServico['id'] = $idAjaxNomeDaMae;
	 		$arrServico['descricao'] = 'AJAX FILTRA NOME DAS MÃES - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'AJAX Filtra nome das Mães';
	 		$arrServico['id_pai'] = $idIncluirNovo;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idAjaxNomeDaMae;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'filterPessoaMae.php';
	 		$arrServicoMeta['id_servico'] = $idAjaxNomeDaMae;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'filter';
	 		$arrServicoMeta['id_servico'] = $idAjaxNomeDaMae;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_classificacao';
	 		$arrServicoMeta['valor'] = $dadosClassificacao['metanome'].',ENTIDADE';
	 		$arrServicoMeta['id_servico'] = $idAjaxNomeDaMae;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_perfil';
	 		$arrServicoMeta['valor'] = 'GERAL,ENTIDADE,ENDERECO,CONTATOS';
	 		$arrServicoMeta['id_servico'] = $idAjaxNomeDaMae;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_campo';
	 		$arrServicoMeta['valor'] = '.nomemae';
	 		$arrServicoMeta['id_servico'] = $idAjaxNomeDaMae;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		//"506aa18f-7a9e-4cd6-9aec-39e550401d4b"
	 		$arrServico = array();
	 		$idAjaxNomeDoPai = UUID::v4();
	 		$arrServico['id'] = $idAjaxNomeDoPai;
	 		$arrServico['descricao'] = 'AJAX FILTRA NOME DOS PAIS - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'AJAX Filtra nome dos pais';
	 		$arrServico['id_pai'] = $idIncluirNovo;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idAjaxNomeDoPai;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'filterPessoaPai.php';
	 		$arrServicoMeta['id_servico'] = $idAjaxNomeDoPai;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'filter';
	 		$arrServicoMeta['id_servico'] = $idAjaxNomeDoPai;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_classificacao';
	 		$arrServicoMeta['valor'] = $dadosClassificacao['metanome'].',ENTIDADE';
	 		$arrServicoMeta['id_servico'] = $idAjaxNomeDoPai;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_perfil';
	 		$arrServicoMeta['valor'] = 'GERAL,ENTIDADE,ENDERECO,CONTATOS';
	 		$arrServicoMeta['id_servico'] = $idAjaxNomeDoPai;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_campo';
	 		$arrServicoMeta['valor'] = '.nomepai';
	 		$arrServicoMeta['id_servico'] = $idAjaxNomeDoPai;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		//"aeccbaf8-6ff0-4ac1-a2b8-27ceb45f72b7"
	 		$arrServico = array();
	 		$idAjaxFiltraOperadora = UUID::v4();
	 		$arrServico['id'] = $idAjaxFiltraOperadora;
	 		$arrServico['descricao'] = 'AJAX FILTER OPERADORA - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'AJAX Filter operadora';
	 		$arrServico['id_pai'] = $idIncluirNovo;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['id_tib'] = $objTib->fetchRow("metanome = 'TPOPECEL'")->id;
	 		$arrServico['ordem'] = '1';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idAjaxFiltraOperadora;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'filterItemBiblioteca.php';
	 		$arrServicoMeta['id_servico'] = $idAjaxFiltraOperadora;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'filter';
	 		$arrServicoMeta['id_servico'] = $idAjaxFiltraOperadora;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_campo';
	 		$arrServicoMeta['valor'] = '.operadoracel';
	 		$arrServicoMeta['id_servico'] = $idAjaxFiltraOperadora;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_tibcampo';
	 		$arrServicoMeta['valor'] = 'nome';
	 		$arrServicoMeta['id_servico'] = $idAjaxFiltraOperadora;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		//"1494d35b-ffab-49a7-a003-2df3f89b215e"
	 		$arrServico = array();
	 		$idAjaxFilterCNAE2 = UUID::v4();
	 		$arrServico['id'] = $idAjaxFilterCNAE2;
	 		$arrServico['descricao'] = 'AJAX FILTER CNAE - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'AJAX Filter CNAE';
	 		$arrServico['id_pai'] = $idIncluirNovo;
	 		$arrServico['visivel'] = 't';
	 		$arrServico['id_tib'] = $objTib->fetchRow("metanome = 'TPCNAE'")->id;
	 		$arrServico['ordem'] = '1';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_campo';
	 		$arrServicoMeta['valor'] = '.cnaefiscal';
	 		$arrServicoMeta['id_servico'] = $idAjaxFilterCNAE2;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'filter';
	 		$arrServicoMeta['id_servico'] = $idAjaxFilterCNAE2;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'filterItemBiblioteca.php';
	 		$arrServicoMeta['id_servico'] = $idAjaxFilterCNAE2;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idAjaxFilterCNAE2;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_selecttags';
	 		$arrServicoMeta['valor'] = 'false';
	 		$arrServicoMeta['id_servico'] = $idAjaxFilterCNAE2;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comboordem';
	 		$arrServicoMeta['valor'] = 'CNAECODSUBCLAS';
	 		$arrServicoMeta['id_servico'] = $idAjaxFilterCNAE2;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comboform';
	 		$arrServicoMeta['valor'] = 'CNAECODSUBCLAS - CNAEDESCSUBCLAS';
	 		$arrServicoMeta['id_servico'] = $idAjaxFilterCNAE2;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);

	 		//"85d65f40-d618-4447-94a2-d38d0966e653"
	 		$arrServico = array();
	 		$idVinculo = UUID::v4();
	 		$arrServico['id'] = $idVinculo;
	 		$arrServico['descricao'] = 'VINCULOS '.$dadosClassificacao['metanome'].' - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'Vínculos';
	 		$arrServico['id_pai'] = $idIndex;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['ordem'] = '0';
	 		$arrServico['metanome'] = strtolower($dadosClassificacao['metanome']).'_entidade_vinculo';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'listaction';
	 		$arrServicoMeta['id_servico'] = $idVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'indexVinculo.php';
	 		$arrServicoMeta['id_servico'] = $idVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_icon';
	 		$arrServicoMeta['valor'] = 'fa fa-users';
	 		$arrServicoMeta['id_servico'] = $idVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'reload';
	 		$arrServicoMeta['id_servico'] = $idVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_conversacao';
	 		$arrServicoMeta['valor'] = strtolower($dadosClassificacao['metanome']).'_entidade_vinculo';
	 		$arrServicoMeta['id_servico'] = $idVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);

	 		//"89a7a8bf-0002-4f4f-86f4-c1fd25f1c280"
	 		$arrServico = array();
	 		$idInserirNovoVinculo = UUID::v4();
	 		$arrServico['id'] = $idInserirNovoVinculo;
	 		$arrServico['descricao'] = 'INSERIR NOVO VINCULO '.$dadosClassificacao['metanome'].' - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'Inserir Novo Vínculo';
	 		$arrServico['id_pai'] = $idVinculo;
	 		$arrServico['visivel'] = 't';
	 		$arrServico['ordem'] = '2';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'action';
	 		$arrServicoMeta['id_servico'] = $idInserirNovoVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'reload';
	 		$arrServicoMeta['id_servico'] = $idInserirNovoVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'createVinculo.php';
	 		$arrServicoMeta['id_servico'] = $idInserirNovoVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		//"3d71e68c-0687-4c40-8318-8159e9da43a6"
	 		$arrServico = array();
	 		$idSalvarVinculo = UUID::v4();
	 		$arrServico['id'] = $idSalvarVinculo;
	 		$arrServico['descricao'] = 'SALVAR VINCULO '.$dadosClassificacao['metanome'].' - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'Salvar Vínculo';
	 		$arrServico['id_pai'] = $idInserirNovoVinculo;
	 		$arrServico['visivel'] = 'f';
	 		$arrServico['ordem'] = '1';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'insertVinculo.php';
	 		$arrServicoMeta['id_servico'] = $idSalvarVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'formaction';
	 		$arrServicoMeta['id_servico'] = $idSalvarVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idSalvarVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_conversacao';
	 		$arrServicoMeta['valor'] = strtolower($dadosClassificacao['metanome']).'_entidade_vinculo';
	 		$arrServicoMeta['id_servico'] = $idSalvarVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_target';
	 		$arrServicoMeta['valor'] = strtolower($dadosClassificacao['metanome']).'_entidade_vinculo';
	 		$arrServicoMeta['id_servico'] = $idSalvarVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		//"c5cbdf35-047d-4460-9f62-8138631cb9c0"
	 		$arrServico = array();
	 		$idAjaxFiltraVInculo2 = UUID::v4();
	 		$arrServico['id'] = $idAjaxFiltraVInculo2;
	 		$arrServico['descricao'] = 'AJAX FILTRAR VINCULO '.$dadosClassificacao['metanome'].' - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'Ajax Filtrar Vínculo';
	 		$arrServico['id_pai'] = $idInserirNovoVinculo;
	 		$arrServico['visivel'] = 't';
	 		$arrServico['ordem'] = '1';
	 		$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'filter';
	 		$arrServicoMeta['id_servico'] = $idAjaxFiltraVInculo2;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idAjaxFiltraVInculo2;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'filterPessoa.php';
	 		$arrServicoMeta['id_servico'] = $idAjaxFiltraVInculo2;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_campo';
	 		$arrServicoMeta['valor'] = '.pessoa';
	 		$arrServicoMeta['id_servico'] = $idAjaxFiltraVInculo2;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		//"091e7858-9a46-4df1-8e0e-880a3f50744d"
	 		$arrServico = array();
	 		$idExcluirVinculo = UUID::v4();
	 		$arrServico['id'] = $idExcluirVinculo;
	 		$arrServico['descricao'] = 'EXCLUIR VINCULO '.$dadosClassificacao['metanome'].' - Serviço de gestão da entidade '. $dadosClassificacao['nome'] .' - Serviço Gerado automáticamente pela aplicação';
	 		$arrServico['nome'] = 'Excluir Vínculo';
	 		$arrServico['id_pai'] = $idVinculo;
	 		$arrServico['visivel'] = 't';
	 		$arrServico['ordem'] = '1';
			$arrServico['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServico->insert($arrServico);
	 		unset($arrServico);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_arquivo';
	 		$arrServicoMeta['valor'] = 'deleteVinculo.php';
	 		$arrServicoMeta['id_servico'] = $idExcluirVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_comportamento';
	 		$arrServicoMeta['valor'] = 'listaction';
	 		$arrServicoMeta['id_servico'] = $idExcluirVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_show';
	 		$arrServicoMeta['valor'] = 'ajax';
	 		$arrServicoMeta['id_servico'] = $idExcluirVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_target';
	 		$arrServicoMeta['valor'] = strtolower($dadosClassificacao['metanome']).'_entidade_vinculo';
	 		$arrServicoMeta['id_servico'] = $idExcluirVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);
	 		$arrServicoMeta = array();
	 		$arrServicoMeta['id'] = UUID::v4();
	 		$arrServicoMeta['metanome'] = 'ws_icon';
	 		$arrServicoMeta['valor'] = 'glyphicon glyphicon-trash';
	 		$arrServicoMeta['id_servico'] = $idExcluirVinculo;
	 		$arrServicoMeta['dt_criacao'] = date("Y-m-d h:i:s.B");
	 		$objServicoMeta->insert($arrServicoMeta);
	 		unset($arrServicoMeta);

 		} catch(Zend_Exception $ex) {
 			var_dump($dados);
 			echo '<Br>-----------<Br>';
 			var_dump($ex->getMessage());
 			exit;
 		}
 	}

 	public function importarPessoasAction(){
 		$identity = Zend_Auth::getInstance()->getIdentity();

 		$objIb = new Config_Model_Dao_ItemBiblioteca();
 		$objTib = new Config_Model_Dao_Tib();
 		$CODNATJIRIDIC = $objTib->fetchRow("metanome = 'CODNATJIRIDIC'")->id;

 		$dadosWsReferencia = array();
 		$dadosWsReferencia['TIB'] = $CODNATJIRIDIC;
 		$dadosWsReferencia['VALOR_IB'] = $objIb->fetchRow("id_ib_pai = '".$_POST['id']."' and id_tib = '$CODNATJIRIDIC'")->valor;

 		$objClassificacaoMeta = new Auxiliar_Model_Dao_ClassificacaoMetadata();
 		if( is_null( $objClassificacaoMeta->fetchRow("metanome = 'ws_referencia_classificacao' and valor = '".json_encode($dadosWsReferencia)."'") ) ){
 			die('Classe não existente, deve ser criada!');
 		} else {
 			try {
	 			$classificacaoMeta = $objClassificacaoMeta->fetchRow("metanome = 'ws_referencia_classificacao' and valor = '".json_encode($dadosWsReferencia)."'");
	 			$objTinf = new Config_Model_Dao_Informacao();
	 			$objInfo = new Auxiliar_Model_Dao_TbInformacao();
	 			$objVP   = new Auxiliar_Model_Dao_RlVinculoPessoa();

	 			$rowset = $objInfo->fetchAll("valor ilike '".$dadosWsReferencia['VALOR_IB']."%' and id_tinfo = '".$objTinf->fetchRow("metanome = 'CODNATJUR'")->id."'");
	 			foreach ( $rowset as  $k => $v){
	 				if(is_null($objVP->fetchRow("id_classificacao = '".$classificacaoMeta->id_classificacao."' and id_pessoa = '".$v->id_pessoa."' and id_grupo = '".$identity->time['id']."'"))){
		 				$dadosVinculo = array();
		 				$dadosVinculo['id'] = UUID::v4();
		 				$dadosVinculo['id_classificacao'] = $classificacaoMeta->id_classificacao;
		 				$dadosVinculo['id_pessoa'] = $v->id_pessoa;
		 				$dadosVinculo['id_grupo'] = $identity->time['id'];
		 				$dadosVinculo['datainicio'] = date("Y-m-d h:i:s.B");
		 				$objVP->insert($dadosVinculo);
	 				}
	 			}
 			} catch(Zend_Exception $ex) {
 				var_dump($dados);
 				echo '<Br>-----------<Br>';
 				var_dump($ex->getMessage());
 				exit;
 			}
 		}
 	}
}