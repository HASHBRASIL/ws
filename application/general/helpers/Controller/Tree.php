<?php

class Controller_Tree extends Zend_Controller_Action_Helper_Abstract {

    public function __construct() {
        $this->pluginLoader = new Zend_Loader_PluginLoader ();
    }


    public function generate($array)
    {
        $tree = array();
        foreach ($parent as $k=>$l){
            if(isset($list[$l['id']])){
                $l['children'] = createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        } 
        return $tree;
    }
}