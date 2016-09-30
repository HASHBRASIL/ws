<?php

    class Content_PessoaController extends App_Controller_Action_Twig
    {


        /**
         * @var Legacy_Model_Bo_Pessoa
         */
        protected $_bo;

        public function init()
        {
            parent::init();
            $this->_bo = new Legacy_Model_Bo_Pessoa();

            $objGrupo = new Config_Model_Bo_Grupo();

            if (isset($this->servico['id_grupo'])){
                $this->_grupo = $this->servico['id_grupo'];
            } elseif (isset($this->servico['metadata']['ws_grupo'])){
                $grupos = $objGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'],$this->servico['metadata']['ws_grupo']);
                if(!empty($grupos)){
                    $this->_grupo = current($grupos)['id'];
                } else {
                    echo "Grupo destino não encontrado. Favor verificar metadata.";
                    die();
                }
            } else {
                $this->_grupo = $this->identity->time['id'];
            }
        }

        function gridAction () {

            //$gridHeader = $this->_bo->getGridHeader($this->servico);
            //x($gridHeader);

            //$this->header = $gridHeader['header'];
            //x($this->servico);
            
            //x($this->_gridSelect->__toString());
            
            $this->header = array();
            $this->header[] = array('campo' => 'nome', 'label' => 'Nome');
            $this->header[] = array('campo' => 'nome2', 'label' => 'Sobrenome');

            $busca = $this->_bo->selectGrid($this->_grupo, $this->servico['metadata']['ws_classificacao'], $this->servico['metadata']['ws_perfil'], null);

            // // x($ret);
            // x($ret->__toString());

            $this->_gridSelect      = $busca['query'];
            $this->_countGridSelect = $busca['count'];
            
            parent::gridAction();
            
            $modelServico     = new Config_Model_Bo_Servico();
            $servicoPaginador = $modelServico->getServicoEmUmaArvore($this->servico['id'], 'PAGINADORAJAX');
            
            $this->view->data['linkPaginador'] = $servicoPaginador['id'];

        } 

        public function getPaginationAction()
        {
            $this->_helper->layout->disableLayout();

            $params = $this->getRequest()->getParam('params');
            
            foreach($params as $key => $value) { $this->setParam($key, $value); }

            $busca  = $this->_bo->selectGrid2(  $this->identity->time['id'], 
                                                $this->servico['ws_tipopessoa'],
                                                null,
                                                $this->servico['ws_classificacao'],
                                                json_decode($params['filtro']), true);
            
            $paginator = Zend_Paginator::factory($busca['query']);
            
            $fO = array('lifetime' => 3600, 'automatic_serialization' => true);
            $bO = array('cache_dir'=> APPLICATION_PATH.'/general/cache');
            $cache = Zend_cache::factory('Core', 'File', $fO, $bO);
            Zend_Paginator::setCache($cache);
            
            $paginator  ->setCurrentPageNumber( !empty($params['page']) ? $params['page'] : 1)
                        ->setItemCountPerPage( !empty($params['itens']) ? $params['itens'] : 50)
                        ->setPageRange(6);

            $this->view->paginator = $paginator;
            $this->view->data      = array('data' => $paginator, 'header' => $this->header);
            $this->view->file      = 'pagination.html.twig';
        }
        
        function grid2Action () {

            if(isset($this->servico['ws_filtro'])) {
                $filtros = json_decode($this->servico['ws_filtro']);
            }
            //$this->_gridSelect = $this->_bo->selectGrid2($this->identity->time['id'],$this->servico['ws_tipopessoa'],$this->servico['ws_informacao'],$this->servico['ws_classificacao'],$filtros);
            $busca = $this->_bo->selectGrid2($this->identity->time['id'],$this->servico['ws_tipopessoa'],null,$this->servico['ws_classificacao'],$filtros);

            //$this->_countGridSelect = $this->_bo->countGrid2($this->identity->time['id'],$this->servico['ws_tipopessoa'],$this->servico['ws_classificacao'],$filtros);
  
            $this->_gridSelect      = $busca['query'];
            $this->_countGridSelect = $busca['count'];
            
            
            $this->header = array();
            $this->header[] = array('campo' => 'nome', 'label' => 'Nome');
            $this->header[] = array('campo' => 'nome2', 'label' => 'Sobrenome');

            $lstInf = explode(',',$this->servico['ws_informacao']);
            if((count($lstInf)>1) || ((count($lstInf)==1) && (strlen($lstInf[0])>0)) ) {
                foreach($lstInf as $cnt=>$inf){
                    $this->header[] = array('campo' => "info{$cnt}", 'label' => strtolower($inf));
                }
            }
            
            parent::gridAction();
            
            $modelServico     = new Config_Model_Bo_Servico();
            $servicoPaginador = $modelServico->getServicoEmUmaArvore($this->servico['id'], 'PAGINADORAJAX');
            
            $this->view->data['linkPaginador'] = $servicoPaginador['id'];
            echo 2;
        } 

        function importacontatoAction () {
            ini_set("auto_detect_line_endings", true);
            set_time_limit(0);
            $svcBo = new Config_Model_Bo_Servico();
            $tinfBo = new Config_Model_Bo_TipoInformacao();
            $rlPI = new Config_Model_Bo_RlPerfilInformacao();
            $infBo = new Config_Model_Bo_Informacao();
            $rgiBo = new Config_Model_Bo_RlGrupoInformacao();
            $rvpBo = new Config_Model_Bo_RlVinculoPessoa();
            $clsBo = new Config_Model_Bo_Classificacao();

            $target = "";
            if (isset($this->servico['ws_target']) && $this->servico['ws_target']) {
                $target = current($svcBo->getServicoByMetanome($this->servico['ws_target']))['id'];
            } else {
                $target = $this->servico['id_pai'];
            }

            $classificacao = array();
            if (isset($this->servico['ws_classificacao']) && $this->servico['ws_classificacao']) {
                $classificacao = explode(',',$this->servico['ws_classificacao']);
            }

            $this->_bo->_dao->beginTransaction();
            try {
                $ret = $this->_localSave();

                $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

                $caminho = $filedir->path . $ret['caminho'];
                $row = 0;
                $tamanho = 0;
                $campos = array();
                $master = array();
                if (($handle = fopen($caminho, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle,0,';')) !== FALSE) {
                        if($row==0) {
                            $tamanho = count($data);
                            foreach($data as $linha=>$campo) {
                                $campos[$linha] = current($tinfBo->getByMetanome(str_replace(array(1,2,3,),'',$campo)));
                                if($campos[$linha]==null) {
                                    $ori = $ret['original'];
                                    $msg = "Planilha $ori com não conformidades de campos. Campo $campo não encontrado. Favor revisar as colunas utilizadas.";
                                    Throw new Exception($msg);
                                }
                                $multiplo = $rlPI->getByInformacaoMultiplo($campos[$linha]['id']);
                                $campos[$linha]['multiplo'] = $multiplo != null;
                                $campos[$linha]['nomecampo'] = $campo;
                                if($campos[$linha]['id_pai']!= null) {
                                    $master[$campos[$linha]['id_pai']] = current($tinfBo->getById($campos[$linha]['id_pai']));
                                    $multiplo = $rlPI->getByInformacaoMultiplo($campos[$linha]['id_pai']);
                                    $master[$campos[$linha]['id_pai']]['multiplo'] = $multiplo != null;
                                }
                            }

                        } else {
                            if(count($data)==$tamanho) {
                                $multiplo = array();
                                $masterib = array();
                                $idPessoa = null;
                                $info = null;
                                foreach($data as $linha=>$valor) {
                                    $valor = mb_convert_encoding($valor, 'UTF-8');
                                    if($linha==0) {
                                        $idPessoa = $this->_bo->persiste(null,$valor,null);
                                        $info = $infBo->persiste(null,$campos[$linha]['id'],$idPessoa,null,$valor);
                                    } else {
                                        if($campos[$linha]['nomecampo']==$campos[$linha]['metanome']){
                                            if($campos[$linha]['id_pai']==null) {
                                                $info = $infBo->persiste(null,$campos[$linha]['id'],$idPessoa,null,$valor);
                                            } else {
                                                if(!isset($masterib[$campos[$linha]['id_pai']])) {
                                                    $masterib[$campos[$linha]['id_pai']] = $infBo->persiste(null,$campos[$linha]['id_pai'],$idPessoa,null,null);
                                                    $rgiBo->persiste(null,$this->identity->time['id'],$idPessoa,$masterib[$campos[$linha]['id_pai']]);
                                                } 
                                                $info = $infBo->persiste(null,$campos[$linha]['id_pai'],$idPessoa,$masterib[$campos[$linha]['id_pai']],$valor);
                                            }
                                        } else {
                                            if($campos[$linha]['id_pai']==null) {
                                                $info = $infBo->persiste(null,$campos[$linha]['id'],$idPessoa,null,$valor);
                                            } else {
                                                if(!isset($masterib[$campos[$linha]['id_pai']][str_replace($campos[$linha]['metanome'],'',$campos[$linha]['nomecampo'])])) {
                                                    $masterib[$campos[$linha]['id_pai']][str_replace($campos[$linha]['metanome'],'',$campos[$linha]['nomecampo'])] = $infBo->persiste(null,$campos[$linha]['id_pai'],$idPessoa,null,null);
                                                    $rgiBo->persiste(null,$this->identity->time['id'],$idPessoa,$masterib[$campos[$linha]['id_pai']][str_replace($campos[$linha]['metanome'],'',$campos[$linha]['nomecampo'])]);
                                                } 
                                                $info = $infBo->persiste(null,$campos[$linha]['id_pai'],$idPessoa,$masterib[$campos[$linha]['id_pai']][str_replace($campos[$linha]['metanome'],'',$campos[$linha]['nomecampo'])],$valor);
                                            }
                                        }
                                    }
                                    
                                    $rgiBo->persiste(null,$this->identity->time['id'],$idPessoa,$info);                                    
                                }
                                foreach($classificacao as $cls) {
                                    $classe = current($clsBo->getByMetanome($cls)->toArray());
                                    $rvpBo->persiste(null,$classe['id'],$idPessoa,null,$this->identity->time['id'],null,null);
                                }
                            } else {
                                $ori = $ret['original'];
                                $msg = "Planilha $ori com linhas de tamanho diferente. Importação interrompida. Favor revisar dados.";
                                Throw new Exception($msg);
                            }
                        }
                        // $num = count($data);
                        // echo "<p> $num fields in line $row: <br /></p>\n";
                        $row++;
                        // for ($c=0; $c < $num; $c++) {
                        //     echo $data[$c] . "<br />\n";
                        // }
                    }
                    fclose($handle);
                }
                
                $row--;
                //$this->_bo->_dao->rollBack();
                $this->_bo->_dao->commit();
                $response = array(
                    'success' => true,
                    'msg' => $this->_translate->translate("Importação realizada com sucesso. $row pessoas importadas."),
                    'data' => array('target' => array('servico' => $target))
                );
                $this->_helper->json($response);
            } catch (Exception $e) {
                $this->_bo->_dao->rollBack();
                $response = array(
                    'success' => false,
                    'msg' => $this->_translate->translate($e->getMessage()),
                    'data' => array('target' => array('servico' => $target))
                // x($e->getMessage(),false);
                // x($e->getLine(),false);
                // x($e->getTrace());
                );

                $this->_helper->json($response);
            }
            // 
        }

        public function createAction() {
            $svcBo = new Config_Model_Bo_Servico();
            $tinfBo = new Config_Model_Bo_TipoInformacao();
            $rlPI = new Config_Model_Bo_RlPerfilInformacao();
            $infBo = new Config_Model_Bo_Informacao();
            $rgiBo = new Config_Model_Bo_RlGrupoInformacao();
            $rvpBo = new Config_Model_Bo_RlVinculoPessoa();
            $clsBo = new Config_Model_Bo_Classificacao();
            $ibBo = new Content_Model_Bo_ItemBiblioteca();

            $campos = $rlPI->getByPerfisMultiplo($this->servico['ws_perfil']);

            $arPerfil = explode(',', $this->servico['ws_perfil']);

            foreach ($campos as $campo) {
                $campo['metadatas'] = json_decode($campo['metadatas']);
                $arCampos[$campo['perfil']][] = $campo;
            }

            foreach ($arCampos as $chave => $campo) {
                foreach ($arCampos[$chave] as $key => $field) {
                    if ($field['tipo']=='ref_itemBiblioteca' && $field['lista']==true) {
                    $items = $ibBo->getAllItensByTibByOrdem($field['metadatas']->ws_tib, $field['metadatas']->ws_comboordem);
                    $field['items'] = array();
                    $countItem = 0;
                    foreach($items as $idItem => $valorItem) {
                        $field['items'][$countItem] = array();
                        $field['items'][$countItem]['id'] = $idItem;
                        $textoValor = $field['metadatas']->ws_comboform;
                        foreach($valorItem as $metanome => $txt){
                            $textoValor = str_replace($metanome,$txt,$textoValor);
                        }
                        $field['items'][$countItem]['valor'] = $textoValor;
                        $countItem++;
                    }
                }
                if ( !empty($field['id_pai']) ) {
                        $arrayCampos[$chave][$field['nome_pai']][] = $field;
                    } else {
                        if ( $field['tipo'] != 'Master' ) {
                            $arrayCampos[$chave][$key] = $field;
                        } else {
                            $arrayCampos['master'][$field['nome']] = $field;
                        }
                    }
                }
            }

            //echo $this->render('form.html.twig',);
            // x($arPerfil,false);
            // x($arrayCampos);
            $this->view->data =  array('perfis' => $arPerfil, 'campos' => $arrayCampos);

            $this->view->file = 'form.html.twig';
        }

        public function retrieveAction() {
            if (isset($this->servico['metadata']['ws_id']) && ($this->servico['metadata']['ws_id'])) {
                $param = array ('usr' => $identity->id, 'time' => $identity->time['id_representacao']);
                $uuidPessoa = $param[$this->servico['metadata']['ws_id']];
            } elseif ($this->getRequest()->getParam('id')) {
                $uuidPessoa = $this->getRequest()->getParam('id');
            } else {
                parseJson(true, 'É necessário selecionar uma pessoa para editar!');
            }

            $tpInformacao = new Config_Model_Bo_TipoInformacao();
            $pessoa = $this->_bo;
            $itemBiblioteca = new Content_Model_Bo_ItemBiblioteca();
            $tib = new Config_Model_Bo_Tib();

            $rsPessoa = $pessoa->getById($uuidPessoa);

            $rowPessoa = current($rsPessoa);

            $campos = $tpInformacao->getTpInformacaoByPerfisByPessoaByGrupo($this->servico['ws_perfil'], $uuidPessoa, $this->_grupo);

            $arPerfil = explode(',', $this->servico['ws_perfil']);

            foreach ($campos as $campo) {
                $campo['metadatas'] = json_decode($campo['metadatas']);
                $arCampos[$campo['perfil']][] = $campo;
            }

            //x($campos);

            foreach ($arCampos as $chave => $campo) {
                foreach ($arCampos[$chave] as $key => $field) {
                    if($field['tipo'] == 'ref_itemBiblioteca') {
                        if( $field['valor']) {
                            $reg = $itemBiblioteca->getById($field['valor']);
                            $padrao = $tib->getCampoPadrao(current($reg)['id_tib']);
                            $iblabel = $itemBiblioteca->getByPaiETIB($field['valor'],current($padrao)['id']);
                        }
                        if(!empty($iblabel)) {
                            $field['label'] = current($iblabel)['valor'];
                        }
                        if($field['lista']) {
                            $field['items'] = array();
                            $countItem = 0;
                            if(!$field['obrigatorio']){
                                $field['items'][$countItem]['id'] = '';
                                $field['items'][$countItem]['valor'] = '';
                                $countItem++;
                            }
                            $items = $itemBiblioteca->getAllItensByTibByOrdem($field['metadatas']->ws_tib, $field['metadatas']->ws_comboordem);
                            foreach($items as $idItem => $valorItem) {
                                $field['items'][$countItem] = array();
                                $field['items'][$countItem]['id'] = $idItem;
                                $textoValor = $field['metadatas']->ws_comboform;
                                foreach($valorItem as $metanome => $txt){
                                    $textoValor = str_replace($metanome,$txt,$textoValor);
                                }
                                $field['items'][$countItem]['valor'] = $textoValor;
                                $countItem++;
                            }
                        }
                    } else if ($field['tipo'] == 'ref_pessoa') {
                        if(preg_match('/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/', $field['valor'])){
                            $itemPessoa = $pessoa->getPessoaById($field['valor']);
                            $itemPessoa = current($itemPessoa);
                            $field['items'] = array(array('id'=>$field['valor'],'valor'=>$itemPessoa['nome']));
                        }
                    }
                    if ( !empty($field['id_pai']) ) {
                        if (!empty($field['tbinfopaiid'])) {
                            $arrayCampos[$chave][$field['nome_pai']][$field['tbinfopaiid']][] = $field;
                        } else {
                            $arrayCampos[$chave][$field['nome_pai']][] = $field;
                        }
                    } else {
                        if ( $field['tipo'] != 'Master' ) {
                            $arrayCampos[$chave][$key] = $field;
                        } else {
                            $arrayCampos['master'][$field['nome']] = $field;
                        }
                    }
                }       
            }

            // new dBug($arrayCampos);
            //die();

            
            //x($arrayCampos);
            //echo $twig->render('form.html.twig', array('perfis' => $arPerfil, 'campos' => $arrayCampos, 'id' => $uuidPessoa, 'pessoa' => $rowPessoa));
            $this->view->data =  array('perfis' => $arPerfil, 'campos' => $arrayCampos,'id' => $uuidPessoa, 'pessoa' => $rowPessoa);

            $this->view->file = 'form.html.twig';

        }

        public function insertAction() {
            $svcBo = new Config_Model_Bo_Servico();
            $tinfBo = new Config_Model_Bo_TipoInformacao();
            $rlPI = new Config_Model_Bo_RlPerfilInformacao();
            $infBo = new Config_Model_Bo_Informacao();
            $rgiBo = new Config_Model_Bo_RlGrupoInformacao();
            $rvpBo = new Config_Model_Bo_RlVinculoPessoa();
            $clsBo = new Config_Model_Bo_Classificacao();
            $ibBo = new Content_Model_Bo_ItemBiblioteca();

            $campos = $rlPI->getByPerfisMultiplo($this->servico['ws_perfil']);

            $arPerfil = explode(',', $this->servico['ws_perfil']);

            $data = $this->getRequest()->getParams();

            $classificacao = array();
            if (isset($this->servico['ws_classificacao']) && $this->servico['ws_classificacao']) {
                $classificacao = explode(',',$this->servico['ws_classificacao']);
            }

            $nome = '';
            $nome2 = '';
            $responsavel = '';
            $campos = array();
            $master = array();

            foreach ($data as $chave => $valor) {
                preg_match('/_/', $chave, $r);
                if( count( $r ) > 0 ){
                    $c  =   explode( '_', $chave);
                    if(($c[1]=='NOMEPESSOA') || ($c[1]=='RAZAOSOCIAL')){
                        $nome = $valor;
                    } else if(($c[1]=='SOBRENOME') || ($c[1]=='NOMEFANTASIA')){
                        $nome2 = $valor;
                    } else if(($c[1]=='RESPONSAVEL')){
                        $responsavel = $valor;
                    }
                    $strdata[ $c[1] ] = $valor;
                }
            }

            foreach($strdata as $campo=>$valor) {
                $campos[$campo] = current($tinfBo->getByMetanome($campo));
                
                $multiplo = $rlPI->getByInformacaoMultiplo($campos[$campo]['id']);
                $campos[$campo]['multiplo'] = $multiplo != null;
                $campos[$campo]['nomecampo'] = $campo;
                if($campos[$campo]['id_pai']!= null) {
                    $master[$campos[$campo]['id_pai']] = current($tinfBo->getById($campos[$campo]['id_pai']));
                    $multiplo = $rlPI->getByInformacaoMultiplo($campos[$campo]['id_pai']);
                    $master[$campos[$campo]['id_pai']]['multiplo'] = $multiplo != null;
                }
            }

            // x($campos,false);
            // x($master,false);
            // x($strdata);
            
            $this->_bo->_dao->beginTransaction();
            try {
                $multiplo = array();
                $masterib = array();
                $idPessoa = $this->_bo->persiste(null,$nome,$nome2);
                $info = null;
                foreach($strdata as $campo => $valor){
                    if(($campos[$campo]['multiplo'] == false ) && (($campos[$campo]['id_pai'] == null) || ($master[$campos[$campo]['id_pai']]['multiplo'] == false))){
                        if($campos[$campo]['id_pai']==null) {
                            $info = $infBo->persiste(null,$campos[$campo]['id'],$idPessoa,null,$valor);
                        } else {
                            if(!isset($masterib[$campos[$campo]['id_pai']])) {
                                $masterib[$campos[$campo]['id_pai']] = $infBo->persiste(null,$campos[$campo]['id_pai'],$idPessoa,null,null);
                                $rgiBo->persiste(null,$this->identity->time['id'],$idPessoa,$masterib[$campos[$campo]['id_pai']]);
                            } 
                            $info = $infBo->persiste(null,$campos[$campo]['id_pai'],$idPessoa,$masterib[$campos[$campo]['id_pai']],$valor);
                        }
                    } else {
                        if($campos[$campo]['id_pai']==null) {
                            if(is_array($valor)) {
                                foreach($valor as $item) {
                                    $info = $infBo->persiste(null,$campos[$campo]['id'],$idPessoa,null,$item);
                                }
                            } else {
                                $info = $infBo->persiste(null,$campos[$campo]['id'],$idPessoa,null,$valor);
                            }
                            
                        } else {
                            if(is_array($valor)) {
                                foreach($valor as $id => $item) {
                                    if(!isset($masterib[$campos[$campo]['id_pai']][$id])) {
                                        $masterib[$campos[$campo]['id_pai']][$id] = $infBo->persiste(null,$campos[$campo]['id_pai'],$idPessoa,null,null);
                                        $rgiBo->persiste(null,$this->identity->time['id'],$idPessoa,$masterib[$campos[$campo]['id_pai']][$id]);
                                    } 
                                    $info = $infBo->persiste(null,$campos[$campo]['id_pai'],$idPessoa,$masterib[$campos[$campo]['id_pai']][$id],$item);
                                }
                            } else {
                                if(!isset($masterib[$campos[$campo]['id_pai']])) {
                                $masterib[$campos[$campo]['id_pai']] = $infBo->persiste(null,$campos[$campo]['id_pai'],$idPessoa,null,null);
                                $rgiBo->persiste(null,$this->identity->time['id'],$idPessoa,$masterib[$campos[$campo]['id_pai']]);
                            } 
                            $info = $infBo->persiste(null,$campos[$campo]['id_pai'],$idPessoa,$masterib[$campos[$campo]['id_pai']],$valor);
                            }                            
                        }
                    }
                }
            
                $rgiBo->persiste(null,$this->identity->time['id'],$idPessoa,$info);

                foreach($classificacao as $cls) {
                    $classe = current($clsBo->getByMetanome($cls)->toArray());
                    $rvpBo->persiste(null,$classe['id'],$idPessoa,null,$this->identity->time['id'],null,null);
                }
                $this->_bo->_dao->commit();
                $response = array(
                    'success' => true,
                    'msg' => $this->_translate->translate("Pessoa cadastrada com sucesso."),
                    'data' => array('target' => array('servico' => $target))
                );
                $this->_helper->json($response);
            } catch (Exception $e) {
                $this->_bo->_dao->rollBack();
                $response = array(
                    'success' => false,
                    'msg' => $this->_translate->translate($e->getMessage()),
                    'data' => array('target' => array('servico' => $target))
                );

                $this->_helper->json($response);
            }

        }

        public function updateAction() {
            x($this->getRequest()->getParams());

            if(count($data)==$tamanho) {
                $multiplo = array();
                $masterib = array();
                $idPessoa = null;
                $info = null;
                foreach($data as $linha=>$valor) {
                    $valor = mb_convert_encoding($valor, 'UTF-8');
                    if($linha==0) {
                        $idPessoa = $this->_bo->persiste(null,$valor,null);
                        $info = $infBo->persiste(null,$campos[$linha]['id'],$idPessoa,null,$valor);
                    } else {
                        if($campos[$linha]['nomecampo']==$campos[$linha]['metanome']){
                            if($campos[$linha]['id_pai']==null) {
                                $info = $infBo->persiste(null,$campos[$linha]['id'],$idPessoa,null,$valor);
                            } else {
                                if(!isset($masterib[$campos[$linha]['id_pai']])) {
                                    $masterib[$campos[$linha]['id_pai']] = $infBo->persiste(null,$campos[$linha]['id_pai'],$idPessoa,null,null);
                                    $rgiBo->persiste(null,$this->identity->time['id'],$idPessoa,$masterib[$campos[$linha]['id_pai']]);
                                } 
                                $info = $infBo->persiste(null,$campos[$linha]['id_pai'],$idPessoa,$masterib[$campos[$linha]['id_pai']],$valor);
                            }
                        } else {
                            if($campos[$linha]['id_pai']==null) {
                                $info = $infBo->persiste(null,$campos[$linha]['id'],$idPessoa,null,$valor);
                            } else {
                                if(!isset($masterib[$campos[$linha]['id_pai']][str_replace($campos[$linha]['metanome'],'',$campos[$linha]['nomecampo'])])) {
                                    $masterib[$campos[$linha]['id_pai']][str_replace($campos[$linha]['metanome'],'',$campos[$linha]['nomecampo'])] = $infBo->persiste(null,$campos[$linha]['id_pai'],$idPessoa,null,null);
                                    $rgiBo->persiste(null,$this->identity->time['id'],$idPessoa,$masterib[$campos[$linha]['id_pai']][str_replace($campos[$linha]['metanome'],'',$campos[$linha]['nomecampo'])]);
                                } 
                                $info = $infBo->persiste(null,$campos[$linha]['id_pai'],$idPessoa,$masterib[$campos[$linha]['id_pai']][str_replace($campos[$linha]['metanome'],'',$campos[$linha]['nomecampo'])],$valor);
                            }
                        }
                    }
                    
                    $rgiBo->persiste(null,$this->identity->time['id'],$idPessoa,$info);                                    
                }
                foreach($classificacao as $cls) {
                    $classe = current($clsBo->getByMetanome($cls)->toArray());
                    $rvpBo->persiste(null,$classe['id'],$idPessoa,null,$this->identity->time['id'],null,null);
                }
            }
        }

    }