<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  17/10/2013
 */
class Compra_CampanhaController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Compra_Model_Bo_Campanha
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Compra_Model_Bo_Campanha();
        $this->_helper->layout()->setLayout('metronic');
        parent::init();
        $this->_id = $this->getParam('id_campanha');
        $this->_redirectDelete = "compra/campanha/grid";
    }

    public function _initForm()
    {
        $tipoComissaoBo = new Compra_Model_Bo_TipoComissao();
        $tipoMoedaBo 	= new Financial_Model_Bo_Moeda();
        $grupoBo 		= new Material_Model_Bo_Grupo();
        $tpPessoa       = new Sis_Model_Bo_TipoPessoa();

        $this->view->comboTpComissao 	= array(null => '---- Selecione ----')+$tipoComissaoBo->getPairs(false);
        $this->view->comboMoeda 		= $tipoMoedaBo->getPairs(false, null, null, 'moe_defaut DESC');
        $this->view->comboGrupo			= array(null => '---- Selecione ----')+$grupoBo->getPairs();
        $this->view->comboTpFisica      = $tpPessoa->getPairs(false);
    }

    public function gridAction()
    {
    	$this->view->listCampanha = $this->_bo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO));
    }

}