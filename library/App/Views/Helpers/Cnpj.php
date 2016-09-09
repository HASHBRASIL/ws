<?php

class App_View_Helper_Cnpj extends Zend_View_Helper_Abstract
{
    /**
     * @param  string $cnpj
     * @return string
     */
    public function cnpj($cnpj)
    {
        $primeiraParte = substr( $cnpj, 0, 2 );
        $segundaParte = substr( $cnpj, 2, 3 );
        $terceiraParte = substr( $cnpj, 5, 3 );
        $divisor = substr( $cnpj, 8, 4 );
        $identificador = substr( $cnpj, 12, 2 );

        return "$primeiraParte.$segundaParte.$terceiraParte/$divisor-$identificador";
    }
}
