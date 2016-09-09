<?php

class Zend_View_Helper_MenuTree extends Zend_View_Helper_FormElement
{

    private $_moduleName;
    private $_controllerName;
    private $_actionName;
    private $identity;

    public function __construct() {
        $request                   = Zend_Controller_Front::getInstance()->getRequest();
        $this->identity            = Zend_Auth::getInstance()->getIdentity();
        $this->_moduleName         = $request->getModuleName();
        $this->_controllerName     = $request->getControllerName();
        $this->_actionName         = $request->getActionName();
        $this->_servicos           = $this->identity->servicos;
        $this->_permissionGrupo    = $this->identity->permission[$this->identity->time['id']];
    }

    public function menuTree($arvore, $horizontal = false) {

        $html = "";

        if($arvore){
            foreach($arvore as $key => $menu){
                if (($this->_servicos[$key]['visivel']) && ($this->view->isAllowed($key))) {

                    $href = "home.php?servico=".$key;

                    $icon = isset($this->_servicos[$key]['ws_icon']) ? $this->_servicos[$key]['ws_icon'] : 'icon-cog';

                    if ( $arvore->hasChildren() ) {
                        $filho = $this->menuTree($arvore->getChildren());

                        if ($filho) {

                            $html .= "<li class='has-sub'>";
                            //$html .= "<a href='{$href}'>";
                            $html .= "<span class='sub-menu-label'>";
                            // $html .= "<i class='{$icon}'></i>";
                            $html .= $this->_servicos[$key]['nome'];
                            //$html .= "<i class='fa fa-angle-right'></i>";
                            if(!empty($this->servicos[$key]['metanome'])){
                                $html.="<i class='fa fa-question-circle' aria-hidden='true' title='" .   Zend_Registry::get('Zend_Translate')->translate($this->servicos[$key]['metanome']) . "'></i>";
                            }
                            $html .= "</span>";
                            //$html .= "</a>";
                            $html .= "<nav class='sub-menu-hash'>";
                            $html .= "<ul>";
                            $html .= $filho;
                            $html .= "</ul>";
                            $html .= "</nav>";
                            $html .= "</li>";

                        } else {

                            $active = $horizontal == false ? '' : 'opened';

                            // @todo colocar class="active" para quando for ativo
                            $html .= "<li $active>";
                            $html .= "<span>";
                            $html .= "<a href='{$href}'>";
                            // $html .= "<i class='{$icon}'></i>";
                            $html .= $this->_servicos[$key]['nome']; 
                            $html .= "</a>";
                            if(!empty($this->_servicos[$key]['metanome'])){
                                $html.="<i class='fa fa-question-circle' aria-hidden='true' title='" .  Zend_Registry::get('Zend_Translate')->translate($this->_servicos[$key]['metanome']) . "'></i>";
                            }
                            $html .= "</span>";
                            $html .= "</li>";

                        }
                    }
                }
            }
        }
        return $html;
    }
}
