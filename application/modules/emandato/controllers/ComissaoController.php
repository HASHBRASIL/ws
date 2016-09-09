<?php
/**
 * @author Fernando Augusto
 * @since  17/05/2016
 */
class Emandato_ComissaoController extends App_Controller_Action_Twig
{
    /**
     * @var Content_Model_Bo_Ib
     */
    protected $_bo;



    public function init()
    {
        parent::init();
        $this->_bo = new Emandato_Model_Bo_Comissao();
    }

    public function indexAction() 
    {
        
    }

    public function gridAction()
    {
        $this->header = $this->_bo->getBasicConfigHeader($this->servico);

        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

        $select  = $this->_bo->getSelectGridComissoes();

        $this->_gridSelect = $select;
        
        parent::gridAction();
    }

    public function importaingestaoAction() 
    {
        $svcBO = new Config_Model_Bo_Servico();
        
        $ret = $this->_bo->importaIngestao();
        
        $this->_addMessageSuccess("Dados salvos com sucesso. $ret registros adicionados.", "home.php?servico=" . current($svcBO->getServicoByMetanome($this->servico['ws_target']))['id']);
    }

    public function gerasiteAction() {
        
        $siteBO = new Config_Model_Bo_Site();
        $svcBO = new Config_Model_Bo_Servico();
        $grp = $this->_bo->getComissao($this->getParam('id'));
        $site = strtolower($grp['metanome']) . '.emandato.com';
        $siteGrp = $siteBO->getSiteByIdPaiByAlias($grp['id'],strtolower($grp['metanome']));
        if($siteGrp == null){
            $ret = $this->_bo->geraSite($this->getParam('id'));
            $siteBO->geraDns(Config_Model_Bo_Site::EMANDATO,strtolower($grp['metanome']));
            $this->_addMessageSuccess("Site $site criado.", "home.php?servico=" . current($svcBO->getServicoByMetanome($this->servico['ws_target']))['id']);
        } else {
            $this->_addMessageError("Site $site existente.", "home.php?servico=" . current($svcBO->getServicoByMetanome($this->servico['ws_target']))['id']);
        }
        
    }

    public function vinculanoticiasAction() {
        echo 'EEEEEEEEE';
        exit;
    }

}