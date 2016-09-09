<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 10/04/2013
 * @desc formatação dos numeros decimal para cortar se vier com zero
 *
 */
class Zend_View_Helper_CortarDecimal extends Zend_View_Helper_Abstract
{
    /**
     * @param  string $ccpf
     * @return string
     */
    public function cortarDecimal($decimal)
    {
        if(substr_count($decimal, ".")){
            $cortaDecimal = explode('.', $decimal);
            if($cortaDecimal[1] == 0){
                return $cortaDecimal[0];
            }
        }
        return number_format($decimal, 2, ',', '.');;
    }




}
