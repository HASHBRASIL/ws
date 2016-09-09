<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  25/07/2014
 */
class Zend_View_Helper_BooleanTranslate extends Zend_View_Helper_FormElement
{

    /**
     * @param string $baseUrl
     * @param array $params
     */
    public function booleanTranslate($boolean)
    {
        switch ($boolean){
            case 0:
                return "Não";
                break;
            case 1:
                return "Sim";
                break;
            default:
                return "Não";
        }
    }
}