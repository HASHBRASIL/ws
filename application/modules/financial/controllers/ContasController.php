<?php

class Financial_ContasController extends App_Controller_Action_TwigCrud
{
    /**
     * @var Financial_Model_Bo_Contas
     */
    protected $_bo;

    public function init()
    {
        parent::init();
//        $this->_aclActionAnonymous = array("autocomplete", 'get');
        $this->_helper->layout()->setLayout('novo_hash');

        // @todo ajustar isso.
        $this->_redirectDelete = "home.php?servico=" . $this->servico['id_pai'];
        $this->_bo = new Financial_Model_Bo_Contas();
        $this->_id = $this->getParam("id");
    }

    public function _initForm()
    {
        $bancoObj = new Financial_Model_Bo_Bancos();
        $tipoContaBancoObj = new Financial_Model_Bo_TipoContaBanco();

        $this->view->bancoCombo = $bancoObj->getPairs();
        $this->view->tipoContaBancoCombo = $tipoContaBancoObj->getPairs();


        $identity = Zend_Auth::getInstance()->getIdentity();
        $data = array();

        foreach ($identity->timesColigados as $time) {
            $data[$time['id']] = $time['nome'];
        }

        $this->view->comboGrupo = $data;


    }

    public function creditoAction()
    {
        exit('foi?>');
    }

    public function importacaoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->view->file = "dragndrop2.html.twig";

//        foreach ($this->servico['filhos'] as $filho) {
//            if ($filho['metadata']['ws_comportamento'] == 'upload') {
//                $this->view->servicoUpload = $filho['id'];
//            }
//        }

        $filhos = $this->identity->servicos[$this->servico['id']]['filhos'];
        $data = array();
        foreach ($filhos as $id => $filho) {
            if ($filho['ws_comportamento'] == 'filter') {
                $data['servico'] = $id;
            }
        }

        $data['acceptedfiles'] = '/\.(ofx)$/i';

        $this->view->data = array('data' => $data);


    }

//    public function gridAction(){
//        $header   = array();
//        $header[] = array('campo' => 'con_id', 'label' => 'Código');
//        $header[] = array('campo' => 'con_codnome', 'label' => 'Codinome');
//        $header[] = array('campo' => 'con_agencia', 'label' => 'Agência');
//        $header[] = array('campo' => 'con_age_digito', 'label' => 'Dígito da Agência');
//        $header[] = array('campo' => 'con_numero', 'label' => 'Número da Conta');
//        $header[] = array('campo' => 'con_digito', 'label' => 'Dígito da Conta');
//        $header[] = array('campo' => 'bco_nome', 'label' => 'Banco');
//        $header[] = array('campo' => 'tcb_descricao', 'label' => 'Tipo Conta');
//        $this->header = $header;
//
//        $this->_gridSelect = $queryninja;
//
//        parent::gridAction();
//
//        $this->view->file = "index.html.twig";
//
//    }

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


        $this->_helper->json(array('success' =>true, 'message'=>'Arquivo gerado com sucesso!', 'data' => array('target' => array('url' => $arquivo))));
    }

    public function extratoFormAction()
    {

        $this->_helper->layout()->setLayout('novo_hash');
        $this->view->contaPairs = $this->_bo->getPairs();
    }

    public function extratoViewAction()
    {

        $this->_helper->layout()->setLayout('novo_hash');
        if ($this->getRequest()->isPost()) {
            $financialBo = new Financial_Model_Bo_Financial();

            $request = $this->getAllParams();
            $financialList = $financialBo->getExtrato($request);
            $this->view->conta = $this->_bo->get($this->getParam('con_id'));
            $this->view->financialList = $financialList;
            $this->view->request = $request;
        }

    }

    public function extratoPdfAction()
    {

        if ($this->getRequest()->isPost()) {

            $financialBo = new Financial_Model_Bo_Financial();
            $pdf = new App_Util_Pdf(null, null, null, null, null, $orientacao = "L");

            $request = $this->getAllParams();
            $financialList = $financialBo->getExtrato($request);
            $this->view->conta = $this->_bo->get($this->getParam('con_id'));
            $this->view->financialList = $financialList;
            $this->view->request = $request;

            $nomeContas = array();

            if (isset($request['contaList'])) {

                foreach ($request['contaList'] as $key => $conta) {

                    $contaObj = $this->_bo->get($conta);
                    $nomeContas[] = $contaObj->con_codnome;
                }
            }

            $this->view->nomeContas = $nomeContas;

            $html = $this->view->render('financial/extrato-pdf.phtml');

            $pdf->modificarFonte('helvetica', 10);
            $pdf->adicionarHtml($html);
            $pdf->abrirArquivo();
            exit();

        }
    }

    public function autocompleteAction()
    {
        $term = $this->getRequest()->getParam('term');
        $list = $this->_bo->getAutocomplete($term, false);
        $this->_helper->json($list);
    }

    public function getAction()
    {
        $idConta = $this->getParam('id_conta');
        $conta = $this->_bo->get($idConta);
        $this->_helper->json($conta);
    }


    public function uploadAction()
    {
        $upload = new Zend_File_Transfer_Adapter_Http();

        if (! $upload->isValid()){
            exit('erro!');
//            $resposta = array('error' => true, 'msg' => 'Ocorreu uma falha no envio do documento.', 'messages' => current($upload->getMessages()));
        } else {
            $info         = $upload->getFileInfo();
            $fileContent = file_get_contents($info['file']['tmp_name']);

            try {
                $contador = $this->_bo->uploadContas($upload, $fileContent, $info);

                $target = (isset($this->servico['ws_target']) && $this->servico['ws_target'])
                    ? $this->servico['ws_target']
                    : $this->servico['id_pai'];

                if ($this->getRequest()->isXmlHttpRequest()) {
                    $response = array(
                        'success' => true,
                        'msg' => $this->_translate->translate("Dados salvos com sucesso"),
                        'data' => array('target' => array('servico' => $target))
                    );
                    $this->_helper->json($response);
                }

                if ($target) {
                    $this->_addMessageSuccess("Dados salvos com sucesso", "home.php?servico=" . $target);
                } else {
                    $this->_addMessageSuccess("Dados salvos com sucesso");
                }
            } catch (App_Validate_Exception $e) {
                throw $e;
//                //verifica se e pelo ajax
//                if ($this->getRequest()->isXmlHttpRequest()) {
//                    $response = array('success' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTraceAsString());
//                    $this->_helper->json($response);
//                } else {
//                }
            } catch (Exception $e) {
                throw $e;
//                //verifica se e pelo ajax
//                if ($this->getRequest()->isXmlHttpRequest()) {
//                    $response = array('success' => false, 'msg' => 'Não foi possível realizar a operação solicitada. ' . $e->getMessage(), 'trace' => $e->getTraceAsString());
//                    $this->_helper->json($response);
//                }
//
//                $this->_addMessageError($e->getMessage());
            }

        }
//        $this->_addMessageSuccess("Dados salvos com sucesso");
//        exit();

    }


}
