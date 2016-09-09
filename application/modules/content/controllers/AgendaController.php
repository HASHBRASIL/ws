<?php
class Content_AgendaController extends App_Controller_Action
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

    public function listarCompromissosAction(){
    	$identity = Zend_Auth::getInstance()->getIdentity();
    	$ibBo	= new Content_Model_Bo_ItemBiblioteca();
    	$tib	= new Config_Model_Dao_Tib();
    	$rlGI	= new Content_Model_Dao_RlGrupoItem();
    	$grupo	= new Config_Model_Bo_Grupo();

    	$idTibAgenda = $tib->fetchRow("metanome = 'TPAGENDA'")->id;
    	$idTibTitulo = $tib->fetchRow("metanome = 'eventotitulo'")->id;
    	$idTibDtInicio	 = $tib->fetchRow("metanome = 'eventodtinicio'")->id;
    	$idTibDtFim  = $tib->fetchRow("metanome = 'eventodtfim'")->id;

    	$compromissos = $ibBo->find("id_tib = '$idTibAgenda' and id_time = '".$identity->time['id']."'",'',30,0);

    	$i = 0;
    	$rowset = array();
    	foreach ($compromissos as $k => $n){

    		$titulo = $ibBo->find("id_ib_pai = '".$n->id."' and id_tib = '$idTibTitulo'");
    		$dataDeInicio = $ibBo->find("id_ib_pai = '".$n->id."' and id_tib = '$idTibDtInicio'");
    		$dataDeTermino = $ibBo->find("id_ib_pai = '".$n->id."' and id_tib = '$idTibDtFim'");

    		$arrRlGrupo	= $rlGI->fetchAll("id_item = '".$n->id."'");
    		foreach ( $arrRlGrupo as $k => $rl){
    			$db = Zend_Db_Table::getDefaultAdapter();
    			$stmt = $db->query('SELECT * FROM rastroGrupo(:grupo)', array(':grupo' => $rl->id_grupo));
    			$rowsetStrG = $stmt->fetchAll();
    			$strgrupo	= str_replace('"', '', $rowsetStrG[0]['rastrogrupo']);
    			$strgrupo	= str_replace('[', '', $strgrupo);
    			$strgrupo	= str_replace(']', '', $strgrupo);
    			$strgrupo	= str_replace(',', ' | ', $strgrupo);

    			if ( !empty($titulo['valor'])){$rowset[$i]['titulo'] = $titulo['valor']->valor;}
    			if ( !empty($dataDeInicio['valor'])){$rowset[$i]['dataInicio'] = $dataDeInicio['valor']->valor;}
    			if ( !empty($dataDeTermino['valor'])){$rowset[$i]['fonteFim'] = $dataDeTermino['valor']->valor;}
    			$rowset[$i]['grupo'] = $strgrupo;
    			$rowset[$i]['id'] = $rl->id;
    			$i++;
    		}
    	}

    	$header = array();
    	$header[] = array('campo' => 'grupo', 'label' => 'Grupo - Hierarquia');
    	$header[] = array('campo' => 'titulo', 'label' => 'Título');
    	$header[] = array('campo' => 'dataInicio', 'label' => 'Inicio');
    	$header[] = array('campo' => 'dataFim', 'label' => 'Final');

    	$this->view->file = 'paginator.html.twig';//'index.html.twig';
    	$this->view->data = array('data' => $rowset, 'header' => $header);
    }

     public function editarAgendaAction(){
     	$identity = Zend_Auth::getInstance()->getIdentity();
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
     			$ar = $ib->find("id = '".$item['item']['id']."'");
	            $campos[$chave]['aliase']   =   $ar['valor']->id;
     		}
     	}

     	$tibDao = new Config_Model_Dao_Tib();
     	$idTibTipo = $tibDao->fetchRow("metanome = 'eventotipo'")->id;
     	$ibDao = new Config_Model_Dao_ItemBiblioteca();
     	$tipos	=	$ibDao->fetchAll("id_tib = '$idTibTipo' and id_time = '".$identity->time['id']."'");

     	$optionsTipo = array();
     	$i = 0;
     	foreach ( $tipos as $chave_ib => $valor_ib){
     		//x($valor_ib);
     		$optionsTipo[$i]['id'] = $valor_ib['id'];
     		$optionsTipo[$i]['valor'] = $valor_ib['valor'];
     		$i++;
     	}
x($optionsTipo);
     	ksort ($campos);

     	$arrCampos = array();
     	$arrCampos["Conteudo"] = $campos;

     	$this->view->file = 'form.html.twig';
     	$this->view->data = array( 'id' => $a['valor']->id,'perfis' => $arrPerfis, 'campos' => $arrCampos);
     }

     public function updateAgendaAction() {
     	x($_POST);
     }
}