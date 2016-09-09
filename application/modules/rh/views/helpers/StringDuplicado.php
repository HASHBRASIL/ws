<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  03/06/2014
 */
class Zend_View_Helper_StringDuplicado extends Zend_View_Helper_FormElement
{

    /**
     * @param string $baseUrl
     * @param array $params
     */
    public function stringDuplicado($duplicado)
    {
        switch ($duplicado){
            case Rh_Model_Bo_DadosPonto::DUPLICADO:
                return "Duplicado";
                break;
            case Rh_Model_Bo_DadosPonto::DUPLICADO_APROVADO:
                return "Justificado";
                break;
            default:
                return "Não duplicado";
        }
    }
}