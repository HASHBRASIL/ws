<?php

class Config_LicenseController extends App_Controller_Action_Twig
{
//    public function init()
//    {
//        parent::init();
//        $this->_helper->layout()->setLayout('novo_hash');
//    }

//    /**
//     * configuração padrão para definir o uso de twig em todas as actions do controller
//     * @todo pode-se criar um App_controller_Action_Twig para facilitar isso
//     */
//    public function postDispatch()
//    {
//        $this->view->params = $this->getAllParams();
//        $this->renderScript('twig.phtml');
//        return parent::postDispatch();
//    }

        public function init()
        {
            parent::init();
            $this->_bo = new Legacy_Model_Bo_Grupo();
        }

    public function indexAction()
    {
        $grupoBo = new Legacy_Model_Bo_Grupo();

        $this->header[] = array('campo' => 'nome', 'label' => 'Nome');
        $this->_gridSelect = $grupoBo->getLicense();

        parent::gridAction();
    }

    public function formAction()
    {
        $idTime = $this->getParam('id');
        $rlgsBo = new Legacy_Model_Bo_RlGrupoServico();

        $rowset = $rlgsBo->getModulosByTime($idTime);

        $this->view->file = 'license-form.html.twig';
        $this->view->data = array('modulos' => $rowset, 'timeId' => $idTime);
    }

    public function saveAction()
    {
        $modulos = $this->getParam('modulo');
        $idTime = $this->getParam('id');

        $rlgsBo = new Legacy_Model_Bo_RlGrupoServico();

        $rlgsBo->salvarModulos($idTime, $modulos);

        $this->_helper->FlashMessenger('Salvo com sucesso. As atualizações vão ser aplicadas ao autenticar no sistema.');

        // ws_target
        $this->redirect('config/license/index');

    }

    public function paginacaoAction()
    {
        $total = $this->getParam('total');

        $grupoBo = new Legacy_Model_Bo_Grupo();

        $rowset = $grupoBo->find(array('id_representacao is not null'), null, 10, $total);

        $this->_helper->json(array('data' => $rowset->toArray()));
    }

}

