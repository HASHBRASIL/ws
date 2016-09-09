<?php

/**
 * Created by PhpStorm.
 * User: solbisio
 * Date: 14/12/15
 * Time: 21:15
 */
class Legacy_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        //die();

        $identity = Zend_Auth::getInstance()->getIdentity();
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');

        $request = $this->getRequest();
        $params  = $request->getParams();

        $idServico = $params['servico'];

        $HASH_SERVICO = $identity->servicosAtual[$idServico];

//        $SERVICO = $HASH_SERVICO;

        $_SESSION['USUARIO']['ID']   = $identity->id;
        $_SESSION['USUARIO']['NOME'] = $identity->pessoa->nome;
        $_SESSION['USUARIO']['FOTO'] = '';
        $_SESSION['TIME']['ID']      = $identity->grupo['id'];


//      $this->_helper->layout->disableLayout();
//      $viewRenderer->setNoRender(true);
        require_once "../home2.php";


        $this->_helper->layout->setLayout('novo_hash');


//        require_once "../includes/functions.php";
//
//        spl_autoload_register('hash_autoloader');
//
//        require_once "../includes/databaseconnect.php";
//        require_once "../includes/connect.php";
//        require_once "../includes/twig.php";
//
//
//        ob_start();
//
//        if (isset($HASH_SERVICO['metadata']['ws_show']) && $HASH_SERVICO['metadata']['ws_show'] != 'reload') {
////            $this->_helper->layout->disableLayout();
////            $viewRenderer->setNoRender(true);
//
//        } else {
////            $viewRenderer->setNoRender(true);
//            include "../includes/header.php";
//
//        }
//
//            require_once "../includes/rastro.php";
//
//            include '../includes/' . $SERVICO['ws_arquivo'];
//            $output = ob_get_clean();
//            echo $output;
////            $response = $this->getResponse();
//
////            $response->setBody($output);
//
////            $response->sendResponse();
//
//
//
//        if (isset($HASH_SERVICO['metadata']['ws_show']) && $HASH_SERVICO['metadata']['ws_show'] == 'reload') {
//            include "../includes/footer.php";
//        }

    }
}