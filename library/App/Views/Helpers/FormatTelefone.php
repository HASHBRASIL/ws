<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 10/04/2013
 * @desc formatação dos numeros decimal se vier redondo irá mostrar sem o ponto
 *
 */
class Zend_View_Helper_FormatTelefone extends Zend_View_Helper_Abstract
{
    /**
     * @param int|string $telefone
     * @return string
     */
    public function formatTelefone($telefone)
    {
        if(empty($telefone)){
        	return null;
        }
        if(strpos($telefone, '0') === 0){
        	$primeiraParte 	= substr( $telefone, 0, 4 );
        	$segundaParte	= substr( $telefone, 4, 3 );
        	$terceiraParte	= substr( $telefone, 7, 4 );
        	return $primeiraParte.' '.$segundaParte.' '.$terceiraParte;
        }
        switch (strlen($telefone) ){
        	case 10:
        		$codigoCidade 	= substr( $telefone, 0, 2 );
        		$primeiraParte	= substr( $telefone, 2, 4 );
        		$segundaParte	= substr( $telefone, 6, 4 );
        		return '('.$codigoCidade.')'.$primeiraParte.'-'.$segundaParte;
        		break;
        	case 11:
        		$codigoCidade 	= substr( $telefone, 0, 2 );
        		$primeiraParte	= substr( $telefone, 2, 5 );
        		$segundaParte	= substr( $telefone, 7, 4 );
        		return '('.$codigoCidade.')'.$primeiraParte.'-'.$segundaParte;
        		break;
        	case 12:
        		$codigoPais		= substr( $telefone, 0, 2 );
        		$codigoCidade 	= substr( $telefone, 2, 2 );
        		$primeiraParte	= substr( $telefone, 4, 4 );
        		$segundaParte	= substr( $telefone, 8, 4 );
        		return $codigoPais.'('.$codigoCidade.')'.$primeiraParte.'-'.$segundaParte;
        		break;
        	case 13:
        		$codigoPais		= substr( $telefone, 0, 2 );
        		$codigoCidade 	= substr( $telefone, 2, 2 );
        		$primeiraParte	= substr( $telefone, 4, 5 );
        		$segundaParte	= substr( $telefone, 9, 4 );
        		return $codigoPais.'('.$codigoCidade.')'.$primeiraParte.'-'.$segundaParte;
        		break;
        	default:
        		$codigoCidade 	= substr( $telefone, 0, 2 );
        		$primeiraParte	= substr( $telefone, 2, 4 );
        		$segundaParte	= substr( $telefone, 6, 4 );
        		return '('.$codigoCidade.')'.$primeiraParte.'-'.$segundaParte;
        		break;
        }
    }
}
