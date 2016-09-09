<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  17/04/2013
 */
class Zend_View_Helper_Params extends Zend_View_Helper_FormElement
{

    /**
     * @param string $baseUrl
     * @param array $params
     */
    public function params($baseUrl, $params)
    {
        $param = "";
        if(!empty($params)){
            foreach ($params as $name => $value){
                if(!empty($name) && !empty($value)){
                    $param .= "/".$name."/".$value;
                }
            }
        }
        return $this->view->baseUrl($baseUrl.$param);
    }
}