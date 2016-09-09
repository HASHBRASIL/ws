<?php
class Auth_Bootstrap extends Zend_Application_Module_Bootstrap
{
	public function _initModuleRegistry(){
	
		$registry = App_Module_Registry::getInstance();
		$registry->setModuleName('auth', 'Gestão de Autenticações e Menus');
	
	}
}