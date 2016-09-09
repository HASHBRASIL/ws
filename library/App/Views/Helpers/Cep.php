<?php

class Zend_View_Helper_Cep extends Zend_View_Helper_Abstract
{
    /**
     * @param  string $cep
     * @return string
     */
    public function cep($cep)
    {
        $primeiraParte = substr( $cep, 0, 5 );
        $segundaParte = substr( $cep, 5, 3 );
        return "$primeiraParte-$segundaParte";
    }
}