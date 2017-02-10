<?php
class Content_NoticiaController extends App_Controller_Action_Twig
{

 	// public function init()
  //   {
  //       parent::init();
  //       $this->_helper->layout()->setLayout('novo_hash');
  //   }

  //   *
  //    * configuração padrão para definir o uso de twig em todas as actions do controller
  //    * @todo pode-se criar um App_controller_Action_Twig para facilitar isso

  //   public function postDispatch()
  //   {
  //       $this->view->params = $this->getAllParams();
  //       $this->renderScript('twig.phtml');
  //       return parent::postDispatch();
  //   }
    /**
     * index: esta rota deve listar todos os servi�os em ordem alfab�tica, em forma de �rvore.
     * Deve utilizar o index.twig padr�o para exibi��o.
     * Esta exibi��o � paginada e utilizar� a rota /list para completar a listagem.
     */
    public function listarTodasNoticiasAction()
    {
    	$ibBo	= new Content_Model_Bo_ItemBiblioteca();
        $tib	= new Config_Model_Dao_Tib();
        $rlGI	= new Content_Model_Dao_RlGrupoItem();
        $grupo	= new Config_Model_Bo_Grupo();

        $idTibNoticia   = $tib->fetchRow("metanome = 'TPNOTICIA'")->id;
        $idTibTitulo    = $tib->fetchRow("metanome = 'title'")->id;
        $idTibDtPub	= $tib->fetchRow("metanome = 'pubdate'")->id;
        $idTibFonte     = $tib->fetchRow("metanome = 'channel'")->id;

        $noticias = $ibBo->find("id_tib = '$idTibNoticia'",'',30,0);

        $i = 0;

        $rowset = array();

        foreach ($noticias as $k => $n)
        {
            $titulo             = $ibBo->find("id_ib_pai = '".$n->id."' and id_tib = '$idTibTitulo'");
            $dataDePublicacao   = $ibBo->find("id_ib_pai = '".$n->id."' and id_tib = '$idTibDtPub'");
            $fonte              = $ibBo->find("id_ib_pai = '".$n->id."' and id_tib = '$idTibFonte'");

            $arrRlGrupo	= $rlGI->fetchAll("id_item = '".$n->id."'");

            foreach ( $arrRlGrupo as $k => $rl)
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                $stmt = $db->query('SELECT * FROM rastroGrupo(:grupo)', array(':grupo' => $rl->id_grupo));
                $rowsetStrG = $stmt->fetchAll();

                $strgrupo	= str_replace('"', '', $rowsetStrG[0]['rastrogrupo']);
                $strgrupo	= str_replace('[', '', $strgrupo);
                $strgrupo	= str_replace(']', '', $strgrupo);
                $strgrupo	= str_replace(',', ' | ', $strgrupo);

                if ( !empty($titulo['valor'])){$rowset[$i]['titulo'] = $titulo['valor']->valor;}
                if ( !empty($dataDePublicacao['valor'])){$rowset[$i]['data'] = date('d/m/Y H:i:s', strtotime($dataDePublicacao['valor']->valor));}
                if ( !empty($fonte['valor'])){$rowset[$i]['fonte'] = $fonte['valor']->valor;}

                $rowset[$i]['grupo'] = $strgrupo;
                $rowset[$i]['id'] = $rl->id;
                $i++;
            }
        }

        $header   = array();
        $header[] = array('campo' => 'grupo', 'label' => 'Grupo - Hierarquia');
        $header[] = array('campo' => 'titulo', 'label' => 'Título');
        $header[] = array('campo' => 'fonte', 'label' => 'Fonte');
        $header[] = array('campo' => 'data', 'label' => 'Data');

        $this->header = $header;

        $this->view->file = 'paginator.html.twig';
        $pagina = $this->_getParam('page', 1);

        $paginacao = Zend_Paginator::factory($rowset);

        $paginacao->setCurrentPageNumber(1);

        $paginacao->setItemCountPerPage(20);

        $paginacao->setCurrentPageNumber($pagina);

    	$this->view->data = array('data' => $paginacao, 'header' => $header);
    }

    public function paginarListarTodasNoticiasAction()
    {

    	$total = $this->getParam('total');

    	$ibBo	= new Content_Model_Bo_ItemBiblioteca();
    	$tib	= new Config_Model_Dao_Tib();
    	$rlGI	= new Content_Model_Dao_RlGrupoItem();
    	$grupo	= new Config_Model_Bo_Grupo();

    	$idTibNoticia = $tib->fetchRow("metanome = 'TPNOTICIA'")->id;
    	$idTibTitulo = $tib->fetchRow("metanome = 'title'")->id;
    	$idTibDtPub	 = $tib->fetchRow("metanome = 'pubdate'")->id;
    	$idTibFonte  = $tib->fetchRow("metanome = 'channel'")->id;

    	$noticias = $ibBo->find("id_tib = '$idTibNoticia'",'',30,30);

    	$i = 0;
    	$rowset = array();
    	foreach ($noticias as $k => $n){

    		$titulo = $ibBo->find("id_ib_pai = '".$n->id."' and id_tib = '$idTibTitulo'");
    		$dataDePublicacao = $ibBo->find("id_ib_pai = '".$n->id."' and id_tib = '$idTibDtPub'");
    		$fonte = $ibBo->find("id_ib_pai = '".$n->id."' and id_tib = '$idTibFonte'");

    		$arrRlGrupo	= $rlGI->fetchAll("id_item = '".$n->id."'");
    		foreach ( $arrRlGrupo as $k => $rl){
    			$arrGrupos = array();
    			$g = $grupo->find("id = '".$rl->id_grupo."'");
    			$arrGrupos[] = $g['valor']->nome;
    			//x($g);
    			while ( !is_null($g['valor']->id_pai) ){
    				$g = $grupo->find("id = '".$g['valor']->id_pai."'");
    				$arrGrupos[] = $g['valor']->nome;
    			}
    			$arrGrupos2 = array_reverse($arrGrupos);
    			$contagem 	= count($arrGrupos2);
    			$controle	= 1;
    			$strgrupo	= '';
    			foreach ($arrGrupos2 as $key => $value){


    				$strgrupo .= $value;
    				if ($controle < $contagem){
    					$strgrupo .= ' | ';
    				}
    				$controle++;
    			}
    			if ( !empty($titulo['valor'])){$rowset[$i]['titulo'] = $titulo['valor']->valor;}
    			if ( !empty($dataDePublicacao['valor'])){$rowset[$i]['data'] = $dataDePublicacao['valor']->valor;}
    			if ( !empty($fonte['valor'])){$rowset[$i]['fonte'] = $fonte['valor']->valor;}
    			$rowset[$i]['grupo'] = $strgrupo;
    			$i++;
    		}
    	}

    	$this->_helper->json(array('data' => $rowset));
    }

    public function listarTodasNoticiasPorTimeAction(){
    	set_time_limit('36000');
    	$identity = Zend_Auth::getInstance()->getIdentity();

    	$ibBo	= new Content_Model_Bo_ItemBiblioteca();
    	$tib	= new Config_Model_Dao_Tib();
    	$rlGI	= new Content_Model_Dao_RlGrupoItem();
    	$grupo	= new Config_Model_Bo_Grupo();
    	$grupoDao	= new Config_Model_Dao_Grupo();

    	$idTibNoticia = $tib->fetchRow("metanome = 'TPNOTICIA'")->id;
    	$idTibTitulo = $tib->fetchRow("metanome = 'title'")->id;
    	$idTibDtPub	 = $tib->fetchRow("metanome = 'pubdate'")->id;
    	$idTibFonte  = $tib->fetchRow("metanome = 'channel'")->id;

    	$db = Zend_Db_Table::getDefaultAdapter();
    	$stmt1 = $db->query('WITH RECURSIVE arvore_grupos (id) AS
									(
									select id from tb_grupo where id = :grupo
									union all
									select tb_grupo.id from tb_grupo
									inner join arvore_grupos on tb_grupo.id_pai = arvore_grupos.id
									)
									select id from arvore_grupos', array(':grupo' => $identity->time['id']));
    	$rowsetGruposDoTime = $stmt1->fetchAll();

    	$arrIdGrupos = array();
    	$noticias = array();
    	foreach ($rowsetGruposDoTime as $ka => $va){
    		$arrIdGrupos[] = $va['id'];

    		$arrRlGrupo	= $rlGI->fetchAll("id_grupo = '".$va['id']."'");
    		foreach ($arrRlGrupo as $krelacao => $vrelacao){
    			 $resultado = $ibBo->find("id = '".$vrelacao->id_item."' and id_tib = '$idTibNoticia'");
    			 if( !empty($resultado['valor'])){
    			 	$noticias[] = $resultado;
    			 }
    		}

    	}

//    	$noticias = $ibBo->find("id_tib = '$idTibNoticia'");
    	$i = 0;
    	$rowset = array();
    	foreach ($noticias as $k => $n){

    		$titulo = $ibBo->find("id_ib_pai = '".$n['valor']->id."' and id_tib = '$idTibTitulo'");
    		$dataDePublicacao = $ibBo->find("id_ib_pai = '".$n['valor']->id."' and id_tib = '$idTibDtPub'");
    		$fonte = $ibBo->find("id_ib_pai = '".$n['valor']->id."' and id_tib = '$idTibFonte'");

    		$arrRlGrupo	= $rlGI->fetchAll("id_item = '".$n['valor']->id."'");
    		foreach ( $arrRlGrupo as $k => $rl){
    			if (in_array($rl->id_grupo, $arrIdGrupos)){

					$stmt2 = $db->query('SELECT * FROM rastroGrupo(:grupo)', array(':grupo' => $rl->id_grupo));
					$rowsetStrG = $stmt2->fetchAll();
					$strgrupo	= str_replace('"', '', $rowsetStrG[0]['rastrogrupo']);
					$strgrupo	= str_replace('[', '', $strgrupo);
					$strgrupo	= str_replace(']', '', $strgrupo);
					$strgrupo	= str_replace(',', ' | ', $strgrupo);

	    			if ( !empty($titulo['valor'])){$rowset[$i]['titulo'] = $titulo['valor']->valor;}
	    			if ( !empty($dataDePublicacao['valor'])){$rowset[$i]['data'] = $dataDePublicacao['valor']->valor;}
	    			if ( !empty($fonte['valor'])){$rowset[$i]['fonte'] = $fonte['valor']->valor;}
	    			$rowset[$i]['grupo'] = $strgrupo;
	    			$rowset[$i]['id'] = $rl->id;
	    			$i++;
    			}
    		}
    	}


    	$header = array();
    	$header[] = array('campo' => 'grupo', 'label' => 'Grupo - Hierarquia');
    	$header[] = array('campo' => 'titulo', 'label' => 'Título');
    	$header[] = array('campo' => 'fonte', 'label' => 'Fonte');
    	$header[] = array('campo' => 'data', 'label' => 'Data');

    	$this->view->file = 'paginator.html.twig';


        $pagina = $this->_getParam('page', 1);

        $paginacao = Zend_Paginator::factory($rowset);

        $paginacao->setCurrentPageNumber(1);

        $paginacao->setItemCountPerPage(20);

        $paginacao->setCurrentPageNumber($pagina);

    	$this->view->data = array('data' => $paginacao, 'header' => $header);

    }

    public function editarNoticiaAction()
    {
    	$id_rl	= $this->getParam('id');
    	$rlGI	= new Config_Model_Bo_RlGrupoItem();
    	$a = $rlGI->find("id = '$id_rl'");

    	$idNoticia = $a['valor']->id_item;

    	$ib = new Content_Model_Bo_ItemBiblioteca();
    	$tib = new Config_Model_Bo_Tib();
		$rowsetDataItemBiblioteca = $ib->find("id_ib_pai = '$idNoticia'");

		$arrPerfis = array("Conteudo");
    	$arrFilhos  =   array();
    	if (count($rowsetDataItemBiblioteca) > 0) {
        	foreach ($rowsetDataItemBiblioteca as $chave => $campo){
				$arrFilhos[$chave]['item']      =   $campo->toArray();
				$rowsetDataItemBibliotecaTemplate = $tib->find("id = '" .$campo->id_tib."'");
	            foreach ($rowsetDataItemBibliotecaTemplate as $key => $template ){
    	            $arrFilhos[$chave]['template']  =   $template->toArray();
        	    }
        	}
    	}
    	$arrOrderm =  array();
    	foreach ( $arrFilhos as $chave => $item ){
    		$tibMeta = new Config_Model_Dao_TibMetadata();
    		$wsOrdem = $tibMeta->fetch("id_tib = '".$item['item']['id_tib']."' and metanome = 'ws_ordem' and id_tib_pai = '".$item['template']['id_tib_pai']."'");

    		$arrOrderm[$wsOrdem['valor']->id_tib]	= $wsOrdem['valor']->valor;
    	}

	    $campos =   array();
	    foreach ( $arrFilhos as $chave => $item ){
	    	$chave = $arrOrderm[$item['item']['id_tib']];
	        $campos[$chave]['nome']         = $item['template']['nome'];
	        $campos[$chave]['id']           = $item['item']['id'];
	        $campos[$chave]['tipo']         = $item['template']['tipo'];
	        $campos[$chave]['metanome']     = $item['template']['metanome'];
	        $campos[$chave]['id_pai']       = $item['template']['id_tib_pai'];
	        $campos[$chave]['valor']        = $item['item']['valor'];
	        $campos[$chave]['id_tib']        = $item['item']['id_tib'];

	        if ( $item['template']['tipo'] == 'ref_itemBiblioteca') {
	            $ar = $ib->find("id = '".$item['item']['valor']."'");
	            $campos[$chave]['aliase']   =   $ar->id;
	        }
    	}

    	ksort ($campos);

    	$arrCampos = array();
    	$arrCampos["Conteudo"] = $campos;

    	$this->view->file = 'form.html.twig';
    	$this->view->data = array( 'id' => $a['valor']->id,'perfis' => $arrPerfis, 'campos' => $arrCampos);
    }

    public function updateNoticiaAction(){
    	$identity 	= Zend_Auth::getInstance()->getIdentity();
    	$id_rl	= $this->getParam('id');

		$rlGP = new Config_Model_Bo_RlGrupoPessoa();
		$relacaoGP = $rlGP->find("id_pessoa = '".$identity->id."'");
		$arrIdGrupos = array();
		foreach ($relacaoGP as $k1 => $v1){
			$db = Zend_Db_Table::getDefaultAdapter();
	    	$stmt1 = $db->query('WITH RECURSIVE arvore_grupos (id) AS
								(
								select id from tb_grupo where id = :grupo
								union all
								select tb_grupo.id from tb_grupo
								inner join arvore_grupos on tb_grupo.id_pai = arvore_grupos.id
								)
								select id from arvore_grupos', array(':grupo' => $v1->id_grupo));
	    	$rowsetGruposDoTime = $stmt1->fetchAll();

	    	foreach ($rowsetGruposDoTime as $ka => $va){
	    		$arrIdGrupos[] = $va['id'];
	    	}
	    	$arrIdGrupos[] = $v1->id_grupo;
		}
		$arrIdGrupos = array_unique($arrIdGrupos);
    	$rlGI	= new Content_Model_Dao_RlGrupoItem();
		$relacaoGI = $rlGI->fetch("id = '$id_rl'");

		try {
	    	if ( in_array($relacaoGI['valor']->id_grupo,$arrIdGrupos)){
	    		//editar o cara aqui
	    		$ib	= new Content_Model_Dao_ItemBiblioteca();
	    		foreach ($ib->fetch("id_ib_pai = '".$relacaoGI['valor']->id_item."'") as $kib => $vib){
	    			$tib = new Config_Model_Bo_Tib();
	    			$tibDaVez = $tib->find("id = '".$vib->id_tib."'");
	    			if (array_key_exists($vib->id.'_'.$tibDaVez['valor']->metanome,$_POST)){
		    			$arrIbToUpt = array();
		    			$arrIbToUpt	= $vib->toArray();
		    			$arrIbToUpt['valor'] = $_POST[$vib->id.'_'.$tibDaVez['valor']->metanome];
		    			$arrIbToUpt['id_time'] = $identity->time['id'];
		    			unset($arrIbToUpt['id_criador']);

		    			$condicao = $ib->getAdapter ()->quoteInto ( 'id = ?', $vib->id );
		    			$ib->update($arrIbToUpt, $condicao);
	    			}
	    		}
	    	} else {
	    		//criar o novo aqui
	    		$ib	= new Content_Model_Dao_ItemBiblioteca();
	    		$masterModelo = $ib->fetch("id = '".$relacaoGI['valor']->id_item."'");

	    		$arrNovasIbs = array();
	    		$idMasterNovaNoticia	=	UUID::v4();
	    		$dataDeCriacao			=	date("Y-m-d h:i:s.B");
	    		$arrNovasIbs['id']			=	$idMasterNovaNoticia;
	    		$arrNovasIbs['dt_criacao']	=	$dataDeCriacao;
	    		$arrNovasIbs['id_criador']	=	$identity->id;
	    		$arrNovasIbs['id_tib']		=	$masterModelo['valor']->id_tib;
	    		$ib->insert($arrNovasIbs);
	    		foreach ($ib->fetch("id_ib_pai = '".$relacaoGI['valor']->id_item."'") as $kib => $vib){
	    			$tib = new Config_Model_Bo_Tib();
	    			$tibDaVez = $tib->find("id = '".$vib->id_tib."'");
	    			$arrNovasIbs['id']			=	UUID::v4();
	    			$arrNovasIbs['dt_criacao']	=	$dataDeCriacao;
	    			$arrNovasIbs['valor']		=	$_POST[$vib->id.'_'.$tibDaVez['valor']->metanome];
	    			$arrNovasIbs['id_criador']	=	$identity->id;
	    			$arrNovasIbs['id_ib_pai']	=	$idMasterNovaNoticia;
	    			$arrNovasIbs['id_tib']		=	$vib->id_tib;
	    			$ib->insert($arrNovasIbs);
	    		}
	    		$arrRelacaoGINova = array();
	    		$arrRelacaoGINova['id']	=	UUID::v4();
	    		$arrRelacaoGINova['id_item'] = $idMasterNovaNoticia;
	    		$arrRelacaoGINova['id_grupo'] = $identity->time['id'];
	    		$rlGI->insert($arrRelacaoGINova);

	    	}
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}

        $this->_helper->FlashMessenger('Notícia salva com sucesso.');

        // ws target
        $this->redirect('content/noticia/listar-todas-noticias-por-time');
    }

    public function criarNoticiaAction(){
    	$tib	= new Config_Model_Dao_Tib();
    	$idTibNoticia = $tib->fetchRow("metanome = 'TPNOTICIA'")->id;

    	$Perfis  = 'Conteudo';

    	$db = Zend_Db_Table::getDefaultAdapter();
    	$stmt1 = $db->query("SELECT item.*, json_object (array_agg( meta.metanome ), array_agg( meta.valor) ) as metadatas, ordem.valor as ordem
                			FROM    tp_itembiblioteca       AS item
                			JOIN    tp_itembiblioteca_metadata AS meta
                    			ON  item.id = meta.id_tib
                			JOIN    (select * from tp_itembiblioteca_metadata where metanome = 'ws_ordem') AS ordem
			                    ON item.id = ordem.id_tib
			                WHERE   item.id_tib_pai = :id_tib_pai
			                GROUP BY item.id, ordem.valor
			                ORDER BY ordem.valor::INT", array(':id_tib_pai' => $idTibNoticia));
    	$rowsetTemplate = $stmt1->fetchAll();
		foreach ( $rowsetTemplate as $key => $value ) {
				$rowsetTemplate[$key]['metadatas'] = json_decode($value['metadatas']);
			}

			// montando campos
			$campos	=	array();
			if (count($rowsetTemplate) > 0){
				foreach ($rowsetTemplate as $key => $row){
					$campos[$Perfis][$key]['nome'] = $row['nome'];
					$campos[$Perfis][$key]['id'] = $row['id'];
					$campos[$Perfis][$key]['tipo'] = $row['tipo'];
					$campos[$Perfis][$key]['metanome'] = $row['metanome'];
					$campos[$Perfis][$key]['id_pai'] = $row['id_tib_pai'];
					$campos[$Perfis][$key]['metadatas'] = $row['metadatas'];
					$campos[$Perfis][$key]['perfil'] = $Perfis;
				}
			}
		$arrPerfis = array();
		$arrPerfis[] = $Perfis;
	   	$arrCampos = array();
    	$arrCampos["Conteudo"] = $campos;

    	$this->view->file = 'form.html.twig';
    	$this->view->data = array( 'perfis' => $arrPerfis, 'campos' => $arrCampos);
    }

    public function insertNoticiaAction(){
    	try {
    		unset($_POST['id']);
    		//criar o novo aqui
    		$identity 	= Zend_Auth::getInstance()->getIdentity();
    		$tib	= new Config_Model_Dao_Tib();
    		$rlGI	= new Content_Model_Dao_RlGrupoItem();
    		$ib	= new Content_Model_Dao_ItemBiblioteca();
    		$idTibNoticia = $tib->fetchRow("metanome = 'TPNOTICIA'")->id;

    		$arrNovasIbs = array();
    		$idMasterNovaNoticia	=	UUID::v4();
    		$dataDeCriacao			=	date("Y-m-d h:i:s.B");
    		$arrNovasIbs['id']			=	$idMasterNovaNoticia;
    		$arrNovasIbs['dt_criacao']	=	$dataDeCriacao;
    		$arrNovasIbs['id_criador']	=	$identity->id;
    		$arrNovasIbs['id_tib']		=	$idTibNoticia;
    		$ib->insert($arrNovasIbs);
    		foreach ($_POST as $kib => $vib){
    			$tib = new Config_Model_Bo_Tib();
    			$tibDaVez = $tib->find("id = '".explode('_',$kib)[0]."'");
    			$arrNovasIbs['id']			=	UUID::v4();
    			$arrNovasIbs['dt_criacao']	=	$dataDeCriacao;
    			$arrNovasIbs['valor']		=	$_POST[$kib];
    			$arrNovasIbs['id_criador']	=	$identity->id;
    			$arrNovasIbs['id_ib_pai']	=	$idMasterNovaNoticia;
    			$arrNovasIbs['id_tib']		=	explode('_',$kib)[0];
    			$ib->insert($arrNovasIbs);
    		}
    		$arrRelacaoGINova = array();
    		$arrRelacaoGINova['id']	=	UUID::v4();
    		$arrRelacaoGINova['id_item'] = $idMasterNovaNoticia;
    		$arrRelacaoGINova['id_grupo'] = $identity->time['id'];
    		$rlGI->insert($arrRelacaoGINova);

            $this->_helper->FlashMessenger('Notícia salva com sucesso.');

            // ws target
            $this->redirect('content/noticia/listar-todas-noticias-por-time');

    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function deleteAction() {
    	$identity 	= Zend_Auth::getInstance()->getIdentity();
    	$id_rl	= $this->getParam('id');

    	$rlGI	= new Content_Model_Dao_RlGrupoItem();
		$relacaoGI = $rlGI->fetch("id = '$id_rl' and id_grupo = '".$identity->time['id']."'");

		try {
	    	if ( !empty($relacaoGI['valor'])){
	    		$rlGI->delete("id = '".$id_rl."'");
	    		$ib	= new Content_Model_Dao_ItemBiblioteca();
	    		$ib->delete("id_ib_pai = '".$relacaoGI['valor']->id_item."'");
	    		$ib->delete("id = '".$relacaoGI['valor']->id_item."'");
	    	} else {
	    		//criar o novo aqui
	    		x('não pode ser deletado, ele não faz parte do seu grupo');
	    	}
    	} catch(Zend_Exception $ex) {
    		var_dump($dados);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function vincularAoGrupoLogadoAction(){
		$identity 	= Zend_Auth::getInstance()->getIdentity();
    	$id_rl	= $this->getParam('id');

    	$rlGI	= new Content_Model_Dao_RlGrupoItem();
    	$relacaoGI = $rlGI->fetch("id = '$id_rl'");

    	$db = Zend_Db_Table::getDefaultAdapter();
    	$stmt1 = $db->query('WITH RECURSIVE arvore_grupos (id) AS
								(
								select id from tb_grupo where id = :grupo
								union all
								select tb_grupo.id from tb_grupo
								inner join arvore_grupos on tb_grupo.id_pai = arvore_grupos.id
								)
								select id from arvore_grupos', array(':grupo' => $identity->time['id']));
    	$rowsetGruposDoTime = $stmt1->fetchAll();
    	$jaVinculadoAoTime	= false;
    	$arrIdGrupos = array();
    	foreach ($rowsetGruposDoTime as $ka => $va){
    		$relacaoGI2 = $rlGI->fetch("id_item = '".$relacaoGI['valor']->id_item."' and id_grupo = '".$va['id']."'");

    		if ( !empty($relacaoGI2['valor'])){
    			$jaVinculadoAoTime = true;
    		}
    	}

    	try {
    		if(!$jaVinculadoAoTime){
	    		$arrRelacaoGINova				= array();
	    		$arrRelacaoGINova['id']			= UUID::v4();
	    		$arrRelacaoGINova['id_item']	= $relacaoGI['valor']->id_item;
	    		$arrRelacaoGINova['id_grupo']	= $identity->time['id'];

	    		$rlGI->insert($arrRelacaoGINova);
	    	}
    	} catch(Zend_Exception $ex) {
    		var_dump($arrRelacaoGINova);
    		echo '<Br>-----------<Br>';
    		var_dump($ex->getMessage());
    		exit;
    	}
    }

    public function copiarParaTimeLogadoAcntion() {
		$identity 	= Zend_Auth::getInstance()->getIdentity();
    	$id_rl	= $this->getParam('id');

    	$rlGI	= new Content_Model_Dao_RlGrupoItem();
    	$relacaoGI = $rlGI->fetch("id = '$id_rl'");

    	$db = Zend_Db_Table::getDefaultAdapter();
    	$stmt1 = $db->query('WITH RECURSIVE arvore_grupos (id) AS
								(
								select id from tb_grupo where id = :grupo
								union all
								select tb_grupo.id from tb_grupo
								inner join arvore_grupos on tb_grupo.id_pai = arvore_grupos.id
								)
								select id from arvore_grupos', array(':grupo' => $identity->time['id']));
    	$rowsetGruposDoTime = $stmt1->fetchAll();
    	$jaVinculadoAoTime	= false;
    	$arrIdGrupos = array();
    	foreach ($rowsetGruposDoTime as $ka => $va){
    		$relacaoGI2 = $rlGI->fetch("id_item = '".$relacaoGI['valor']->id_item."' and id_grupo = '".$va['id']."'");

    		if ( !empty($relacaoGI2['valor'])){
    			$jaVinculadoAoTime = true;
    		}
    	}

    	if(!$jaVinculadoAoTime){
    		//criar o novo aqui
    		$ib	= new Content_Model_Dao_ItemBiblioteca();
    		$masterModelo = $ib->fetch("id = '".$relacaoGI['valor']->id_item."'");

    		try {
	    		$arrNovasIbs = array();
	    		$idMasterNovaNoticia	=	UUID::v4();
	    		$dataDeCriacao			=	date("Y-m-d h:i:s.B");
	    		$arrNovasIbs['id']			=	$idMasterNovaNoticia;
	    		$arrNovasIbs['dt_criacao']	=	$dataDeCriacao;
	    		$arrNovasIbs['id_criador']	=	$identity->id;
	    		$arrNovasIbs['id_tib']		=	$masterModelo['valor']->id_tib;
	    		$ib->insert($arrNovasIbs);
	    		foreach ($ib->fetch("id_ib_pai = '".$relacaoGI['valor']->id_item."'") as $kib => $vib){
	    			$tib = new Config_Model_Bo_Tib();
	    			$tibDaVez = $tib->find("id = '".$vib->id_tib."'");
	    			$arrNovasIbs['id']			=	UUID::v4();
	    			$arrNovasIbs['dt_criacao']	=	$dataDeCriacao;
	    			$arrNovasIbs['valor']		=	$vib->valor;
	    			$arrNovasIbs['id_criador']	=	$identity->id;
	    			$arrNovasIbs['id_ib_pai']	=	$idMasterNovaNoticia;
	    			$arrNovasIbs['id_tib']		=	$vib->id_tib;
	    			$ib->insert($arrNovasIbs);
	    		}
	    		$arrRelacaoGINova = array();
	    		$arrRelacaoGINova['id']	=	UUID::v4();
	    		$arrRelacaoGINova['id_item'] = $idMasterNovaNoticia;
	    		$arrRelacaoGINova['id_grupo'] = $identity->time['id'];

	    		$rlGI->insert($arrRelacaoGINova);
    		} catch(Zend_Exception $ex) {
    			var_dump($dados);
    			echo '<Br>-----------<Br>';
    			var_dump($ex->getMessage());
    			exit;
    		}
    	}
    }
}