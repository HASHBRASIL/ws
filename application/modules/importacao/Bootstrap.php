<?php
class Importacao_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /*
    private function bloqueio() {
        if ($this->_moduleName != 'Importacao') {
            return true;
        } else {
            return false;
        }
    }

    protected function _initAutoloader() {
        if($this->bloqueio()){return true;}
        Zend_Loader_Autoloader::getInstance ()->setFallbackAutoloader ( true );
    }
    
    protected function _initImportador() {
        if($this->bloqueio()){return true;}
        /*
        $caminho = getcwd().'../application/general/helpers/Controller';
        Zend_Loader::loadFile($caminho.'Importador.php', $caminho, true);
        * /
        $importador = new Controller_Importador();
        Zend_Controller_Action_HelperBroker::addHelper ( $importador );
    }
 /*
   
    public function _initModuleRegistry(){

            $registry = App_Module_Registry::getInstance();
            $registry->setModuleName('default', 'Default');

    }
  
    protected function _initRoutine() {
        $routine = new Controller_Routine();
        Zend_Controller_Action_HelperBroker::addHelper ($routine);
    }*/
}