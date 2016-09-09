<?php
class Pcp_Bootstrap extends Zend_Application_Module_Bootstrap
{
	public function _initModuleRegistry(){
	
		$registry = App_Module_Registry::getInstance();
		$registry->setModuleName('pcp', 'Gestão de planejamento, controle e produção');
	
	}
}