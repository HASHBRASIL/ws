<?php
class Financial_AgrupadorController extends App_Controller_Action_TwigCrud
{
    /**
     * @var Financial_Model_Bo_AgrupadorFinanceiro
     */
    protected $_bo;

    public function init()
    {
//    	$this->_redirectDelete = "financial/financial/grid";
        $this->_bo = new Financial_Model_Bo_AgrupadorFinanceiro();
//    	$this->_aclActionAnonymous = array('get-pairs-per-ty    pe','next-or-previous-id', 'get', 'duplicar-tks-ajax', 'historico');
        $this->_helper->layout()->setLayout('novo_hash');
        parent::init();
        $this->_id = $this->getParam("id");
    }


    public function savedndAction() {

        set_time_limit(0);

        // $svcBo = new Config_Model_Bo_Servico();

        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

        $options = $this->getAllParams();

        $retorno = $this->_localSave();

        $boRlAgrupadorFinanceiroIb = new Financial_Model_Bo_RlAgrupadorFinanceiroIb();

        $boRlAgrupadorFinanceiroIb->adicionarVinculo($retorno, $options);

        //var_dump($retorno);


        if (($retorno['ext'] == 'pdf') || ($retorno['ext'] == 'tif') || ($retorno['ext'] == 'tiff')) {
            $pdf = new Spatie\PdfToImage\Pdf($filedir->path . "/" . $retorno['caminho']);

            $googleVision = new App_Model_Bo_Vision();

            foreach (range(1, $pdf->getNumberOfPages()) as $pageNumber) {
                $fileContents = $pdf->setPage($pageNumber)->getImageData("xpto.jpg");
    //            $retornoOcr = $googleVision->process($fileContents);

                $this->_saveFile($fileContents, "jpg", $retorno['ib']); // @todo como salvar o retorno OCR-- //, $retornoOcr);
            }
        }

        $this->_msg(true, 'Importação realizada com sucesso.');
    }

    public function gridUploadAction()
    {
        $options = $this->getAllParams();

        $boItemBiblioteca = new Content_Model_Bo_ItemBiblioteca();
        $modelTPIB = new Content_Model_Bo_TpItemBiblioteca();

        $objGrupo = new Config_Model_Bo_Grupo();

        $grupos = $objGrupo->getGruposByIDPaiByMetanome($this->identity->time['id'], $this->servico['metadata']['ws_grupo']);
        $this->_grupo = current($grupos)['id'];

        $this->header = $modelTPIB->getBasicConfigHeader($this->servico);

        $select = $boItemBiblioteca->getItemBibliotecaGrid($this->servico['id_tib'], $this->_grupo, $options);
        $this->_gridSelect = $select;

        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');
        $this->view->filedir = $filedir;

        parent::gridAction();
    }

    public function _initForm(){

//        var_dump($this->getAllParams());

        $moedaBo	  		 = new Financial_Model_Bo_Moeda();
        $pessoaBO 		 = new Legacy_Model_Bo_Pessoa();

        $processoBo     	 = new Processo_Model_Bo_Processo();
        $centroCustoBo		 = new Financial_Model_Bo_CentroCusto();
        $grupoOperacaoBo	 = new Empresa_Model_Bo_GrupoOperacoes();
        $financialBo 		 = new Financial_Model_Bo_Financial();
        $statusBo		 	 = new Financial_Model_Bo_Status();
        $contaBo		 	 = new Financial_Model_Bo_Contas();
        $documentoInternoBo	 = new Financial_Model_Bo_DocumentoInterno();
        $documentoExternoBo	 = new Financial_Model_Bo_DocumentoExterno();
        $planoContaBo		 	 = new Financial_Model_Bo_PlanoContas();

//        if ($this->_id){
//
//            $agrupadorfinancialObj = $this->_bo->get($this->_id);
//
//            $planoContaSaved = array();
//            $planoContaSaved['plc_id'] = "";
//            $planoContaSaved['plc_descricao'] = "";
//
//            if(isset($agrupadorfinancialObj->getPlanoContas()->plc_id)){
//
//                $planoContaSaved['plc_id'] = $agrupadorfinancialObj->getPlanoContas()->plc_id;
//                $planoContaSaved['plc_descricao'] = $agrupadorfinancialObj->getPlanoContas()->plc_cod_contabil." ".$agrupadorfinancialObj->getPlanoContas()->plc_descricao;
//
//            }
//
//            $this->view->planoContaSaved = $planoContaSaved;
//
//            $this->view->financialPerAgrupador = $financialBo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_agrupador_financeiro = ?"=> $this->getParam('id_agrupador_financeiro') ));
//
//
//            $valorPago = $financialBo->getFinancialListPago($this->getParam('id_agrupador_financeiro'));
//
//            $this->view->transacoesPagas = $valorPago;
//
//            $valorTotal = 0;
//            foreach ($valorPago as $key => $pago) {
//
//                $valorTotal = $valorTotal + $pago->fin_valor;
//
//            }
//
//            $this->view->valorTotalPago = $valorTotal;
//
//            if ($this->getParam('fin_id')){
//
//                $this->view->finIdForDialog = $this->getParam('fin_id');
//
//            }
//
//        }

//        $this->view->comboEmpresasGrupo 	= $pessoaBO->getGrupoPairs();
//        $this->view->comboGrupoOperacao		= $grupoOperacaoBo->getPairs();
        $this->view->comboCentroCusto		= $centroCustoBo->getPairs();
        $this->view->comboMoedas			= $moedaBo->getPairs(false);
//        $this->view->comboProcesso    		= $processoBo->getPairs(false);
//        $this->view->comboStatus        	= $statusBo->getPairs(false);
        $this->view->comboContas			= $contaBo->getPairs();
//        $this->view->comboDocumentoInterno	= $documentoInternoBo->getPairs();
//        $this->view->comboDocumentoExterno	= $documentoExternoBo->getPairs();
        $this->view->comboPlanoContas	= $planoContaBo->getPairs();

//        var_dump($this->servico['metadata']);
//        exit('aqui?');

        if (isset($this->servico['filhos'])) {

            foreach ($this->servico['filhos'] as $filho) {
                if ($filho['metadata']['ws_comportamento'] == 'filter') {
                    // @autocomplete
                    $autocomplete = $filho;
                }
            }
        }

        if (isset($this->servico['metadata']['erp_tipomov']) && $this->servico['metadata']['erp_tipomov']) {
            $this->getRequest()->setParam('tmv_id', $this->servico['metadata']['erp_tipomov']);
        }

        $this->view->autoCompletePessoa = $autocomplete;

    }

    public function _initEditForm($object)
    {
        $pessoaBO 		 = new Legacy_Model_Bo_Pessoa();
        if ($object->id_pessoa_cliente) {
            $rowPessoa = $pessoaBO->getById($object->id_pessoa_cliente);
            $this->view->comboPessoaCliente = array($rowPessoa['id'] => $rowPessoa['nome']);
        } else {
            $this->view->comboPessoaCliente = array();
        }
    }

    public function conciliacaoAction()
    {
        $transacaoContaBo     	 = new Financial_Model_Bo_TransacaoConta();
        $this->view->comboPendetes	= $transacaoContaBo->getPendentes();

        parent::formAction();
    }

    public function conciliacaoContaAddAction()
    {
        $transacaoContaBo     	 = new Financial_Model_Bo_TransacaoConta();
        $this->view->comboTipo	= $transacaoContaBo->fieldsFilter['tp_transacao_conta'];
        parent::formAction();
    }


    public function formExtraAction()
    {

        $identity = Zend_Auth::getInstance()->getIdentity();
        $data = array();

        foreach ($identity->timesColigados as $time) {
            $data[$time['id']] = $time['nome'];
        }

        $this->view->comboGrupo = $data;

        parent::formAction();
    }


    public function importReceitaAction()
    {
        $this->_helper->layout->disableLayout();
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

        if ($this->getParam('tmv_id')) {

        }

        $this->view->data = array('data' => $data);
    }


    public function importDespesaAction()
    {
        $this->_helper->layout->disableLayout();
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


    public function uploadReceitaAction()
    {
        // @todo flag ta invertida?!
        $this->proccessUpload(Financial_Model_Dao_AgrupadorFinanceiro::CREDITO);
    }
    public function uploadDespesaAction()
    {
        $this->proccessUpload(Financial_Model_Dao_AgrupadorFinanceiro::DEBITO);
//        $idMaster = $this->import();
//        // @todo salvar transacao
//        $this->_bo->processUpload($idMaster, 2);
//
//        $target = (isset($this->servico['ws_target']) && $this->servico['ws_target'])
//            ? $this->servico['ws_target']
//            : $this->servico['id_pai'];
//
//        $response = array(
//            'success' => true,
//            'msg' => $this->_translate->translate("Dados salvos com sucesso"),
//            'data' => array('target' => array('servico' => $target))
//        );
//        $this->_helper->json($response);

    }

    public function proccessUpload($tipoMovimento)
    {
        $upload = new Zend_File_Transfer_Adapter_Http();


        if (! $upload->isValid()){
            $resposta = array('error' => true, 'msg' => 'Ocorreu uma falha no envio do documento.', 'messages' => current($upload->getMessages()));
        } else {

            $info         = $upload->getFileInfo();
            $fileContents = file_get_contents($info['file']['tmp_name']);
            $dtTipo       = $info['file']['type'];

//            var_dump($info['file']['tmp_name']);
//            var_dump($info['file']['name']);
//            var_dump($dtTipo);
//
//            var_dump(mime_content_type($info['file']['tmp_name']));

            $extensao = substr(strrchr($info['file']['name'], "."),1);


//            if (($retorno['ext'] == 'pdf') || ($retorno['ext'] == 'tif') || ($retorno['ext'] == 'tiff')) {
////                $pdf = new Spatie\PdfToImage\Pdf($filedir->path . "/" . $retorno['caminho']);
//
//                $googleVision = new App_Model_Bo_Vision();
//
//                foreach (range(1, $pdf->getNumberOfPages()) as $pageNumber) {
//                    $fileContents = $pdf->setPage($pageNumber)->getImageData("xpto.jpg");
//                    //            $retornoOcr = $googleVision->process($fileContents);
//
//                    $this->_saveFile($fileContents, "jpg", $retorno['ib']); // @todo como salvar o retorno OCR-- //, $retornoOcr);
//                }
//            }

            $boRlAgrupadorFinanceiroIb = new Financial_Model_Bo_RlAgrupadorFinanceiroIb();

            $data = array();

//            var_dump($extensao);
//            exit;


            switch (strtolower($extensao)) {
                case 'ofx':
                    $idMaster = $this->import();
                    // @todo validar unicidade (não duplicar)
                    $this->_bo->processUpload($idMaster,$tipoMovimento);
                    break;
                case 'pdf':
                    //Fall through to next case;
                case 'tif':
                    //Fall through to next case;
                case 'tiff':

                    $retornoPai = $this->_saveFile($fileContents, "jpg");

                    $fileTransformation = new Spatie\PdfToImage\Pdf($info['file']['tmp_name']);

                    foreach (range(1, $fileTransformation->getNumberOfPages()) as $pageNumber) {
                        $fileContents = $fileTransformation->setPage($pageNumber)->getImageData("xpto.jpg");
                        $retorno = $this->_saveFile($fileContents, "jpg", $retornoPai['ib'], true);

                        $boRlAgrupadorFinanceiroIb->adicionarVinculo($retorno, null);

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

                    $retorno = $this->_saveFile($fileContents, $extensao, null, true);

                    $boRlAgrupadorFinanceiroIb->adicionarVinculo($retorno, null);

                    $data[] = $retorno;

                    break;
                default:
                    throw new Exception("Extensão do arquivo não suportado.");
                    break;
            }
        }

        $response = array(
            'success' => true,
            'msg' => $this->_translate->translate("Dados salvos com sucesso"),
            'new' => $data
        );

        $this->_helper->json($response);
    }

    public function processImageAction()
    {

        $googleVision = new App_Model_Bo_Vision();

    }


    public function import()
    {

        $identity = Zend_Auth::getInstance()->getIdentity();
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');

        $request = $this->getRequest();
        $params  = $request->getParams();

        $idServico = $params['servico'];

        $HASH_SERVICO = $identity->servicosAtual[$idServico];

//        $SERVICO = $HASH_SERVICO;

        $_SESSION['USUARIO']['ID']   = $identity->id;
        $_SESSION['USUARIO']['NOME'] = $identity->pessoa->nome;
        $_SESSION['USUARIO']['FOTO'] = '';
        $_SESSION['TIME']['ID']      = $identity->grupo['id'];


//      $this->_helper->layout->disableLayout();
//      $viewRenderer->setNoRender(true);
//        require_once "../home2.php";

        $this->_helper->layout->setLayout('novo_hash');


        chdir('..');
        require_once "includes/error.php";

        require_once "includes/functions.php";

        spl_autoload_register('hash_autoloader');

        require_once "includes/databaseconnect.php";
        require_once "includes/connect.php";
        require_once "includes/twig.php";

        include 'includes/filterImportaNFe.php';

        // voltar ao estado anterior
        chdir('public');

        // @todo id master da tib que foi salva
//        $id_master

        return $id_master;
    }


//    public function _initGrid(){
//        //$statusBo		 	 = new Financial_Model_Bo_Status();
//        $pessoaBO 		 = new Legacy_Model_Bo_Pessoa();
//        $contaBo		 	 = new Financial_Model_Bo_Contas();
//        $centroCustoBo		 = new Financial_Model_Bo_CentroCusto();
//        $grupoOperacaoBo	 = new Empresa_Model_Bo_GrupoOperacoes();
//        $planoContas 		 = new Financial_Model_Bo_PlanoContas();
//        $contaBo		 	 = new Financial_Model_Bo_Contas();
//
//        //$this->view->comboStatus        	= $statusBo->getPairs(false);
//        $this->view->comboEmpresasGrupo 	= $pessoaBO->getGrupoPairs();
//        $this->view->comboContas			= $contaBo->getPairs(false);
//        $this->view->comboCentroCusto		= $centroCustoBo->getPairs();
//        $this->view->comboGrupoOperacao		= $grupoOperacaoBo->getPairs();
//        $this->view->comboPlanoContas		= $planoContas->getPairsPerType();
//        $this->view->comboContas			= $contaBo->getPairs(false);
//    }

    public function conciliacaoContaAction () {
        $this->view->comboTransacaoFinanceira = $this->_bo->getTransacaoAberta();
        parent::formAction();
    }

    public function splitAction()
    {

        $identity = Zend_Auth::getInstance()->getIdentity();
        $centroCustoBo = new Financial_Model_Bo_CentroCusto();
        $planoContaBo = new Financial_Model_Bo_PlanoContas();

        $data = array();

        foreach ($identity->timesColigados as $time) {
            $data[$time['id']] = $time['nome'];
        }

        $request = $this->getAllParams();

        $this->view->comboGrupo = $data;
        $this->view->comboCentroCusto = $centroCustoBo->getPairs();
        $this->view->comboPlanoContas = $planoContaBo->getPairs();

        $this->view->id = $this->_id;

        // verificar se vem via post
        if ($this->getRequest()->isPost()) {
            try {
                $this->_bo->validateSplit($request);

                $this->_bo->saveSplit($request);

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

            } catch (App_Validate_Exception $e) {

                //verifica se e pelo ajax
                if ($this->getRequest()->isXmlHttpRequest()) {
                    $response = array('success' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTraceAsString());
                    $this->_helper->json($response);
                } else {
                }
            } catch (Exception $e) {
                //verifica se e pelo ajax
                if ($this->getRequest()->isXmlHttpRequest()) {
                    $response = array(
                        'success' => false,
                        'msg' => 'Não foi possível realizar a operação solicitada. ' . $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    );
                    $this->_helper->json($response);
                }

                $this->_addMessageError($e->getMessage());
            }
        }

    }


}