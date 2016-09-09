<?php

class Zend_View_Helper_Real extends Zend_View_Helper_Abstract
{
    /**
     * @param  string $cep
     * @return string
     */
    public function real($decimal , $simbolo = true)
    {
        if($simbolo){
            return 'R$ ' . number_format($decimal, 2, ',', '.');
        }
        return number_format($decimal, 2, ',', '.');
    }
}