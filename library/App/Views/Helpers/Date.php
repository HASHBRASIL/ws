<?php

class Zend_View_Helper_Date extends Zend_View_Helper_Abstract
{
    /**
     * @param  string $date
     * @param  string $format
     * @param  string $default
     * @return string
     */
    public function date($date, $string = 'dd/MM/yyyy')
    {
    if(is_array($string)){
        $string = 'dd/MM/yyyy';
    }
        if($date){
            $date = new Zend_Date($date);
            return $date->toString($string);
        }
        return null;
    }
}
