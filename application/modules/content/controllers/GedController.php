<?php

class Content_GedController extends App_Controller_Action_Twig {

    /**
     * @var Content_Model_Bo_ItemBiblioteca
     */
    protected $_bo;

    public function gridGrupoAction()
    {
        // pegar query para listar documentos

        $header   = array();
//        $header[] = array('campo' => 'id', 'label' => 'Código');
        $header[] = array('campo' => 'arquivo', 'label' => 'Documento', 'tipo' => 'image');
        $header[] = array('campo' => 'titulo', 'label' => 'Título');
        $header[] = array('campo' => 'dt_publicacao', 'label' => 'Data', 'tipo' => 'data');
//        $header[] = array('campo' => 'con_numero', 'label' => 'Número da Conta');
//        $header[] = array('campo' => 'con_digito', 'label' => 'Dígito da Conta');
//        $header[] = array('campo' => 'bco_nome', 'label' => 'Banco');
//        $header[] = array('campo' => 'tcb_descricao', 'label' => 'Tipo Conta');
        $this->header = $header;

        $idGrupo = $this->getParam('id_grupo') ? $this->getParam('id_grupo') : $this->identity->grupo['id'];

        $this->_gridSelect = $this->_bo->getFolderByGrupoByTib($idGrupo, $this->identity->servicosAtual[$this->getParam('servico')], $this->getParam('id'));

        parent::gridAction();

        $this->view->file = 'grid-grupo.html.twig';

        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

        $this->view->filedir = $filedir;


        $this->identity  = Zend_Auth::getInstance()->getIdentity();

        $id = $this->getParam('id');

        if (!$id) {
            $id =  $this->identity->time['id'];
        }

        $filhos = $this->identity->servicos[$this->servico['id']]['filhos'];
        $data = array();
        foreach ($filhos as $idFilho => $filho) {
            if ($filho['ws_comportamento'] == 'filter') {
                $data['servico'] = $idFilho;
            }
        }

        $modelGrupo = new Config_Model_Bo_Grupo();
        $grupos = $modelGrupo->listGruposFilho($id);

        $this->view->data['grupos'] = $grupos;

        $this->view->data['idTimeEscolhido'] = $idGrupo;

//        $this->view->data = array('filedir' => $filedir->url);
//        echo $this->_gridSelect->__toString();

    }

    public function dragndropAction()
    {
        $this->view->file = "ged-upload.html.twig";

        $filhos = $this->identity->servicos[$this->servico['id']]['filhos'];
        $data = array();
        foreach ($filhos as $id => $filho) {
            if ($filho['ws_comportamento'] == 'filter') {
                $data['servico'] = $id;
            }
        }

        $this->view->data = array('data' => $data, 'posted' => $this->getAllParams());

    }

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

    public function uploadAction()
    {
        // srvico d0bbae9c-58f6-4214-bd7c-c19758f520c2
        $this->servico = array_merge($this->servico, $this->getAllParams());

        $upload = new Zend_File_Transfer_Adapter_Http();

        $ocr = $this->getParam('ocr') != 0 ? $this->getParam('ocr') : false;


        if (! $upload->isValid()){
            $resposta = array('error' => true, 'msg' => 'Ocorreu uma falha no envio do documento.', 'messages' => current($upload->getMessages()));
        } else {

            $info         = $upload->getFileInfo();
            $fileContents = file_get_contents(realpath($info['file']['tmp_name']));
            $dtTipo       = $info['file']['type'];


            $fileName = $info['file']['name'];

            $extensao = substr(strrchr($info['file']['name'], "."),1);

            $data = array();

            switch (strtolower($extensao)) {
                case 'ofx':
                    $response = array(
                        'success' => false,
                        'msg' => $this->_translate->translate("Formato nao compativel")
                    );
//                    $idMaster = $this->import();
//                    // @todo validar unicidade (não duplicar)
//                    $this->_bo->processUpload($idMaster, $tipoMovimento);
                    break;
                case 'pdf':
                    //Fall through to next case;
                case 'tif':
                    //Fall through to next case;
                case 'tiff':

                    $retornoPai = $this->_saveFile($fileContents, $fileName);

                    $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

                    $fileone = realpath($filedir->path . $retornoPai['caminho']);


                    $fileTransformation = new Spatie\PdfToImage\Pdf($fileone);

                    foreach (range(1, $fileTransformation->getNumberOfPages()) as $pageNumber) {
                        $fileContents = $fileTransformation->setPage($pageNumber)->getImageData("xpto.jpg");
                        $retorno = $this->_saveFile($fileContents, $pageNumber . ".jpg", $retornoPai['ib'], $ocr);

//                        $boRlAgrupadorFinanceiroIb->adicionarVinculo($retorno, null);

                        $data[] = $retorno;
                    }

                    break;
                // break omitido intensionalmente
                case 'png':
                    //Fall through to next case;
                case 'gif':
                    //Fall through to next case;
                case 'jpg':
                    //Fall through to next case;
                case 'jpe':
                    //Fall through to next case;
                case 'jpeg':

                    $retorno = $this->_saveFile($fileContents, $fileName, null, $ocr);

//                    $boRlAgrupadorFinanceiroIb->adicionarVinculo($retorno, null);

                    $data[] = $retorno;

                    break;
                default:
                    throw new Exception("Extensão do arquivo não suportado.");
                    break;
            }
        }

        $response = array(
            'success' => true,
            'msg' => $this->_translate->translate("Dados salvos com sucesso")
//            'new' => $data
        );

        $this->_helper->json($response);

    }

    public function viewAction()
    {

        $id = $this->getParam('id');

        $data = $this->_bo->getValoresFilhosNomeados($id);

        $this->view->data['ib'] = $data;
    }

    public function indexAction()
    {
        // monta tela inicial
        // lista documentos
        // joga pra view

        $this->identity  = Zend_Auth::getInstance()->getIdentity();

        $id = $this->getParam('id');

        if (!$id) {
            $id =  $this->identity->time['id'];
        }

        $filhos = $this->identity->servicos[$this->servico['id']]['filhos'];
        $data = array();
        foreach ($filhos as $idFilho => $filho) {
            if ($filho['ws_comportamento'] == 'filter') {
                $data['servico'] = $idFilho;
            }
        }

        $modelGrupo = new Config_Model_Bo_Grupo();
        $grupos = $modelGrupo->listGruposFilho($id);

        $this->view->data = array(
            'grupos' => $grupos,
            'data'   => $data
        );

    }

    public function listAction() {

        // b47c5f57-f704-4e52-bb62-3508f272110e
        // df112d61-c47f-4fa5-a84e-d5fda86c8d20
        // a93df36f-dd76-4baa-9655-a0297b5d33eb TPBDIMG


        $rowset = $this->_bo->getFolderByGrupoByTib($this->getParam('grupo'), $this->identity->servicosAtual[$this->getParam('servico')], $this->getParam('id'));


        $novaArvore = array();
        foreach ($rowset as $grupo)
        {
            $parent = $grupo['id_ib_vinculado'] == $id ? '#' : $grupo['id_ib_vinculado'];
            $nome = empty($grupo['nome']) ? 'S/N' : $grupo['nome'];

            $novaArvore[] = (object) array(
                'id'     => $grupo['id'],
                'parent' => $parent,
                'text'   => $nome,
                'children' => false, //$this->_bo->temfilhos($id),
                'type'   => $grupo['id_ib_vinculado'],
            );
        }


        $this->_helper->json($novaArvore);

    }

    function gridAction() {

        $modelTPIB = new Content_Model_Bo_TpItemBiblioteca();

        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

//        $this->header = $modelTPIB->getBasicConfigHeader($this->servico);

        $select = $this->_bo->getPaginatorGed($this->servico['id_tib'], $this->_grupo);
        $this->_gridSelect = $select;
        $this->view->filedir = $filedir;

        parent::gridAction();
    }

    public function createAction() {

        $templateItemBiblioteca = new Config_Model_Bo_Tib();

        $itemBiblioteca = $this->_bo;
        $objGrupo = new Config_Model_Bo_Grupo();
        $pathRastro = new Rastro();
        $rastro = $pathRastro->getPath($this->servico['id']);
        //declarando variaveis
        $grupo = null;
        $servico = $this->servico['id_tib']; //tib_pai do cara
        $perfil = 'Conteúdo';

        if (isset($this->servico['id_grupo'])) {
            $grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['metadata']['ws_grupo'])) {
            $grupos = $objGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'], $this->servico['metadata']['ws_grupo']);
            if (!empty($grupos)) {
                $grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino n o encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $grupo = $this->identity->grupo['id'];
        }

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

    public function insertAction() {
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

    public function retrieveAction() {


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

    public function updateAction() {
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

//    public function dragndropAction() {
//
//        $this->view->file = "dragndrop2.html.twig";
//        $filhos = $this->identity->servicos[$this->servico['id']]['filhos'];
//        $data = array();
//        foreach ($filhos as $id => $filho) {
//            if ($filho['ws_comportamento'] == 'filter') {
//                $data['servico'] = $id;
//            }
//        }
//
//        if (isset($this->servico['ws_acceptedFiles'])) {
//            $data['acceptedfiles'] = $this->servico['ws_acceptedFiles'];
//        }
//
//        $this->view->data = array('data' => $data);
//    }

    public function savedndAction() {

        set_time_limit(0);

        // $svcBo = new Config_Model_Bo_Servico();

        $ret = $this->_localSave();

        $this->_msg(true, 'Importação realizada com sucesso.');
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

//
//('3633182d-368d-47fd-a9c7-172c28f4f3b2',
//'99696bec-ca4a-484c-a68e-78dbc19c1ce1',
//'b89a72f6-3d7d-11e6-a572-030e68b2eb95',
//'e2204ca0-3dfe-11e6-847b-0ff2a4830130',
//'7ca2fa20-4615-11e6-8e31-63e2293552ce')
//
//
//
//b474e4de-3753-11e6-8872-dbdfb03b22ad	TbServicoMetadata	ws_arqcampo	3633182d-368d-47fd-a9c7-172c28f4f3b2	          d6775a9e-373f-11e6-a992-c3e1182a2995	2016-06-20 22:58:53.07068
//22a173a6-3d7f-11e6-94af-f3ff55942036	TbServicoMetadata	ws_arqnome	99696bec-ca4a-484c-a68e-78dbc19c1ce1	              d6775a9e-373f-11e6-a992-c3e1182a2995	2016-06-28 19:24:53.250567
//2c5374ee-3d7f-11e6-a277-2f68676369c9	TbServicoMetadata	ws_arqstatus	b89a72f6-3d7d-11e6-a572-030e68b2eb95	        d6775a9e-373f-11e6-a992-c3e1182a2995	2016-06-28 19:25:09.516455
//3e3669ec-3e32-11e6-8ddf-572f5398353d	TbServicoMetadata	ws_arqdata	e2204ca0-3dfe-11e6-847b-0ff2a4830130	            d6775a9e-373f-11e6-a992-c3e1182a2995	2016-06-29 16:46:59.439847
//6f7d5d7e-4618-11e6-9e45-83cbc2d92f19	TbServicoMetadata	ws_grupo	ARQCONTATOS	7ca2fa20-4615-11e6-8e31-63e2293552ce	2016-07-09 18:02:24.500354






