<?php

class Zend_View_Helper_Cpf extends Zend_View_Helper_Abstract
{
    /**
     * @param  string $cpf
     * @return string
     */
    public function cpf($cpf)
    {
        if(strlen($cpf) > 11){
            $primeiraParte = substr( $cpf, 0, 2 );
            $segundaParte = substr( $cpf, 2, 3 );
            $terceiraParte = substr( $cpf, 5, 3 );
            $divisor = substr( $cpf, 8, 4 );
            $identificador = substr( $cpf, 12, 2 );

            return "$primeiraParte.$segundaParte.$terceiraParte/$divisor-$identificador";
        }else if(!empty($cpf)) {
            $primeiraParte = substr( $cpf, 0, 3 );
            $segundaParte = substr( $cpf, 3, 3 );
            $terceiraParte = substr( $cpf, 6, 3 );
            $identificador = substr( $cpf, 9, 2 );

            return "$primeiraParte.$segundaParte.$terceiraParte-$identificador";
        }
        return "";
    }
}
