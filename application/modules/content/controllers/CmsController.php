<?php
class Content_CmsController extends App_Controller_Action
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

    public function retrieveConteudoAction()
    {
    	//pegar o time -- alterar para o
    	$this->identity = Zend_Auth::getInstance()->getIdentity();
    	$time = $this->identity->time['id'];

    	$grupo = new Config_Model_Dao_Grupo();

    	$site = $grupo->fetchRow("id_pai = '$time' and metanome = 'SITE'");

    	if (is_object($site)) {
	    	$arrGrupsPai = $grupo->fetchAll("id_pai = '".$site->id."'");
	    	//var_dump($site);
	    	$arvore = array();
	    	$i = 0;
	    	foreach ($arrGrupsPai as $k => $v){
		   		// montando arvore por precaução
	    		$arvore[$i]['nome'] = $v->nome;
                        $arvore[$i]['uuid'] = $v->id;
	    		$arrfilhos = $grupo->fetchAll("id_pai = '".$v->id."'");
	    		$if = 0;
	    		foreach ($arrfilhos as $kf => $vf){
	    			$grupo_meta = new Config_Model_Dao_GrupoMetadata();
	    			if (!is_null($grupo_meta->fetchRow("id_grupo = '".$vf->id."' and valor = 'itensDetalhe' and metanome = 'cms_area_conteudo'"))){
	    				$arvore[$i]['filhos'][$if]['id'] = $vf->id;
	    			}
                                $order = $grupo_meta->fetchRow("id_grupo = '$vf->id' and metanome = 'cms_ordem'");
                                if(!empty($order)){
                                    $arvore[$i]['filhos'][$if]['cms_ordem'] = $order->valor;
                                }

	    			$arvore[$i]['filhos'][$if]['nome'] = $vf->nome;
	    			$if++;
	    		}
	    		$i++;
	    	}
	    	$arrCampos = array();
	    	$arrCampos['Conteúdo da página'][0] = array(
	    			'id'            => 'conteudo',
	    			'ordem'         => '0',
	    			'obrigatorio'   => 'true',
	    			'nome'          => 'Título',
	    			'metanome'		=> 'titulo',
	    			'tipo'          => 'text',
	    			'perfil'        => 'servico',
	    			'metadatas'     => array(
	    					'ws_ordemLista'         => '1',
	    					'ws_style'              => 'col-md-12',
	    					'ws_style_object'	=> 'select2-skin'
	    			)
	    	);
	    	$arrCampos['Conteúdo da página'][1] = array(
	    			'id'            => 'conteudo',
	    			'ordem'         => '0',
	    			'obrigatorio'   => 'true',
	    			'nome'          => 'Texto',
	    			'metanome'		=> 'texto',
	    			'tipo'          => 'rich',
	    			'perfil'        => 'servico',
	    			'metadatas'     => array(
	    					'ws_ordemLista'         => '1',
	    					'ws_style'              => 'col-md-12',
	    					'ws_style_object'	=> 'select2-skin'
	    			)
	    	);
	    	$arrCampos['Conteúdo da página'][2] = array(
	    			'id'            => 'id',
	    			'metanome'		=> 'titulo',
	    			'tipo'          => 'hidden',
	    			'perfil'        => 'servico',
	    	);
	    	$arrCampos['Conteúdo da página'][3] = array(
	    			'id'            => 'id',
	    			'metanome'		=> 'conteudo',
	    			'tipo'          => 'hidden',
	    			'perfil'        => 'servico',
	    	);
	    	$arrCampos['Conteúdo da página'][4] = array(
	    			'id'            => 'texto',
	    			'metanome'		=> 'ckeditor',
	    			'tipo'          => 'hidden',
	    			'perfil'        => 'servico',
	    	);




            $novaArvore = array((object) array("id" => 'site', "parent" => "#", "text" => 'site', "type" => "site"));
            //monta os pais
            foreach( $arvore as $key => $value ) {
                $novaArvore[] = (object) array("id" => $value['uuid'], 'uuid' => $value['uuid'], "parent" => "site", "text" => $value['nome']);
            }



            for($i = 0; $i < count($arvore); ++$i)
            {
                if(!empty($arvore[$i]['filhos']) && is_array($arvore[$i]['filhos'])){
                    usort($arvore[$i]['filhos'], function ($a, $b){ return @$a['cms_ordem'] > @$b['cms_ordem']; });
                }
            }

            //monta os filhos
            foreach( $arvore as $key => $value ) {
                if(isset($value['filhos'])){
                    foreach( $value['filhos'] as $chave => $valor ) {
                        if(isset($valor['id']) ) {
                            $objId = $valor['id'];
                        }else {
                            $objId = 'nao_tem_id_'.$chave;
                        }

                        $novaArvore[] = (object) array("id" => $objId, "parent" => $value['uuid'], "text" => $valor['nome'], "type" => "root");
                    }
                }
            }

            $arrPerfis = array();
            $arrPerfis[] = 'Conteúdo da página';
            $this->view->file = 'incluir-conteudo.twig';

            $this->view->data = array('novoItens' => $novaArvore, 'itens' => $arvore, 'perfis' => $arrPerfis,'campos' => $arrCampos);
    	} else {
    		$this->view->file = 'mensagem.twig';
    		$this->view->data = array('mensagem' => 'Este time não possui conteúdo para edição.');
    	}
    }

    public function buscarConteudoIbAction(){
    	$this->identity = Zend_Auth::getInstance()->getIdentity();
    	$idGrupo= $this->getParam('id');
    	$ib = new Config_Model_Dao_ItemBiblioteca();
    	$tib = new Config_Model_Dao_Tib();
    	$tib_master = $tib->fetchRow("metanome = 'TPINSTITUCIONAL'");
    	$tib_titulo = $tib->fetchRow("metanome = 'TPTITULOINSTITUCIONAL'");
    	$tib_conteudo = $tib->fetchRow("metanome = 'TPCONTEUDOINSTITUCIONAL'");
    	$rlGI = new Config_Model_Dao_RlGrupoItem();

		$arrIbs = $ib->fetchAll("id_tib = '".$tib_master->id. "'");
		$id_ibs = '';
		$contagem = count($arrIbs);
		foreach ($arrIbs as $k => $v){
			$contagem--;
			$id_ibs .= "'".$v->id."'";
			if ( $contagem > 0 ){
				$id_ibs .= ",";
			}
		}
    	$relacao = $rlGI->fetchRow("id_grupo = '$idGrupo' and id_item in ($id_ibs)");
		$arrItens = $ib->fetchAll("id_ib_pai = '".$relacao->id_item."'");
		$arrConteudo = array();
		foreach ($arrItens as $ki => $vi){
			switch ($vi->id_tib){
				case $tib_conteudo->id:
					$arrConteudo['conteudo']['valor'] = $vi->valor;
					$arrConteudo['conteudo']['id'] = $vi->id;
					break;
				case $tib_titulo->id:
					$arrConteudo['titulo']['valor'] = $vi->valor;
					$arrConteudo['titulo']['id'] = $vi->id;
					break;
			}
		}
		echo json_encode($arrConteudo);

    	die();
    }

    public function updateConteudoAction(){
    	try {
//		x($_POST);
	    	$ib = new Config_Model_Dao_ItemBiblioteca();

	    	$titulo = $ib->fetchRow("id = '".$_POST['id_titulo']."'");
	    	$texto	=  $ib->fetchRow("id = '".$_POST['id_conteudo']."'");

	    	$titulo->valor = $_POST['conteudo_titulo'];
	    	$texto->valor = $_POST['conteudo_texto'];

	    	$titulo->save();
	    	$texto->save();
	    } catch(Zend_Exception $ex) {
	    	var_dump($dados);
	    	echo '<Br>-----------<Br>';
	    	var_dump($ex->getMessage());
	    	exit;
	    }
    }
}
