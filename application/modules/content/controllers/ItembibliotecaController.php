<?php

class Content_ItembibliotecaController extends App_Controller_Action_Twig {

    /**
     * @var Content_Model_Bo_Ib
     */
    protected $_bo;

    public function init() {
        parent::init();
        $this->_bo = new Content_Model_Bo_ItemBiblioteca();
        $objGrupo = new Config_Model_Bo_Grupo();

        if (isset($this->servico['id_grupo'])) {
            $this->_grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['metadata']['ws_grupo'])) {

            $grupos = $objGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'], $this->servico['metadata']['ws_grupo']);

            if (!empty($grupos)) {
                $this->_grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino n o encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $this->_grupo = $this->identity->grupo['id'];
        }
    }
    
    
    public function getPaginationAction()
    {
        $this->_helper->layout->disableLayout();

        $params = $this->getRequest()->getParam('params');

        foreach($params as $key => $value) { $this->setParam($key, $value); }

        $busca = $this->_bo->getItemBibliotecaGrid($params['id_tib'], $params['_grupo']);

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
    
    function gridAction()
    {
        $modelTPIB = new Content_Model_Bo_TpItemBiblioteca();

        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

        $this->header = $modelTPIB->getBasicConfigHeader($this->servico);

        $select = $this->_bo->getItemBibliotecaGrid($this->servico['id_tib'], $this->_grupo);

        $this->_gridSelect = $select['query'];
        $this->_countGridSelect = $select['count'];
        $this->view->filedir = $filedir;

        parent::gridAction();
        
        $modelServico     = new Config_Model_Bo_Servico();
        $servicoPaginador = $modelServico->getServicoEmUmaArvore($this->servico['id'], 'PAGINADORAJAX');

        $this->view->data['linkPaginador'] = $servicoPaginador['id'];
        $this->view->data['paramsPaginator']['id_tib'] = $this->servico['id_tib'];
        $this->view->data['paramsPaginator']['_grupo'] = $this->_grupo;
    }

    public function createAction()
    {
        $templateItemBiblioteca = new Config_Model_Bo_Tib();

        $itemBiblioteca = $this->_bo;
        $objGrupo = new Config_Model_Bo_Grupo();
        $pathRastro = new Rastro();
        $rastro = $pathRastro->getPath($this->servico['id']);
        //declarando variaveis
        $grupo = $this->_grupo;
        $servico = $this->servico['id_tib']; //tib_pai do cara
        $perfil = 'Conteúdo';

        // if (isset($this->servico['id_grupo'])) {
        //     $grupo = $this->servico['id_grupo'];
        // } elseif (isset($this->servico['metadata']['ws_grupo'])) {
        //     $grupos = $objGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'], $this->servico['metadata']['ws_grupo']);
        //     if (!empty($grupos)) {
        //         $grupo = current($grupos)['id'];
        //     } else {
        //         echo "Grupo destino n o encontrado. Favor verificar metadata.";
        //         die();
        //     }
        // } else {
        //     $grupo = $this->identity->grupo['id'];
        // }

        //carregando template
        $rowsetTemplate = $templateItemBiblioteca->getTemplateByIdTibPai($servico);

        foreach ($rowsetTemplate as $key => $value) {
            $rowsetTemplate[$key]['metadatas'] = json_decode($value['metadatas']);
        }

        // montando campos
        $campos = array();
        if (count($rowsetTemplate) > 0) {
            foreach ($rowsetTemplate as $key => $row) {
                $campos[$perfil][$key]['nome'] = $row['nome'];
                $campos[$perfil][$key]['id'] = $row['id'];
                $campos[$perfil][$key]['tipo'] = $row['tipo'];
                $campos[$perfil][$key]['metanome'] = $row['metanome'];
                $campos[$perfil][$key]['id_pai'] = $row['id_tib_pai'];
                $campos[$perfil][$key]['metadatas'] = $row['metadatas'];
                $campos[$perfil][$key]['perfil'] = $perfil;
                if ($row['tipo'] == 'ref_itemBiblioteca') {
                    $items = $itemBiblioteca->getAllItensByTibByOrdem($row['metadatas']->ws_tib, $row['metadatas']->ws_comboordem);
                    $campos[$perfil][$key]['items'] = array();
                    $qtd = 0;
                    foreach ($items as $idItem => $valorItem) {
                        $campos[$perfil][$key]['items'][$qtd] = array();
                        $campos[$perfil][$key]['items'][$qtd]['id'] = $idItem;
                        $textoValor = $row['metadatas']->ws_comboform;
                        foreach ($valorItem as $metanome => $txt) {
                            $textoValor = str_replace($metanome, $txt, $textoValor);
                        }
                        $campos[$perfil][$key]['items'][$qtd]['valor'] = $textoValor;
                        $qtd++;
                    }
                }
            }
        }

        $this->view->file = 'form.html.twig';
        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');
        $this->view->filedir = $filedir;
        $this->view->data = array('perfis' => array($perfil), 'campos' => $campos);
    }

    public function insertAction()
    {
        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');
        $post = $this->getRequest()->getPost();
        $params = $this->getRequest()->getParams();
        $modelGrupo = new Config_Model_Bo_Grupo();
        $modelServico = new Config_Model_Bo_Servico();
        $modelIB = new Content_Model_Bo_ItemBiblioteca();
        if (isset($this->servico['id_grupo'])) {
            $grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['metadata']['ws_grupo'])) {
            $grupos = $modelGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'], $this->servico['metadata']['ws_grupo']);
            if (!empty($grupos)) {
                $grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino n o encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $grupo = $this->identity->grupo['id'];
        }
//        x($params);
        $idTib = $this->servico['id_tib'];
          
        
        $campos = array();

        foreach ($post as $key => $ib) {
            $a = explode('_', $key);
            if (isset($a[1])) {
                $campos[$a[0]] = str_replace($filedir->url, "", $ib);
            }
        }

        $files = $this->_localSave();

        if ($files) {
            if (count($files) < 4) {
                foreach ($files as $file) {
                    $campos[$file['id_tib']] = $file['caminho'];
                }
            } else {
                $campos[$files['id_tib']] = $files['caminho'];
            }
        }
        if (key_exists('pessoas_PESSOA', $post)){
            $camposTemplate = (new Config_Model_Bo_Tib)->getTemplateByIdTibPai($this->servico['id_tib']);
            $arrayMensagens = array();
            $titulo = "";
            $status = "ABERTA";
//            x($post);
            foreach ($post as $chave => $item) {
                if ($chave == 'titulo_titulo') {
                    $titulo = $item;
                } else {
                    $titulo = "CONVITE - " . $this->identity->nomeusuario;
                }
                $arrayPessoa = array(
                    "msg" => "",
                    "idpessoa" => $this->identity->id,
                    "idgrupo" => $grupo,
                    "pessoas" => array()
                ); 
                if ($chave == 'pessoas_PESSOA') {
                    $array = json_decode($item);
                    $arraypessoastratadas = array();
                    foreach ($array as $itemarray) {
                        $resultado = explode("_", $itemarray);
                        $arraypessoastratadas[] = $resultado[1];
                    }
                    $arrayPessoa['pessoas'] = $arraypessoastratadas;
                    $status = "NOVA";
                }
                if ($chave == 'tpmsg_TPMSG') {
                    $arrayMsg = explode("|", $item);
                    if (count($arrayMsg) > 1) {
                        foreach ($arrayMsg as $msg) {
                            $arrayPessoa['msg'] = $msg;
                            $arrayMensagens[] = $arrayPessoa;
                        }
                    } else {
                        $arrayPessoa['msg'] = $item;
                        $arrayMensagens = array($arrayPessoa);
                    }
                }
            }
            
            $campos = array();
            foreach ($camposTemplate as $template) {
                switch ($template['metanome']) {
                    case "status" : $campos[$template['id']] = $status; break;
                    case "nome" : $campos[$template['id']] = $titulo; break;
                    case "dtiniproc" : $campos[$template['id']] = NULL; break;
                    case "preprocjson" : $campos[$template['id']] = json_encode($arrayMensagens); break;
                    case "dtfimproc" : $campos[$template['id']] = NULL; break;
                    case "dtcriacao" : $campos[$template['id']] = date('Y-m-d H:i:s'); break;
                }
            }
        }
        
        $idPessoa = $this->identity->id != NULL ? $this->identity->id : NULL;

        $servico = $modelServico->getServicoByMetanome($this->servico['metadata']['ws_target']);
        $servicoDestino = current($servico)['id'];

        $idIb = $modelIB->insere($idTib, $idPessoa, $campos);

        $modelIB->addRelGrupoItem($grupo, $idIb);
        $response = array(
            'success' => true,
            'msg' => $this->_translate->translate("Salvo com sucesso!"),
            'data' => array('target' => array('servico' => $servicoDestino))
        );
        $this->_helper->json($response);
    }

    public function retrieveAction()
    {
        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');
        $pathRastro = new Rastro();
        $rastro = $pathRastro->getPath($this->servico['id']);
        $tib = $this->servico['id_tib']; //tib_pai do cara

        $perfil = 'Conteúdo';

        $tpItemBiblioteca = new Config_Model_Bo_Tib();
        $itemBiblioteca = new Content_Model_Bo_ItemBiblioteca();

        if ($this->getRequest()->getParam('id')) {
            $idItemBiblioteca = $this->getRequest()->getParam('id');
        } else {
            // @todo erro!
            parseJson(true, 'É necessário selecionar item para editar!');
        }
        $rowsetDataItemBiblioteca = $itemBiblioteca->getFilhosByIdPai($idItemBiblioteca);

        $arrPerfis = array($perfil);
        $arrFilhos = array();
        if (count($rowsetDataItemBiblioteca) > 0) {
            foreach ($rowsetDataItemBiblioteca as $chave => $campo) {

                $arrFilhos[$chave]['item'] = $campo;

                $rowsetDataItemBibliotecaTemplate = $tpItemBiblioteca->getTemplateById($campo['id_tib']);

                foreach ($rowsetDataItemBibliotecaTemplate as $key => $template) {
                    $arrFilhos[$chave]['template'] = $template;
                }
            }
        }

        //carregando template
        $rowsetTemplate = $tpItemBiblioteca->getTemplateByIdTibPai($tib);
        foreach ($rowsetTemplate as $key => $value) {
            $rowsetTemplate[$key]['metadatas'] = json_decode($value['metadatas']);
        }

        // montando campos
        $campos = array();
        if (count($rowsetTemplate) > 0) {
            foreach ($rowsetTemplate as $key => $row) {

                $campos[$perfil][$key]['nome'] = $row['nome'];
                $campos[$perfil][$key]['id'] = $row['id'];
                $campos[$perfil][$key]['tipo'] = $row['tipo'];
                $campos[$perfil][$key]['metanome'] = $row['metanome'];
                $campos[$perfil][$key]['id_pai'] = $row['id_tib_pai'];
                $campos[$perfil][$key]['metadatas'] = $row['metadatas'];
                $campos[$perfil][$key]['mascara'] = $row['mascara'];
                $campos[$perfil][$key]['perfil'] = $perfil;
                $campos[$perfil][$key]['valor'] = '';
                if ($row['tipo'] == 'ref_itemBiblioteca') {
                    $items = $itemBiblioteca->getAllByTib($row['metadatas']->ws_tib, $row['metadatas']->ws_comboordem);
                    $campos[$perfil][$key]['items'] = array();
                    $qtd = 0;
                    foreach ($items as $idItem => $valorItem) {
                        $campos[$perfil][$key]['items'][$qtd] = array();
                        $campos[$perfil][$key]['items'][$qtd]['id'] = $idItem;
                        $textoValor = $row['metadatas']->ws_comboform;
                        foreach ($valorItem as $metanome => $txt) {
                            $textoValor = str_replace($metanome, $txt, $textoValor);
                        }
                        $campos[$perfil][$key]['items'][$qtd]['valor'] = $textoValor;
                        $qtd++;
                    }
                }
            }
        }

        foreach ($arrFilhos as $chave => $item) {
            foreach ($campos[$perfil] as $chave => $campo) {
                if ($campo['id'] === $item['item']['id_tib']) {
                    $campos[$perfil][$chave]['valor'] = $item['item']['valor'];
                }
            }
        }
        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');
        $this->view->filedir = $filedir;
        $this->view->file = 'form.html.twig';
        $this->view->data = array('perfis' => array($perfil), 'campos' => $campos, 'id' => $idItemBiblioteca, 'filedir' => $filedir->url);
    }

    public function updateAction()
    {
        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');
        $post = $this->getRequest()->getPost();
        $idIbMaster = $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();
        $modelGrupo = new Config_Model_Bo_Grupo();
        $modelServico = new Config_Model_Bo_Servico();
        $modelTib = new Config_Model_Bo_Tib();
        $modelIB = new Content_Model_Bo_ItemBiblioteca();
        if (isset($this->servico['id_grupo'])) {
            $grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['metadata']['ws_grupo'])) {
            $grupos = $modelGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'], $this->servico['metadata']['ws_grupo']);
            if (!empty($grupos)) {
                $grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino n o encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $grupo = $this->identity->grupo['id'];
        }
        $idTib = $this->servico['id_tib'];
        $campos = array();
        $ibAnterior = $modelIB->getItemBibliotecaById($idIbMaster)->toArray();
//        x($post);
        foreach ($ibAnterior as $itemAnt) {
            $tib = $modelTib->getById($itemAnt['id_tib']);
            $campos[$itemAnt['id_tib']]['antigo'] = $itemAnt['valor'];
            $campos[$itemAnt['id_tib']]['id'] = $itemAnt['id'];
            $campos[$itemAnt['id_tib']]['tipo'] = current($tib)['tipo'];
        }

        foreach ($post as $key => $ib) {
            $a = explode('_', $key);
            if (isset($a[1])) {
                if ($ib != 'undefined') {
                    if ($campos[$a[0]]['tipo'] == 'image') {
                        $campos[$a[0]]['novo'] = str_replace($filedir->url, "", $ib);
                    } else {
                        $campos[$a[0]]['novo'] = $ib;
                    }
                }
            }
        }
        $files = $this->_localSave();

        if ($files) {
            if (count($files) < 4) {
                foreach ($files as $file) {
                    $campos[$file['id_tib']]['novo'] = $file['caminho'];
                }
            } else {
                $campos[$files['id_tib']]['novo'] = $files['caminho'];
            }
        }
//        x($campos);
        $idPessoa = $this->identity->id != NULL ? $this->identity->id : NULL;
        foreach ($campos as $chave => $item) {
            if ($item['tipo'] != 'file') {
                if (isset($item['novo']) && isset($item['antigo'])) {
                    $modelIB->update($item['id'], $idPessoa, $item['novo']);
                } else if (!isset($item['antigo'])) {
                    $modelIB->persiste(FALSE, $chave, $idPessoa, $idIbMaster, $item['novo']);
                } else {
                    $modelIB->delete($item['id']);
                }
            } else {
                if (isset($item['novo']) && isset($item['antigo'])) {
                    $modelIB->update($item['id'], $idPessoa, $item['novo']);
                } else if (!isset($item['antigo'])) {
//                    if ($item['novo'] != NULL) 
//                        $modelIB->persiste(FALSE, $chave, $idPessoa, $idIbMaster, $item['novo']);
                }
            }
        }

        $servico = $modelServico->getServicoByMetanome($this->servico['metadata']['ws_target']);
        $servicoDestino = current($servico)['id'];

        $response = array(
            'success' => true,
            'msg' => $this->_translate->translate("Salvo com sucesso!"),
            'data' => array('target' => array('servico' => $servicoDestino))
        );
        $this->_helper->json($response);
    }

    public function deleteAction() {
        $modelServico = new Config_Model_Bo_Servico();
        $modelGrupo = new Config_Model_Bo_Grupo();
        if (isset($this->servico['id_grupo'])) {
            $grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['metadata']['ws_grupo'])) {
            $grupos = $modelGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'], $this->servico['metadata']['ws_grupo']);
            if (!empty($grupos)) {
                $grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino n o encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $grupo = $this->identity->grupo['id'];
        }

        $id_item = $this->getRequest()->getParam('id');
        $modelIb = new Content_Model_Bo_ItemBiblioteca();
        $retorno = $modelIb->delRelGrupoItem($grupo, $id_item);

        if (isset($this->servico['metadata']['ws_target'])) {
            $target = $this->servico['metadata']['ws_target'];
            $servico = $modelServico->getServicoByMetanome($target);
            $servicoDestino = current($servico)['id'];
        } else {
            $servicoDestino = $this->servico['id_pai'];
        }


        $array = array();
        if ($retorno) {
            $array['status'] = true;
            $array['mensagem'] = "Deletado com sucesso!";
        } else {
            $array['status'] = false;
            $array['mensagem'] = "Erro ao deletar!";
        }
        $response = array(
            'success' => $array['status'],
            'msg' => $this->_translate->translate($array['mensagem']),
            'data' => array('target' => array('servico' => $servicoDestino))
        );
        $this->_helper->json($response);
        exit;
    }

    public function dragndropAction() {

        $this->view->file = "dragndrop2.html.twig";

        $filhos = $this->identity->servicos[$this->servico['id']]['filhos'];
        $data = array();
        foreach ($filhos as $id => $filho) {
            if ($filho['ws_comportamento'] == 'filter') {
                $data['servico'] = $id;
            }
        }

        if (isset($this->servico['ws_acceptedFiles'])) {
            $data['acceptedfiles'] = $this->servico['ws_acceptedFiles'];
        }

        $this->view->data = array('data' => $data);
    }

    public function savedndAction() {

        set_time_limit(0);

        // $svcBo = new Config_Model_Bo_Servico();

        $ret = $this->_localSave();

        $this->_msg(true, 'Importação realizada com sucesso.');
    }

    public function salvadexionAction() {
        
        set_time_limit(0);

        $ret = $this->_localSave();

        x($ret);

    }

    public function disparaunicoAction() {
        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

        $url = '';

        if (isset($this->servico['id_grupo'])) {
            $grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['metadata']['ws_grupo'])) {
            $grupos = $objGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'], $this->servico['metadata']['ws_grupo']);
            if (!empty($grupos)) {
                $grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino não encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $grupo = $this->identity->grupo['id'];
        }

        if (isset($this->servico['ws_url'])) {
            $url = $this->servico['ws_url'];
        } else if (isset($this->servico['ws_rota'])) {
            $url = $filedir->remoto . $this->servico['ws_rota'];
        } else {
            $this->_msg(false, 'Rota de chamada não identificada.');
            exit;
        }

        $url = $url . '?ib=' . $term = $this->getRequest()->getParam('id') . '&time=' . $this->identity->time['id'] . '&grupo=' . $grupo;
        if (isset($this->servico['ws_classificacao'])) {
            $url = $url . '&cls=' . $this->servico['ws_classificacao'];
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        $res = curl_exec($ch);
        curl_close($ch);

        $this->_msg(true, 'Arquivo enviado para processamento.');

//        $this->_msg(true,$url);
    }

}
