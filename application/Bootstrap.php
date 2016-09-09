<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $config);
        return $config;
    }

    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

    protected function _initHelpers()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->initView();

        // add zend view helper path
        $viewRenderer->view->addHelperPath('App/Views/Helpers/');
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH .'/general/helpers/Controller',
            'Controller'
        );
    }


    function _initApplication ()
    {

        $this->bootstrap('frontcontroller');
        $front = $this->getResource('frontcontroller');

        $router = new App_Controller_Router_Rewrite();
        $front->setRouter($router);

//        $front  = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        //new dBug($HASH_SERVICO);

        $route  = new Zend_Controller_Router_Route_Regex(
            '(.+\.php)',
            array(
                'module'     => 'legacy',
                'controller' => 'index',
                'action'     => 'index'
            ));
//            array(
//                1 => 'servico'
//            ),
//            'home.php?servico=%s'
//        );

        //new dBug($route);

        $router->addRoute('home_servico', $route);


    }

}

