<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
    	$this->_helper->layout()->disableLayout();
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                break;
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $exception = $errors->exception;
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $message = utf8_decode($exception->getMessage());

            $this->getResponse()->setHeader('Core-Error-File', $exception->getFile(), true)
                ->setHeader('Error-Line', $exception->getLine(), true);

            $message = str_replace("\n", ' ', $message);
            $this->getResponse()->setHttpResponseCode(202)
                ->setHeader('Error-Message', $message, true);

            if ($errors->exception->getMessage() == 'Usuário não autenticado.') {
                $this->getResponse()->setHttpResponseCode(401);
                $jsonMessage = 'permissão negada';
            } else {
                if ($errors->exception->getMessage() == 'Acesso restrito.') {
                    $jsonMessage = 'permissão negada';
                } else {
                    $jsonMessage = $message;
                }
            }

            $this->getResponse()->setHttpResponseCode(401);
            $errorExceptions = $errors->exception;

            $response = array(
                'error' => true,
                'msg' => $jsonMessage,
//                'trace' => Zend_Debug::dump($errorExceptions, null, false)
            );

            $this->_helper->json($response);

        }

            // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

