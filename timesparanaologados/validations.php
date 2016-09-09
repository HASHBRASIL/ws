<?php

    session_start();
    require_once "funcoes.php";

    if( isset( $_POST['enviar'] ) )
    {
        switch( $_POST['enviar'] ){
            case $_POST['enviar'] == 'novoTime-email':
                $informacoesEmail = verificaEmailPorValor( $dbh, $_POST['email'] );
                $informacaoPessoa = verificaPessoaPorIdTbInformacao( $dbh, $informacoesEmail );

                if( $informacaoPessoa < 1 )
                {
                    $_SESSION['time']['email'] = $_POST['email'];
                    echo "time.php";
                }
                else { echo 0; }
                break;

            case $_POST['enviar'] == 'novoTime-nometime':
                $_SESSION['time']['time'] = $_POST['nome'];
                header("Location: alias.php");
                break;

            case $_POST['enviar'] == 'novoTime-alias':

                $alias = verificaAlias($dbh, $_POST['timealias']);
                if( $alias == 0 )
                {
                    $_SESSION['time']['aliastime'] = $_POST['timealias'];
                    echo "usuario.php";
                }
                else{ echo 0; }
                break;

            case $_POST['enviar'] == 'novoTime-nome':

                $usuario = verificaUsuario($dbh, strtolower($_POST['nomeusuario']));
                if( $usuario == 0 )
                {
                    $_SESSION['time']['nome'] = $_POST['nomeusuario'];
                    echo "confirmacao.php";
                }
                else{ echo 0; }
                break;

            default:
                echo "Ocorreu algum erro, tente novamente.";
        }
    }
