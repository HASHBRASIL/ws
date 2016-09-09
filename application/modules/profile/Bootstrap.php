<?php
class Profile_Bootstrap extends Zend_Application_Module_Bootstrap
{
	public function _initModuleRegistry(){
	
		$registry = App_Module_Registry::getInstance();
		$registry->setModuleName('profile', 'Gestão de Perfis do Sistema');
	
	}
}