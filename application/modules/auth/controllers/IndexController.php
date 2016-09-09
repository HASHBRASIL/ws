<?php

class Auth_IndexController extends App_Controller_Action
{
    /**
     *
     * @var Auth_Model_Bo_Usuario
     */
    protected $_bo;

    public function indexAction()
    {
        $this->_helper->layout->setLayout('metronic');
        // tela inicial depois de autenticar.


        echo "<PRE>";
        $identity       = Zend_Auth::getInstance()->getIdentity();
        var_dump($identity);




//        var_dump($identity);exit();
    }

//    public function preDispatch()
//    {
//    	$this->_authAnonymous = true;
//    	parent::preDispatch();
//
//    }

//    public function init()
//    {
//        $this->_bo = new Auth_Model_Bo_Usuario();
//        $this->_messageBroker = App_Validate_MessageBroker::getInstance();
//    }

    public function saltAction()
    {
        $this->_helper->layout->disableLayout();

        $usuarioBo = new Auth_Model_Bo_Usuario();

//        var_dump($response);

        $response = $usuarioBo->getUsuarioLoginData($this->_getParam('usuario'));

        $this->_helper->json($response);
    }


    public function loginAction()
    {
        $this->_helper->layout->setLayout('simple_layout');
        $this->view->redirect = $this->getParam("redirect");

        if($this->_request->isPost()){

            $usuarioBo = new Auth_Model_Bo_Usuario();

            $result = $usuarioBo->authenticate($this->_getParam('usuario'), $this->_getParam('senha'));

            if ($result) {
                // @todo fazer ajuste para tempo de sessao => mandar para tela de login
//                //zerando o contador de tempo ocioso
//                $timeSession = new Zend_Session_Namespace( 'timeSessionExpire' );
//                $timeSession->limiteTime = null;

                if ($this->getParam("redirect")) {
                    $this->redirect($this->getParam("redirect"));
                } else {
                    $this->redirect('/');
                }

            } else {
                $this->_helper->_flashMessenger->addMessage('credenciais inválidas');
                $this->redirect('/auth/index/login');
            }

        }
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        Zend_Session::destroy();
        $this->redirect('auth/index/login');
    }

    public function resetPassAction()
    {
        $this->_helper->layout->setLayout('simple_layout');

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $email = $this->getRequest()->getParam('email');
        $celular = $this->getRequest()->getParam('celular');

        try {
            if (empty($email) && empty($celular)) {
                throw new Exception('Você deve informar seu e-mail ou o número do seu celular.');
            }

            if (!empty($email)) {
                $this->resetPassByEmail($email);
            }

            if (!empty($celular)) {
                $this->resetPassByCelular($celular);
            }
        } catch (Exception $ex) {
            $this->_helper->_flashMessenger->addMessage($ex->getMessage());
            $this->redirect('/auth/index/reset-pass');
        }
    }

    protected function resetPassByEmail($email)
    {
        $dadosInfo = (new Config_Model_Bo_Informacao())->getInfoByMetanomeEValor(
            Config_Model_Bo_TipoInformacao::META_EMAIL,
            $email
        );
        $idPessoa = current($dadosInfo)['id_pessoa'];

        if (empty($idPessoa)) {
            throw new Exception('Não foi encontrado nenhum usuário com o email informado.');
        }

        $ticket = (new Auth_Model_Bo_Usuario())
            ->geraTicketSenha($idPessoa);

        $urlAlteracao = $this->_helper->configuracao('filedir', 'site')
            . $this->view->url([
                'action' => 'change-pass',
                'ticket' => $ticket
            ]);

        // -- Enviar mensagem e encaminhar para tela de aviso do envio
        $conteudoEmail = <<<HTML
E-mail de alteração de senha.<br />
Altere a sua senha na url: <a href="$urlAlteracao">{$urlAlteracao}</a>
HTML;

        $this->_helper->campanha(
            [
                'assunto' => 'Alteração de senha',
                'mensagem' => $conteudoEmail
            ],
            [$email]
        );

        $this->_helper->_flashMessenger->addMessage('Dentro de alguns instantes '
            . 'você receberá um e-mail com instruções para alterar sua senha.');

        $this->redirect();
    }

    protected function resetPassByCelular($celular)
    {
        $dadosInfo = (new Config_Model_Bo_Informacao())->getInfoByMetanomeEValor(
            Config_Model_Bo_TipoInformacao::META_NUMTEL,
            $celular
        );
        $idPessoa = current($dadosInfo)['id_pessoa'];

        if (empty($idPessoa)) {
            throw new Exception('Não foi encontrado nenhum usuário com o número '
                . 'de telefone informado.');
        }

        $ticket = (new Auth_Model_Bo_Usuario())
            ->geraTicketSenha($idPessoa);

        // -- enviar sms
        $conteudoSms = <<<SMS
Seu código de alteração de senha é: {$ticket}
SMS;
        $this->_helper->campanha->enviarSms($celular, '21#1', $conteudoSms, $ticket);
        $this->redirect('/auth/index/phone-confirmation');
    }

    protected function phoneConfirmationAction()
    {
        $this->_helper->layout->setLayout('simple_layout');
        if ($this->getRequest()->isPost()) {
            $this->redirect(
                '/auth/index/change-pass/ticket/' . $this->getRequest()->getParam('ticket'),
                ['exit' => true]
            );
        }
    }

    protected function changePassAction()
    {
        $this->_helper->layout->setLayout('simple_layout');
        if ($this->getRequest()->isPost()) {

            try {
                $senha = $this->getParam('senha');
                $senha2 = $this->getParam('senha2');

                if ($senha !== $senha2) {
                    throw new Exception('As senhas não conferem, tente novamente.');
                }

                $usuarioBo = new Auth_Model_Bo_Usuario();
                list(, $salt, $encryptedpass) = $usuarioBo->criaPassword($senha);
                $usuarioId = current($usuarioBo->validaTicketSenha($this->getParam('ticket')))['id'];

                $usuarioBo->update([
                    'salt' => $salt,
                    'password_encrypted' => $encryptedpass,
                    'ticket_senha' => null
                ], $usuarioId);

                $this->_helper->_flashMessenger->addMessage('Sua senha foi alterada com sucesso!');
                $this->redirect('/auth/index/login');

            } catch (Exception $ex) {
                $this->_helper->_flashMessenger->addMessage($ex->getMessage());
                $this->redirect("/auth/index/change-pass/ticket/{$this->getParam('ticket')}");
            }
        }

        try {

            if (empty($ticket = $this->getRequest()->getParam('ticket'))) {
                throw new Exception('O ticket de troca de senha informado é inválido.');
            }

            if (!(new Auth_Model_Bo_Usuario())->validaTicketSenha($ticket)) {
                throw new Exception('O ticket de troca de senha informado é inválido.');
            }

        } catch (Exception $ex) {
            $this->_helper->_flashMessenger->addMessage($ex->getMessage());
            $this->redirect('/auth/index/reset-pass');
        }
    }
}
