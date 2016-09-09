<?php
/**
 * @author: Ellyson de Jesus Silva
 * @since: 02/07/2013
 */
class Service_ComponenteController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Service_Model_Bo_Componente
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Service_Model_Bo_Componente();
        parent::init();
    }


    public function gridAction()
    {
        $this->_helper->layout->disableLayout();

        $id_orcamento                 = $this->getParam('id_orcamento');
        $componenteList               = $this->_bo->getComponenteByOrcamento($id_orcamento);
        $this->view->componenteList   = $componenteList;
        $this->view->sumComponente    = $this->_bo->getPairsSumComponente($id_orcamento);
        $this->view->sumTpServico     = $this->_bo->getPairsSumTpServico($id_orcamento);

        //App_Util_Functions::debug($this->_bo->getPairsSumComponente($id_orcamento));
    }
}