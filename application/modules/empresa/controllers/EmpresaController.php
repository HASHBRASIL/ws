<?php
class Empresa_EmpresaController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Legacy_Model_Bo_Pessoa
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Legacy_Model_Bo_Pessoa();
        $this->_helper->layout()->setLayout('metronic');
        $this->_aclActionAnonymous = array("autocomplete", "index",'fornecedor-json', 'grupo-json','autocomplete-cnpj','transportador-json', 'transportador-cnpj', 'get', 'autocomplete-geral');
        $this->_redirectDelete = "empresa/empresa/grid";
        parent::init();
    }

    public function fornecedorJsonAction()
    {
        $fornecedorBo     = new Empresa_Model_Bo_Fornecedor();
        $this->_AutocompleteJson($fornecedorBo);
    }

    public function grupoJsonAction()
    {
        $empresaGrupoBo = new Empresa_Model_Bo_EmpresaGrupo();
        $this->_AutocompleteJson($empresaGrupoBo);
    }

    public function geralJsonAction()
    {
        $this->_AutocompleteJson($this->_bo);
    }

    private function _AutocompleteJson($object)
    {
        $term = $this->getRequest()->getParam('term');
        $list = $object->getAutocomplete($term);
        $this->_helper->json($list);
    }


    public function autocompleteCnpjAction()
    {
        $term = $this->getRequest()->getParam('term');
        $term = str_replace(array('.', '-', '/'), '', $term);
        $list = $this->_bo->getAutocompleteByCnpj($term);
        $this->_helper->json($list);
    }

    public function autocompleteAction()
    {
        $term = $this->getRequest()->getParam('term');
        $list = $this->_bo->getAutocompleteEmpresa($term);
        $this->_helper->json($list);
    }

    public function transportadorJsonAction()
    {
        $term = $this->getRequest()->getParam('term');
        $list = $this->_bo->getAutocompleteTransportador($term);
        $this->_helper->json($list);
    }

    public function transportadorCnpjAction()
    {
        $term = $this->getRequest()->getParam('term');
        $term = str_replace(array('.', '-', '/'), '', $term);
        $where = array(
                'ativo = ?'         => App_Model_Dao_Abstract::ATIVO
        );
        $list = $this->_bo->getAutocompleteByCnpj($term, $where);
        $this->_helper->json($list);
    }


    public function _initForm()
    {
        $tipoPessoaBo    = new Sis_Model_Bo_TipoPessoa();
        $portalBo        = new Empresa_Model_Bo_Portal();
        $mailMktBo       = new Empresa_Model_Bo_MailMarketing();
        $tpClienteBo     = new Empresa_Model_Bo_TipoCliente();
        $tpFornecedorBo  = new Empresa_Model_Bo_TipoFornecedor();
        $segmentoBo   	 = new Sis_Model_Bo_Segmento();
        $indicacaoBo     = new Sis_Model_Bo_Indicacao();
        $estadoBo        = new Sis_Model_Bo_Estado();
        $tipoEnderecoBo  = new Sis_Model_Bo_TipoEndereco();
        $contatoRefBo        = new Sis_Model_Bo_ContatoReferenciado();
        $contatoDeptBo       = new Sis_Model_Bo_ContatoDepartamento();
        $cargoBo             = new Sis_Model_Bo_Cargo();

        $this->view->comboTipoPessoa    = $tipoPessoaBo->getPairs(false);
        $this->view->comboPortal        = array(null => '---- Selecione ----')+$portalBo->getPairs();
        $this->view->comboMailMkt       = array(null => '---- Selecione ----')+$mailMktBo->getPairs();
        $this->view->comboCliente       = array(null => '---- Selecione ----')+$tpClienteBo->getPairs();
        $this->view->comboFornecedor    = array(null => '---- Selecione ----')+$tpFornecedorBo->getPairs();
        $this->view->comboSegmento      = array(null => '---- Selecione ----')+$segmentoBo->getPairs();
        $this->view->comboIndicacao     = array(null => '---- Selecione ----')+$indicacaoBo->getPairs();
        $this->view->comboEstado        = array(null => "Estado")+$estadoBo->getPairs(false);
        $this->view->comboTipoEndereco  = $tipoEnderecoBo->getPairs(false);
        // combo do dialog do contato
        $this->view->comboContatoRef        = array(null => "---- Selecione ----")+$contatoRefBo->getPairs();
        $this->view->comboContatoDept       = array(null => '---- Selecione ----')+$contatoDeptBo->getPairs();
        $this->view->comboCargo             = array(null => "---- Selecione ----")+$cargoBo->getPairs();
    }

    public function gridAction()
    {
        $allParams = $this->getRequest()->getParams();
        if(isset($allParams['searchString'])){
            $allParams['searchString'] = str_replace(array('.', '-', '/'), '', $allParams['searchString']);
        }
        if(!isset($allParams['itens']))
            $allParams['itens'] = 250;

        $paginator = $this->_bo->paginator($allParams);
        $this->view->paginator = $paginator;
    }

    public function indexAction()
    {

    	$this->view->countEmpresa = $this->_bo->countEmpresas();
    	$this->view->countEmpresasLastDays = $this->_bo->countEmpresasLastDays();
    	$this->view->timezone = Zend_Registry::get('config')->phpSettings->date->timezone;

    }

    public function gridEnderecoAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->getParam('id_empresa');
        $empresa = $this->_bo->get($id);
        $this->view->listEndereco = $empresa->getListEndereco();
    }

    public function getAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $id = $this->getParam('id');
        $empresa = $this->_bo->get($id);
        $empresaJson = array();
        foreach ($empresa as $key => $value){
            $empresaJson[$key] = $value;
        }
        $empresaJson = $empresaJson + array(
                'tipo_cliente'    => $empresa->getTipoCliente() ? $empresa->getTipoCliente()->tic_descricao : null,
                'tipo_fornecedor' => $empresa->getTipoFornecedor() ? $empresa->getTipoFornecedor()->tif_descricao : null,
                'segmento'        => $empresa->getSegmento() ? $empresa->getSegmento()->seg_descricacao : null,
                'indicacao'       => $empresa->getIndicacao() ? $empresa->getIndicacao()->ind_descricao : null,
                'portal'          => $empresa->getPortal() ? $empresa->getPortal()->poc_descricacao : null,
                'mail_marketing'  => $empresa->getMailMarketing() ? $empresa->getMailMarketing()->smk_descricao : null,
                'tipo_pessoa'     => $empresa->getTipoPessoa() ? $empresa->getTipoPessoa()->tps_descricao : null
        );
        $this->_helper->json($empresaJson);
    }

    public function gridFinanceiroAction()
    {
        $this->_helper->layout->disableLayout();

        $financialBo        = new Financial_Model_Bo_Financial();
        $idEmpresa          = $this->getParam('idEmpresa');
        $financeiro         = $financialBo->findByEmpresa($idEmpresa);

        $this->view->financeiro = $financeiro;

        $paramsSearch = new Zend_Session_Namespace('paramsSearch');
        $paramsSearch->paramsSearch = array('empresaList' => $idEmpresa);;
    }

    public function gridContatoAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->getParam('id_empresa');
        $empresa = $this->_bo->get($id);
        $this->view->listContato = $empresa->getListContato();
    }

    public function formWizardAction()
    {

        $grupoGeograficoBo         = new Sis_Model_Bo_GrupoGeografico();
        $caracteristicaBo          = new Empresa_Model_Bo_Caracteristica();
        $caracteristicaEmpresaBo   = new Empresa_Model_Bo_CaracteristicaEmpresa();
        $grupoEmpresaBo            = new Sis_Model_Bo_GrupoGeograficoEmpresa();

        parent::formAction();
        $this->view->pairsCaracteristica      = $caracteristicaBo->getPairs();
        $this->view->comboGrupoGeografico     = $grupoGeograficoBo->getPairs();
        $this->view->valueGrupoGeografico     = $this->getParam('grupo_geografico')? $this->getParam('grupo_geografico') : $grupoEmpresaBo->getIdGrupoByGrupo($this->_id);
        $this->view->valueCaracteristica      = $this->getParam('perfil')? $this->getParam('perfil') : $caracteristicaEmpresaBo->getIdPerfilByEmpresa($this->_id);
    }

    public function gridInativosAction()
    {
    	$allParams = $this->getRequest()->getParams();
    	if(isset($allParams['searchString'])){
    		$allParams['searchString'] = str_replace(array('.', '-', '/'), '', $allParams['searchString']);
    	}
    	$paginator = $this->_bo->paginatorInativos($allParams);
    	$this->view->paginator = $paginator;

    }

    public function recycleEmpresaAjaxAction(){

    	if($this->getRequest()->getParam("id")){

    		$empresaObj =  $this->_bo->find(array("id = ?" => $this->getRequest()->getParam("id")))->current();
    		$empresaObj->ativo = 1;
    		try {

    			$empresaObj->save();

    			$result = array("success" => true);

    			$this->_helper->json($result);

    		}catch (Exception $e){

    			$result = array("success" => false);

    			$this->_helper->json($result);

    		}
    	}

    }

    public function mergerAction()
    {
    	//desabilitando o limite de memória de processamento do servidor
    	ini_set( "memory_limit", -1 );
    	//desabilitando o limite do tempo de execução
    	ini_set( "max_execution_time", 0 );
    	$this->_helper->viewRenderer->setNoRender();
    	$this->_helper->layout->disableLayout();
    	$caracteristica = $this->getParam('caracteristica');
    	$caracteristicaEmpresa = new Empresa_Model_Bo_CaracteristicaEmpresa();
    	$count = 0;
    	if(!empty($caracteristica)){
    		if($caracteristica == Empresa_Model_Bo_Caracteristica::FUNCIONARIO){
    			$empresaList = $this->_bo->find(array('funcionario = 1'));
    		}elseif ($caracteristica == Empresa_Model_Bo_Caracteristica::GRUPO){
    			$empresaList = $this->_bo->find(array('grupo = 1'));
    		}elseif ($caracteristica == Empresa_Model_Bo_Caracteristica::TRANSPORTADOR){
    			$empresaList = $this->_bo->find(array('transportador = 1'));
    		}elseif ($caracteristica == Empresa_Model_Bo_Caracteristica::FORNECEDOR){
    			$empresaList = $this->_bo->find(array('tif_id is not null and tif_id <> 3 and tif_id <> 2 '));
    		}
    		if(count($empresaList) > 0){
	    		foreach ($empresaList as $empresa){
	    			$caracteristicaEmpresa->delete($empresa->id, $caracteristica);
	    			$object = $caracteristicaEmpresa->get();
	    			$object->id_caracteristica = $caracteristica;
	    			$object->id_empresa = $empresa->id;
	    			$object->save();
	    			$count++;
	    			echo "Cadastro com id_caracteristica = ".$object->id_caracteristica." id_empresa = ".$object->id_empresa."<br>";
	    		}
    		}
    		echo "<br>$count<br>";
    	}
    }

    public function autocompleteGeralAction()
    {
        $term         = $this->getParam('term');
        $criteria     = $this->getAllParams();
        $list         = $this->_bo->autocompleteGeral($term, $criteria);
        $this->_helper->json($list);
    }
}