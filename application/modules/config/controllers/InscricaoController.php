<?php
/**
 * HashWS
 */

/**
 * Gerencia as requisições de inscrição.
 *
 */
class Config_InscricaoController extends App_Controller_Action
{
    /**
     * @var Zend_Session_Namespace Armazena dados da inscricação antes da persistência.
     */
    protected $session;


 	public function init()
    {
        parent::init();

        $this->_translate = Zend_Registry::get('Zend_Translate');

        $this->_helper->layout()->setLayout('publico');
        $this->session = new Zend_Session_Namespace('inscricao');
    }

    /**
     * configuração padrão para definir o uso de twig em todas as actions do controller
     * @todo pode-se criar um App_controller_Action_Twig para facilitar isso
     */
    public function postDispatch()
    {
        $this->view->params = $this->getAllParams();
        $this->renderScript('twig.phtml');
        return parent::postDispatch();
    }

    public function cleanAction()
    {
        $this->session->unsetAll();
        $this->redirect('/config/inscricao');
    }

    /**
     * Esta rota inicia o processo de inscrição, solicitando um endereço de e-mail.
     */
    public function indexAction()
    {
        if ($this->_request->isPost()) {
            $this->session->email = trim($this->_request->getParam('email'));
            $this->session->produto = strtolower($this->_request->getParam('produto'));

            unset($this->session->listausuarios);

            $pessoas = (new Legacy_Model_Bo_Pessoa())
                    ->findPessoasUsuarioByEmail($this->_request->getParam('email'));

            if (empty($pessoas)) {
                $this->_redirect('/config/inscricao/time');
                return;
            }

            if ($usuarios = $this->usuariosPessoas($pessoas)) {
                $this->session->listausuarios = $usuarios;
                $this->_redirect('/config/inscricao/usuario-cadastrado');
                return;
            }
        }

        $this->view->data = [
            'email'   => $this->session->email,
            'produto' => $this->_request->getParam('produto')
        ];
        $this->view->file = 'index.html.twig';
    }

    /**
     * Retorna os usuários encontrados em uma coleção de pessoas.
     *
     * @param array[] $pessoas Lista de pessoas
     * @return string[]
     */
    protected function usuariosPessoas($pessoas)
    {
        $usuarios = [];

        foreach ($pessoas as $pessoa) {
            if (!empty($pessoa['nomeusuario'])) {
                $usuarios[] = $pessoa['nomeusuario'];
            }
        }

        return $usuarios;
    }

    /**
     * Rota de finalização.
     *
     * Esta rota é acessada quando o usuário informa um e-mail que tenha ao menos
     * um usuário associado a ele. Nesse caso, o usuário é impedido de continuar o processo.
     */
    public function usuarioCadastradoAction()
    {
        $this->view->data = [
            'listausuarios' => $this->session->listausuarios
        ];
        $this->view->file = 'usuario-cadastrado.html.twig';
    }

    /**
     * Rota de solicitação do nome do time.
     */
    public function timeAction()
    {
        if ($this->_request->isPost()) {
            $time = trim($this->_request->getParam('time'));

            if (!empty($time)) {
                $this->session->time = $time;
                $this->_redirect('/config/inscricao/alias');
                return;
            }

            $this->_addMessageError('time_nao_vazio');
        }

        $this->view->data = [
            'time' => $this->session->time
        ];
        $this->view->file = 'time.html.twig';
    }

    /**
     * Rota de solicitação do alias do time.
     */
    public function aliasAction()
    {
        $data = [];

        if ($this->_request->isPost()) {
            $alias = trim($this->_request->getParam('alias'));

            try {
                if (!ctype_alnum($alias)) {
                    throw new App_Validate_Exception('alias_carac_invalido');
                }

                if ((new Config_Model_Bo_GrupoMetadata())->findByAlias($alias)) {
                    throw new App_Validate_Exception('alias_ja_usado');
                }

                $this->session->alias = $alias;

                $this->_redirect('/config/inscricao/usuario');
                return;

            } catch (App_Validate_Exception $e) {
                $this->_addMessageError($e->getMessage());
                $data['alias'] = $alias;
            }
        }

        if (!array_key_exists('alias', $data)) {
            $data['alias'] = $this->session->alias;
        }

        $this->view->data = $data;
        $this->view->file = 'alias.html.twig';
    }

    /**
     * Rota de solicitação do nome do usuário.
     */
    public function usuarioAction()
    {
        $data = [];
        
        if ($this->_request->isPost()) {
            $usuario = $this->_request->getParam('usuario');

            try {
                if (!ctype_alnum($usuario) || ($usuario != strtolower($usuario))) {
                    throw new App_Validate_Exception('usuario_carac_invalido');
                }

                $criteria = ['nomeusuario = ?' => $usuario];
                if ((new Auth_Model_Bo_Usuario())->findOne($criteria)) {
                    throw new App_Validate_Exception('usuario_ja_usado');
                }

                $this->session->usuario = $usuario;
                $this->redirect('/config/inscricao/confirmacao');
                return;

            } catch (App_Validate_Exception $e) {
                $this->_addMessageError($e->getMessage());
                $data['usuario'] = $usuario;
            }
        }

        if (!array_key_exists('usuario', $data)) {
            $data['usuario'] = $this->session->usuario;
        }

        $this->view->data = $data;
        $this->view->file = 'usuario.html.twig';
    }

    /**
     * Rota de confirmação e criação do time.
     */
    public function confirmacaoAction()
    {
        if ($this->_request->isPost()) {
            list(
                $this->session->senha,
                $this->session->idPessoa
            ) = (new Legacy_Model_Bo_Pessoa())->criaPessoaETime(
                $this->session->usuario,
                $this->session->email,
                $this->session->time,
                strtolower($this->session->alias),
                $this->session->produto
            );

            $this->redirect('/config/inscricao/finalizado');
            return;
        }

        $this->view->data = $this->session->getIterator()->getArrayCopy();
        $this->view->file = 'confirmacao.html.twig';
    }

    /**
     * Rota de finalização do processo e envio e-mail de ativação.
     */
    public function finalizadoAction()
    {
        if (empty($this->session->senha)) {
            $this->redirect('/config/inscricao');
            return;
        }

        // -- @todo Transformar em um help
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $opcoes = $config->getOption('filedir');
        $url = $opcoes['site'];

        $urlValidacao = $url . $this->view->url([
                'controller' => 'validacao',
                'action' => 'index',
                'codigo' => $this->session->idPessoa
            ]);

        $conteudoEmail = <<<CONTEUDO
E-mail de ativação

Ative sua conta na url: {$urlValidacao}

Dados de acesso
O seu usuário é: {$this->session->usuario}
A sua senha é: {$this->session->senha}

Dúvidas: suporte@titaniumtech.com.br
CONTEUDO;

        $this->_helper->campanha(
            [
                'assunto' => $this->_translate->translate('email_de_confirmacao'),
                'mensagem' => $conteudoEmail
            ],
            [$this->session->email]
        );

        $this->session->unsetAll();

        $this->view->data = [];
        $this->view->file = 'finalizado.html.twig';
    }
}
