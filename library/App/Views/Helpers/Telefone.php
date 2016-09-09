<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  03/05/2013
 */
class Zend_View_Helper_Telefone extends Zend_View_Helper_Abstract
{
    /**
     * @param  string $cep
     * @return string
     */
    public function telefone($telefone)
    {
        if($telefone == null){
            return null;
        }
        if(strlen($telefone) == 10){
            $ddd             = substr( $telefone, 0, 2 );
            $primeiraParte   = substr( $telefone, 2, 4 );
            $segundaParte    = substr( $telefone, 6, 4 );

            return "(".$ddd.")".$primeiraParte."-".$segundaParte;

        }else if (strlen($telefone) == 11){
            $ddd             = substr( $telefone, 0, 2 );
            $primeiraParte   = substr( $telefone, 2, 5 );
            $segundaParte    = substr( $telefone, 7, 4 );

            return "(".$ddd.")".$primeiraParte."-".$segundaParte;

        }else if (strlen($telefone) == 12){
            $codigo         = substr( $telefone, 0, 2 );
            $ddd            = substr( $telefone, 2, 2 );
            $primeiraParte  = substr( $telefone, 4, 4 );
            $segundaParte   = substr( $telefone, 8, 4 );

            return "+".$codigo."(".$ddd.")".$primeiraParte."-".$segundaParte;

        }else if (strlen($telefone) == 13){
            $codigo         = substr( $telefone, 0, 2 );
            $ddd            = substr( $telefone, 2, 2 );
            $primeiraParte  = substr( $telefone, 5, 4 );
            $segundaParte   = substr( $telefone, 9, 4 );

            return "+".$codigo."(".$ddd.")".$primeiraParte."-".$segundaParte;
        }
    }
}