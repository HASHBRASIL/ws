<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 10/04/2013
 * @desc formatação dos numeros decimal se vier redondo irá mostrar sem o ponto
 *
 */
class Zend_View_Helper_FormatDecimal extends Zend_View_Helper_Abstract
{
    /**
     * @param  string $ccpf
     * @param boolean $cortar
     * @return string
     */
    public function formatDecimal($decimal)
    {
        if($decimal == "0,00"){
            return $decimal;
        }
        return number_format($decimal, 2, ',', '.');
    }




}
