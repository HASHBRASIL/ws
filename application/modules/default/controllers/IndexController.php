<?php

class IndexController extends App_Controller_Action_AbstractCrud
{

    protected $_aclActionAnonymous = array("index");

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        //$twig->addGlobal('rastro', $rastro);
       // $this->_helper->layout->setLayout('metronic');
        // $this->_helper->layout->setLayout('novo');
        $this->_helper->layout->setLayout('novo_hash');
//    	$this->redirect("/empresa/empresa/index");
    }

    public function setLayoutConfigAction()
    {
        $this->_helper->layout->disableLayout();
        $request  = $this->getRequest();

        if ($request->isPost()){

            $identity = Zend_Auth::getInstance()->getIdentity();
            $postado  = $request->getPost();

            $identity->layoutConfig = array(
                'menuOrientacao' => $postado['menuOrientacao']
            );
        }
    }

    public function sidebarAction()
    {
        $idPessoa = Zend_Auth::getInstance()->getIdentity()->id;
        $avatar = current((new Config_Model_Bo_Informacao)
            ->getInfoPessoaByMetanome($idPessoa, 'AVATAR'))['valor'];

        if (empty($avatar)) {
            $this->view->avatar = 'img/img-list.jpg';
        } else {
            $this->view->avatar = $this->_helper->configuracao('filedir', 'url')
                . $avatar;
        }

        $idTime = Zend_Auth::getInstance()->getIdentity()->time['id'];
        $bgtime = current((new Config_Model_Bo_GrupoMetadata())
            ->listMetaByMetanome($idTime, 'ws_avatar')->toArray());

        if (empty($bgtime)) {
            $this->view->ws_avatar = 'img/logo-bg-cinza.png';
        } else {
            $this->view->ws_avatar = $this->_helper->configuracao('filedir', 'url') . $bgtime['valor'];
        }
    }

    public function topbarfullAction(){

        $idServico = $this->getParam('servico');
        $identity = Zend_Auth::getInstance()->getIdentity();

        if(empty($idServico)){
            $rota       = substr(Zend_Controller_Front::getInstance()->getRequest()->getPathInfo(), 1);
            $idServico  = array_search($rota, array_column($identity->servicos, 'rota', 'id'));
        }

        if(!empty($idServico)){
            $this->view->servico = $identity->servicosAtual[$idServico];
        }
    }

    public function topbarAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();

        $idServico = $this->getParam('servico');

        if(empty($idServico)){
            $rota       = substr(Zend_Controller_Front::getInstance()->getRequest()->getPathInfo(), 1);
            $idServico  = array_search($rota, array_column($identity->servicos, 'rota', 'id'));
        }

        //Importante para o o bom relacionamento entre legacy e zf1
        if(!class_exists('DatabaseConnection')){
            require APPLICATION_PATH.'/../includes/databaseconnect.php';
        }

        if(!empty($idServico)){
            $modelRastro = new Rastro();

            $this->view->servico = $identity->servicosAtual[$idServico];
            $this->view->rastro  = $modelRastro->getPath($idServico);
        }
    }

    public function listAction()
    {
        $this->_helper->layout->setLayout('metronic');

        $module_dir = substr(str_replace("\\","/",$this->getFrontController()->getModuleDirectory()),0,strrpos(str_replace("\\","/",$this->getFrontController()->getModuleDirectory()),'/'));
        $temp = array_diff( scandir( $module_dir), Array( ".", "..", ".svn", '.git'));
        $modules = array();
        $controller_directorys = array();
        foreach ($temp as $module) {
            if (is_dir($module_dir . "/" . $module)) {
                array_push($modules,$module);
                array_push($controller_directorys, str_replace("\\","/",$this->getFrontController()->getControllerDirectory($module)));
            }
        }

        foreach ($controller_directorys as $dir) {
            foreach (scandir($dir) as $dirstructure) {
                if (is_file($dir  . "/" . $dirstructure)) {
                    if (strstr($dirstructure,"Controller.php") != false) {
                        include_once($dir . "/" . $dirstructure);
                    }
                }

            }
        }

        $default_module = $this->getFrontController()->getDefaultModule();

        $db_structure = array();

        foreach(get_declared_classes() as $c){
            if(is_subclass_of($c, 'Zend_Controller_Action')){
                $functions = array();
                foreach (get_class_methods($c) as $f) {
                    if (strstr($f,"Action") != false) {
                        array_push($functions,substr($f,0,strpos($f,"Action")));
                    }
                }

//                $c = strtolower(substr($c,0,strpos($c,"Controller")));

                if (strstr($c,"_") != false) {
                    $db_structure[substr($c,0,strpos($c,"_"))][substr($c,strpos($c,"_") + 1)] = $functions;
                }else{
                    $db_structure[$default_module][$c] = $functions;
                }
            }
        }

        echo "<PRE>";
        foreach ($db_structure as $modulo => $controller) {
            foreach ($controller as $k => $actions) {
                $actions = $actions + array('delete', 'autocomplete', 'form');
                foreach ($actions as $action) {
                    echo strtolower("$modulo/" . ltrim(preg_replace('/([A-Z]+)/', "-$1", substr($k,0,strpos($k,"Controller"))), '-') ."/" .
                        preg_replace('/([A-Z]+)/', "-$1", $action)."\n");
                }
            }
        }

        var_dump($db_structure);

        exit();

    }
}