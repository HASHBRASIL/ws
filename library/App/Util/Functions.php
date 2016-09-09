<?php
class App_Util_Functions
{
    static function geraMd5Aleatorio()
    {
        $x = mt_rand() * 30 * 30 -30;
        return md5 ($x);
    }

    /**
     * @description Exibe informações relacionadas a expressão. Se o segundo
     * parametro for TRUE a execução e interrompida
     * @param mix $mixExpression
     * @param boolean $dump
     * @param boolean $boolExit
     * @return void
     */
    static function debug($mixExpression, $dump = false, $boolExit = true)
    {
        $arrBacktrace = debug_backtrace();
        $strMessage = "<fieldset><legend><font color=\"#007000\">functions</font></legend><pre>" ;
        foreach ( $arrBacktrace[ 0 ] as $strAttribute => $mixValue )
        {
            if ( ( $strAttribute != "class" ) && ( $strAttribute != "object" ) && ( $strAttribute != "args" ) )
            {
                if ( $strAttribute == "type" )
                {
                    $strMessage .= "<b>" . $strAttribute . "</b> ". gettype( $mixExpression ) ."\n";
                }
                else
                {
                    $strMessage .= "<b>" . $strAttribute . "</b> ". $mixValue ."\n";
                }
            }
        }
        $strMessage .= "<hr />";
        ob_start();
        if ($dump) {
            var_dump( $mixExpression );
        }else{
            print_r($mixExpression);
        }
        $strMessage .= ob_get_clean();
        $strMessage .= "</pre></fieldset>";
        print $strMessage;
        if ( $boolExit )
        {
            print "<br /><font color=\"#700000\" size=\"4\"><b>D I E</b></font>";
            die();
        }
    }
}
