<?php

class Config_PermissionController extends App_Controller_Action_Twig
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

    public function usuarioAction()
    {
        $idTime = $this->getParam('id');

        $this->view->file = 'permission-usuario.html.twig';
        $this->view->data = array( 'timeId' => $idTime);
    }

    public function formAction()
    {
        $idTime = $this->getParam('id');
        $idUsuario = $this->getParam('usuario');
        $rlppBo = new Legacy_Model_Bo_RlPermissaoPessoa();

        $rowset = $rlppBo->getServicosByUsuarioByTime($idUsuario, $idTime);

        $this->view->file = 'permission-form.html.twig';
        $this->view->data = array('servicos' => $rowset, 'timeId' => $idTime, 'idUsuario' => $idUsuario);
    }

    public function saveAction()
    {
        $servicos = $this->getParam('servicos');
        $idUsuario = $this->getParam('usuario');
        $idTime = $this->getParam('id');
        $dtExpiracao = $this->getParam('dt_expiracao');

        $rlgsBo = new Legacy_Model_Bo_RlPermissaoPessoa();

        $rlgsBo->salvarPermissao($idUsuario, $idTime, $servicos, $dtExpiracao);

        $this->_helper->FlashMessenger('Salvo com sucesso. As atualizações vão ser aplicadas ao autenticar no sistema.');

        // ws target
        $this->redirect('config/license/index');

    }

    public function autocompleteAction()
    {
        $data = $this->getAllParams();

        $usuarioBo = new Auth_Model_Bo_Usuario();

        $rowset = $usuarioBo->autoComplete($data['search']);

        $result = array();
        foreach ($rowset as $row) {
            $result[] = array('id' => $row->id, 'text' => $row->nomeusuario);
        }

        $this->_helper->json(array('results' => $result));
    }

}

