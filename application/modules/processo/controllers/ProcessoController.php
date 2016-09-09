<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  24/06/2013
 */
class Processo_ProcessoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_Processo
     */
    protected $_bo;
    protected $_aclActionAnonymous = array("quick-search-ajax", 'get', 'autocomplete', 'autocomplete-codigo');
    protected $_isCompany = true;


    public function init()
    {
        /**
         * Código temporário responsável por setar o layout de empresa
         * enquanto não for migrado para o novo layout
         */
//    	$identity = Zend_Auth::getInstance()->getIdentity();
//
//    	if($identity->isEmpresa){
//    		$this->_helper->layout->setLayout('empresa');
//    	}

        $this->_bo = new Processo_Model_Bo_Processo();
        parent::init();
        $this->_helper->layout->setLayout('novo_hash');
    }

    public function _initForm(){

        $this->_id = $this->getParam("pro_id");

        $statusBo		 	    = new Financial_Model_Bo_Status();
        $contaBo			 	= new Financial_Model_Bo_Contas();
        $tipoMovimentoBo	    = new Financial_Model_Bo_TipoMovimento();
        $moedaBo	  		    = new Financial_Model_Bo_Moeda();
        $centroCustoBo		    = new Financial_Model_Bo_CentroCusto();

        $tpMaterialBo           = new Processo_Model_Bo_TipoMaterial();
        $agrupadorFinanceiroBo	= new Financial_Model_Bo_AgrupadorFinanceiro();
//        $empresasBO				= new Empresa_Model_Bo_Empresa();
        $pessoaBO 		 = new Legacy_Model_Bo_Pessoa();
        $grupoOperacaoBo	    = new Empresa_Model_Bo_GrupoOperacoes();
        $statusProcessoBo       = new Processo_Model_Bo_Status();

        $this->view->comboEmpresasGrupo 	= array(null => '----Selecione----')+$pessoaBO->getGrupoPairs();
        $this->view->comboGrupoOperacao		= array(null => '----Selecione----')+$grupoOperacaoBo->getPairs();
        $this->view->comboCentroCusto		= array(null => '----Selecione----')+$centroCustoBo->getPairs();
        $this->view->comboMoedas			= array(null => '----Selecione----')+$moedaBo->getPairs(false);
        $this->view->comboTipoMovimento		= array(null => '----Selecione----')+$tipoMovimentoBo->getPairs();
        $this->view->comboStatusProcesso    = array(null => '----Selecione----')+$statusProcessoBo->getPairs(true, null, null,'sta_numero asc');

        //obj do dialog de material
        $marcaBo              = new Material_Model_Bo_Marca();
        $tpUnidadeBo          = new Sis_Model_Bo_TipoUnidade();

        if (!empty($this->_id)){

            $this->view->agrupamentoFinanceiro	= $agrupadorFinanceiroBo->getPairsByProcesso(true, null, null, null, null,$this->_id);

        }

        $this->view->comboContas			= $contaBo->getPairs(false);
        $this->view->comboStatus        	= $statusBo->getPairs(false);

        //mandando para a view os select do dialog material
        $this->view->marcaCombo             = array(null => '---- Selecione ----')+$marcaBo->getPairs();
        $this->view->tpUnidadeCombo         = array(null => '---- Selecione ----')+$tpUnidadeBo->getPairs(false);
        $this->view->tpMaterialCombo        = $tpMaterialBo->getPairs(false, null, null, 'nome DESC');

        /**
         * Se logar como empresa ele vai ver a mesma view com as mesmas regras so que
         * que com o texto diferente e algo já preenchido
         */
        $this->view->isCompany = isset(Zend_Auth::getInstance()->getIdentity()->isEmpresa) ? Zend_Auth::getInstance()->getIdentity()->isEmpresa : false ;
    }

    public function gridAction()
    {
        $allParams = $this->getRequest()->getParams();
        if(isset($allParams['searchString'])){
            $allParams['searchString'] = str_replace(array('.', '-', '/'), '', $allParams['searchString']);
        }

        $paginator = $this->_bo->paginator($allParams);

//        var_dump($paginator);exit;

        $this->view->paginator = $paginator;
        $this->view->workspaceSession     = new Zend_Session_Namespace('workspace');
    }

    public function quickSearchAjaxAction(){

        $id = $this->_bo->find(array("pro_codigo = ?"=>$this->getParam('id')))->current();
        if (isset($id)){
            $this->_helper->json(array("success" => "true", "processo" => $id->pro_id));
        }else{
            $this->_helper->json(array("success" => "false"));
        }
    }

    public function gridPedidoAction()
    {
        $criteria = array('empresas_id = ?' => Zend_Auth::getInstance()->getIdentity()->id);
        $this->view->listPedido = $this->_bo->find($criteria);
    }

    public function gridStatusAction()
    {
        $allParams = $this->getAllParams();
        $this->view->processoByStatusList = $this->_bo->getProcessoByStatus($allParams);
    }

    public function gridPendenciaAction()
    {
        $allParams = $this->getRequest()->getParams();
        if(isset($allParams['searchString'])){
            $allParams['searchString'] = str_replace(array('.', '-', '/'), '', $allParams['searchString']);
        }

        $paginator = $this->_bo->paginatorByPendencia($allParams);

        $this->view->paginator = $paginator;
    }

    public function ordemServicoAction()
    {
        $this->_helper->layout()->disableLayout();

        $proprietarioSession = new Zend_Session_Namespace('proprietario');

        if ($proprietarioSession->proprietario['logo_report']){

            $logoReport = $proprietarioSession->proprietario['logo_report'];
        }else{
            $logoReport = null;
        }

        $pdf = new App_Util_Pdf();

        $idProcesso       = $this->getParam('pro_id');
        $procesoObj        = $this->_bo->get($idProcesso);

        $movimentoBo           = new Material_Model_Bo_Movimento();
        $materialProcessoBo    = new Processo_Model_Bo_MaterialProcesso();
        $processoServicoBo     = new Processo_Model_Bo_ProcessoServico();

        $this->view->processo            = $procesoObj;
        $this->view->cliente             = $procesoObj->getEmpresa();
        $this->view->servicoList         = $processoServicoBo->find(array('id_processo = ?' => $idProcesso, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO));
        $this->view->materialClienteList = $materialProcessoBo->find(array(
                                                                    'id_processo = ?'   	=> $idProcesso,
                                                                    'ativo = ?' 			=> App_Model_Dao_Abstract::ATIVO,
                                                                    'id_tp_material = ?'	=> Processo_Model_Bo_TipoMaterial::CLIENTE
                                                                    )
                                                                );
        $this->view->materialProprioList = $materialProcessoBo->find(array(
                                                                    'id_processo = ?' 		=> $idProcesso,
                                                                    'ativo = ?' 			=> App_Model_Dao_Abstract::ATIVO,
                                                                    'id_tp_material = ?' 	=> Processo_Model_Bo_TipoMaterial::PROPRIO,
                                                                    'id_status_material <> ?'=> Processo_Model_Bo_StatusMaterial::CANCELADO
                                                                    )
                                                                );

        $html = $this->view->render('processo/ordem-servico.phtml');

        $pdf->modificarFonte('helvetica', 10);
        $pdf->adicionarHtml($html);
        $pdf->abrirArquivo();exit();
    }

    public function ordemProducaoAction()
    {
        $this->_helper->layout()->disableLayout();

        $proprietarioSession = new Zend_Session_Namespace('proprietario');

        if ($proprietarioSession->proprietario['logo_report']){

            $logoReport = $proprietarioSession->proprietario['logo_report'];
        }else{
            $logoReport = null;
        }

        $pdf = new App_Util_Pdf();

        $idProcesso       = $this->getParam('pro_id');
        $procesoObj        = $this->_bo->get($idProcesso);

        $movimentoBo           = new Material_Model_Bo_Movimento();
        $materialProcessoBo    = new Processo_Model_Bo_MaterialProcesso();
        $processoServicoBo     = new Processo_Model_Bo_ProcessoServico();

        $this->view->processo            = $procesoObj;
        $this->view->cliente             = $procesoObj->getEmpresa();
        $this->view->servicoList         = $processoServicoBo->find(array('id_processo = ?' => $idProcesso, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO));
        $this->view->materialClienteList = $materialProcessoBo->find(array('id_processo = ?' => $idProcesso, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_tp_material = ?' => Processo_Model_Bo_TipoMaterial::CLIENTE));
        $this->view->materialProprioList = $materialProcessoBo->find(array('id_processo = ?' => $idProcesso, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_tp_material = ?' => Processo_Model_Bo_TipoMaterial::PROPRIO));

        $html = $this->view->render('processo/ordem-producao.phtml');

        $pdf->modificarFonte('helvetica', 10);
        $pdf->adicionarHtml($html);
        $pdf->abrirArquivo();exit();
    }

    public function autocompleteAction()
    {
        $term = $this->getRequest()->getParam('term');
        $list = $this->_bo->getAutocomplete($term, false);
        $this->_helper->json($list);
    }

    public function getAction()
    {
        $idProcesso = $this->getParam('id_processo');
        $processo = $this->_bo->get($idProcesso);
        $processoArray = $processo->toArray();
        $processoArray['entidade'] = $processo->getEmpresa()->nome_razao;
        $this->_helper->json($processoArray);
    }

    public function autocompleteCodigoAction()
    {
        $term = $this->getRequest()->getParam('term');
        $list = $this->_bo->getAutocomplete($term, false, 'pro_id', 'pro_codigo');
        $this->_helper->json($list);
    }
}