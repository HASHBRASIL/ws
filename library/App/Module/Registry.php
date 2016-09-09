<?php
class App_Module_Registry
{
    private $moduleList = null;
    private static $_instance = null;
    private $moduleId = null;

    public static function getInstance()
    {
        if(self::$_instance == null){
            self::$_instance = new App_Module_Registry();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        $this->moduleList = array();
        $this->moduleId   = array();
    }

    public function setModuleName($moduleIndex, $moduleName)
    {
        $this->moduleList[$moduleIndex] = $moduleName;
    }

    public function getModuleList()
    {
        return $this->moduleList;
    }

    public function setModuleId($moduleName, $moduleId)
    {
        $this->moduleId[$moduleName] = $moduleId;
    }

    public function getModuleIdList()
    {
        return $this->moduleId;
    }

    public function getModuleId($moduleName)
    {
        return isset($this->moduleId[$moduleName]) ? $this->moduleId[$moduleName] : null;
    }


}