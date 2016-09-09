<?php

class App_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    /**
     * (non-PHPdoc)
     * @see library/Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract::routeStartup()
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        // @todo regras para sobrescrever rota caso seja necessário
    }

    /**
     * (non-PHPdoc)
     * @see library/Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract::routeShutdown()
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $options  = array(
            'module'     => $request->getModuleName(),
            'controller' => $request->getControllerName()
        );

        $requestedAccess = implode('_', $options);

        // verifica ações liberadas
        $acl = Zend_Registry::getInstance()->get('config')->get('acl');

        foreach($acl as $module_controller) {
            if ($module_controller == $requestedAccess) {
                return;
            }
        }

        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();

        if ($auth->hasIdentity() && isset($identity->id) && $identity->id) {

            // @todo verifica se tem acesso = se tiver acesso = return
            if (array_key_exists($request->getParam('servico'), $identity->permission[$identity->time['id']])) {
                if ($identity->permission[$identity->time['id']][$request->getParam('servico')] > date('Y-m-d')) {
                    // @todo colocar verificação de DATA - dt_expiracao
                    return;
                }
            }

            // permissoes liberadas para logados
            $aclIdentity = Zend_Registry::getInstance()->get('config')->get('acl_identity');

            foreach($aclIdentity as $module_controller) {
                if ($module_controller == $requestedAccess) {
                    return;
                }
            }

        } else {
            // não está logado.
            if (!$request->isXmlHttpRequest()) {
                // redireciona para login se não for JSON.
                $redirect = $request->getPathInfo();

                $request->setModuleName('auth')
                        ->setControllerName('index')
                        ->setActionName('login')
                        ->setParam('redirect', $redirect);

                return;
            } else {
                // se for ajax joga erro na tela.
                throw new Exception('Usuário não autenticado.');
            }
        }


        // se chegou até aqui não tem acesso a tela solicitada Ou tela solicitada não existe.

        // não tem acesso.
        throw new Exception('Acesso negado.');
    }
}