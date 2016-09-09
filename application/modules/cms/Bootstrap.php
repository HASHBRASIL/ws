<?php
class Cms_Bootstrap extends Zend_Application_Module_Bootstrap
{
	public function _initModuleRegistry(){

		$registry = App_Module_Registry::getInstance();
		$registry->setModuleName('cms', 'Gerenciamento de CMS');

	}
}