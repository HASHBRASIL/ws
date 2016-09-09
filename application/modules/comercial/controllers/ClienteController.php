<?php
class Comercial_ClienteController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Comercial_Model_Bo_Cliente
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Comercial_Model_Bo_Cliente();
        $this->_helper->layout()->setLayout('metronic');
        $this->_redirectDelete = "empresa/empresa/grid";
        parent::init();
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

        $grupoGeograficoBo         = new Sis_Model_Bo_GrupoGeografico();
        $caracteristicaBo          = new Empresa_Model_Bo_Caracteristica();
        $caracteristicaEmpresaBo   = new Empresa_Model_Bo_CaracteristicaEmpresa();
        $grupoEmpresaBo            = new Sis_Model_Bo_GrupoGeograficoEmpresa();

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
        //combo do wizard
        $this->view->pairsCaracteristica      = $caracteristicaBo->getPairs();
        $this->view->comboGrupoGeografico     = $grupoGeograficoBo->getPairs();
        $this->view->valueGrupoGeografico     = $this->getParam('grupo_geografico')? $this->getParam('grupo_geografico') : $grupoEmpresaBo->getIdGrupoByGrupo($this->_id);
        $this->view->valueCaracteristica      = $this->getParam('perfil')? $this->getParam('perfil') : $caracteristicaEmpresaBo->getIdPerfilByEmpresa($this->_id);
    }

    public function gridAction()
    {
        $allParams = $this->getRequest()->getParams();
        if(isset($allParams['searchString'])){
            $allParams['searchString'] = str_replace(array('.', '-', '/'), '', $allParams['searchString']);
        }
        $paginator = $this->_bo->paginator($allParams);
        $this->view->paginator = $paginator;
    }

}