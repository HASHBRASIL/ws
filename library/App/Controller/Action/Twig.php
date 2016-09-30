<?php

abstract class App_Controller_Action_Twig extends App_Controller_Action
{
    protected $_gridSelect;

    protected $_translate;

    protected $_countGridSelect;

    /**
     * @var App_Model_Bo_Abstract
     */
    protected $_bo;
    /**
     * configuração padrão para definir o uso de twig em todas as actions do controller
     */
    public function postDispatch()
    {
        $this->view->params = $this->getAllParams();
        $this->renderScript('twig.phtml');

        return parent::postDispatch();
    }

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        if ($this->_bo) {
            $this->_bo->setServico($this->servico);
        }
    }

    public function init()
    {
        $this->_translate = Zend_Registry::get('Zend_Translate');

//        $this->view->data = null;

        $identity   = Zend_Auth::getInstance()->getIdentity();
        $request    = $this->getRequest();
        $params     = $request->getParams();

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->_helper->layout->disableLayout();
        } else {
            $this->_helper->layout->setLayout('novo_hash');
        }

        $this->view->data = array();

        //$this->_helper->layout->setLayout('novo_hash');

        $this->identity = Zend_Auth::getInstance()->getIdentity();
        $this->servico  = $this->identity->servicosAtual[$this->getParam('servico')];

        parent::init();
    }

    /**
     * função para criação de grid
     *
     */
    public function gridAction()
    {
//      $this->_initGrid();

        $options = $this->getAllParams();
        $paramsPaginator = $options;
        
        if( isset($options['searchFields']) && ($options['searchFields']) ) {
            $this->identity->{$options['servico']} = $options;
        }else if(isset($this->identity->{$options['servico']}['searchFields']) && ($this->identity->{$options['servico']}['searchFields']) ) {
            $options = $this->identity->{$options['servico']};
        }

        if ($this->_gridSelect) {
            $options['select'] = $this->_gridSelect;
        }

        $filtro = null;

        if (!isset($this->header) && (!$this->header)) {
            $options['fields'] = $this->_bo->fields;

            $header = array();
            if (isset($options['fields']) && $options['fields']) {
                foreach ($options['fields'] as $campo => $label) {
                    if ($this->_bo->fieldsFilter) {
                        $filtro = $this->_bo->fieldsFilter[$campo];
                    }
                    $header[] = array('campo' => $campo, 'label' => $label, 'filtro' => $filtro);
                }
            }

            $this->header = $header;

        } else {
            $options['fields'] = array_column($this->header, 'label', 'campo');
        }

        //options['countGridSelect'] = $this->_gridSelect['count'];
        
        if($this->_countGridSelect) {
            $options['countGridSelect'] = $this->_countGridSelect;
        }        
        
        $paginator = $this->_bo->paginator($options);

        $this->view->paginator = $paginator;
        $this->view->data      = array('data' => $paginator, 'header' => $this->header, 'paramsPaginator' => $paramsPaginator);
        $this->view->file      = 'paginator.html.twig';
    }

    public function imprimirAction()
    {
        $this->gridAction();
        $layout = $this->_helper->layout->getLayoutInstance();
        $layout->content = $this->view->render('imprimir.phtml');
        $html =  $layout->render();

        $mpdf = new mPDF();

        $mpdf->WriteHTML($html);

        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

        // caminho padrão temporário
        if (!file_exists($filedir->path . "hash/")) {
            mkdir($filedir->path . "hash/", 0755);
        }

        if (!file_exists($filedir->path . "hash/temp/")) {
            mkdir($filedir->path . "hash/temp/", 0755);
        }

        $arquivo = $filedir->path . "hash/temp/" . $this->identity->id  . "_tabela.pdf";

        $mpdf->Output($arquivo, 'F');


        $this->_helper->json(array('success' =>true, 'message'=>'Arquivo gerado com sucesso!', 'data' => array('target' => array('url' => $filedir->url . $arquivo))));
    }

    public function autocompleteAction()
    {
        // @todo fazer uso do ws_comboordem.

        $valor = (!isset($this->servico['ws_comboform'])) ? null : $this->servico['ws_comboform'];

        $params = $this->getAllParams();
        $page = 0;
        if(isset($params['page'])){
            $page = $params['page'];
        }
        $data = $this->_bo->getAutocomplete($params['search'], '10', $page, 'id', $valor);
        $this->_helper->json($data);
    }

    protected function _localSave() {

        $boIb = new Content_Model_Bo_ItemBiblioteca();
        $boTib = new Config_Model_Bo_Tib();
        $boRlGI = new Content_Model_Bo_RlGrupoItem();
        $objGrupo = new Config_Model_Bo_Grupo();

        $arqs = array();

        if ($this->hasParam('id_grupo') && ($this->getParam('id_grupo'))){
            $grupo = $this->getParam('id_grupo');
        } else if (isset($this->servico['id_grupo'])){
            $grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['ws_grupo'])){
            $grupos = $objGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'],$this->servico['ws_grupo']);

            if(!empty($grupos)){
                $grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino não encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $grupo = $this->identity->grupo['id'];
        }

        $ret = array();

        $arqs = $_FILES;

        foreach ($arqs as $key => $file) {
            if (!empty($file['name'])) {
                $tibArquivo = explode('_', $key);
                $id_ib = UUID::v4();
                $arquivo    =   array();
                $arquivo[$id_ib . '_enclosure' ]    = $file;

                $caminhoDoArquivo   =   $boIb->upload( $arquivo, $this->identity->time['id'], $grupo);

                if (isset($this->servico['ws_arqcampo'])) {
                    $arrCampo = $boTib->getById($this->servico['ws_arqcampo']);
                    if (isset($this->servico['ws_arqnome'])) {
                        $arrNome = $boTib->getById($this->servico['ws_arqnome']);
                    }
                    if (isset($this->servico['ws_arqstatus'])) {
                        $arrStatus = $boTib->getById($this->servico['ws_arqstatus']);
                    }
                    if (isset($this->servico['ws_arqdata'])) {
                        $arrData = $boTib->getById($this->servico['ws_arqdata']);
                    }
                    //$arrCampoMaster = $boTib->getById($arrCampo[0]['id_tib_pai']);

                    $id_master = $boIb->persiste(false,$arrCampo[0]['id_tib_pai'],$this->identity->id,null,null);
                    $id_ib = $boIb->persiste(false,$arrCampo[0]['id'],$this->identity->id,$id_master,null);

                    if($arrNome){
                        $id_nome = $boIb->persiste(false,$arrNome[0]['id'],$this->identity->id,$id_master,$file['name']);
                    }
                    if($arrStatus) {
                        $id_status = $boIb->persiste(false,$arrStatus[0]['id'],$this->identity->id,$id_master,'NOVO');
                    }
                    if($arrData) {
                        $id_data = $boIb->persiste(false,$arrData[0]['id'],$this->identity->id,$id_master,date('d/m/Y H:i:s'));
                    }

                    $boIb->persiste($id_ib,null,null,null,$caminhoDoArquivo);

                    $boRlGI->relacionaItem($grupo, $id_master);
                }

                $ret[] = array('ib' => $id_master, 'caminho' => $caminhoDoArquivo, 'original' => $file['name'], 'id_tib' => $tibArquivo[0], 'ext' =>  substr(strrchr($file['name'], "."),1));
            }
        }

        if(count($ret) > 1) {
            $retorno = $ret;
        } else {
            $retorno = current($ret);
        }

        return $retorno;
    }

    protected function _saveFile($fileContents, $fileName, $idPai = null, $ocr = null) {

        $boIb = new Content_Model_Bo_ItemBiblioteca();
        $boTib = new Config_Model_Bo_Tib();
        $boRlGI = new Content_Model_Bo_RlGrupoItem();
        $boRlVI = new Content_Model_Bo_RlVinculoItem();
        $objGrupo = new Config_Model_Bo_Grupo();

        $dadosOcr = null;
        $arqs = array();
        $extension = substr(strrchr($fileName, "."),1);

        if (isset($this->servico['id_grupo'])){

            $grupo = $this->servico['id_grupo'];
        } elseif (isset($this->servico['ws_grupo'])){
            $grupos = $objGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'],$this->servico['ws_grupo']);

            if(!empty($grupos)){
                $grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino não encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $grupo = $this->identity->grupo['id'];
        }

        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

        $id_ib = UUID::v4();
        $nome   =   $id_ib . '.' . $extension;

        $newFolder  =   $filedir->path . $this->identity->time['id'] . '/';

        $retorno    =   $this->identity->time['id'] . '/';

        if ( !file_exists($newFolder) ){
            mkdir( $newFolder, 0755 );
        }

        $newFolder  =   $newFolder . $grupo . '/';
        $retorno    =   $retorno . $grupo . '/';
        if ( !file_exists($newFolder) ) {
            mkdir($newFolder, 0755);
        }

        file_put_contents($newFolder . $nome, $fileContents);

        // @todo coloca o arquivo no google cloud!

        $filePath = $retorno . $nome;

        if (isset($this->servico['ws_arqcampo'])) {
            $arrCampo = $boTib->getById($this->servico['ws_arqcampo']);
            if (isset($this->servico['ws_arqnome'])) {
                $arrNome = $boTib->getById($this->servico['ws_arqnome']);
            }
            if (isset($this->servico['ws_arqstatus'])) {
                $arrStatus = $boTib->getById($this->servico['ws_arqstatus']);
            }
            if (isset($this->servico['ws_arqdata'])) {
                $arrData = $boTib->getById($this->servico['ws_arqdata']);
            }
            //$arrCampoMaster = $boTib->getById($arrCampo[0]['id_tib_pai']);

            $id_master = $boIb->persiste(false,$arrCampo[0]['id_tib_pai'],$this->identity->id,null,null);

            $id_ib = $boIb->persiste(false,$arrCampo[0]['id'],$this->identity->id,$id_master,null);

            if($arrNome){
                $id_nome = $boIb->persiste(false,$arrNome[0]['id'],$this->identity->id,$id_master, $fileName);
            }

            if($arrStatus) {
                if ($ocr == true) {
                    $status = 'OCR';
                } else {
                    $status = 'NOVO';
                }
                $id_status = $boIb->persiste(false,$arrStatus[0]['id'],$this->identity->id,$id_master, $status);
            }

            if($arrData) {
                $id_data = $boIb->persiste(false,$arrData[0]['id'],$this->identity->id,$id_master, date('d/m/Y H:i:s'));
            }

//            if ($ocr == true) {
//                // @todo faz regra OCR
//                $googleVision = new App_Model_Bo_Vision();
//
//                $retornoOcr = $googleVision->process($fileContents);
//
//                $textoOcr = $retornoOcr['responses'][0]['textAnnotations'][0]['description'];
//
//                $arrOcr = $boTib->getByMetanome('ocr');
//
//                $dadosOcr = $boIb->persiste(false,$arrOcr[0]['id'],$this->identity->id,$id_master,$textoOcr);
//            }

            // @todo seria aqui para gerar imagem peq para visualizar

            $boIb->persiste($id_ib,null,null,null,$filePath);

            $boRlGI->relacionaItem($grupo, $id_master);

            if ($idPai) {
                $boRlVI->relacionaItem($idPai, $id_master);
            }
        }

        $retorno = array('ib' => $id_master, 'caminho' => $filePath, 'original' => $fileName, 'ocr' => $dadosOcr);

        return $retorno;
    }
}