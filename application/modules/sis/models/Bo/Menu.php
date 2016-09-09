<?php
class Sis_Model_Bo_Menu extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_Menu
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_Menu();
        parent::__construct();
    }

    public function loadMenu($idModulo)
    {
        $menuSession         = new Zend_Session_Namespace('SisMenu');
        if(!empty($idModulo)){
            if($menuSession->currentModuleId != $idModulo){
                $menu                = $this->_dao->getMenuByModulo($idModulo);
                $menuSession->menu   = $menu;
                $menuSession->currentModuleId = $idModulo;
            }

        }
        if(empty($menuSession->moduleList) ){
            $menuSession->moduleList = App_Module_Registry::getInstance()->getModuleList();
        }

    }
}