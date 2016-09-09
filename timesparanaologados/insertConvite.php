<?php
    session_start();
    require_once "databaseconnect.php";
    require_once "classes/UUID.php";
    require_once "funcoes.php";

    $idTime = ( isset( $_SESSION['time'] ) && !empty( $_SESSION['time']['id'] ) ) ? $_SESSION['time']['id'] : false;
    if( $idTime )
    {
        if( isset( $_POST['convite'] ) )
        {
            $convites = array_filter( $_POST['convite'] );
            $existentes = array();
            $convidados = array();

            for( $i=0; $i < count( $convites ); $i++ )
            {
                $retorno = verificaEmailPorValor( $dbh, $convites[$i] );
                if( $retorno == 0 )
                {
                    array_push( $convidados, $convites[$i] );
                }
                else
                {
                    array_push( $existentes, $retorno );
                }
            }

            if( !empty( $existentes ) ){
                foreach ( $existentes as $existente ) {
                    for( $i=0; $i < count( $existente ); $i++ )
                    {
                        $id = UUID::v4();
                        $id_grupo = $idTime;
                        $id_pessoa = $existente[$i];
                        try
                        {
                            $dbh->beginTransaction();
                            $rlgrupopessoa = criaRLGrupoPessoa( $dbh, $id, $id_grupo, $id_pessoa );
                            $dbh->commit();
                            var_dump( $rlgrupopessoa );

                        }
                        catch (PDOException $e)
                        {
                            echo $e->getMessage();
                            //var_dump( $e );
                            //$dbh->rollBack();
                        }

                        $idConvite = $id;
                        try
                        {
                            $dbh->beginTransaction();
                            $convite = criaConvite( $dbh, $idConvite );
                            $dbh->commit();
                            var_dump( $convite );
                        }
                        catch (PDOException $e)
                        {
                            echo $e->getMessage();
                            //var_dump( $e );
                            //$dbh->rollBack();
                        }
                    }
                }

            }

            if( !empty( $convidados ) )
            {
                for( $i=0; $i < count( $convidados ); $i++ )
                {
                    $idPessoa = UUID::v4();
                    $nomePessoa = explode( "@", $convidados[$i] )[0];
                    try
                    {
                        $dbh->beginTransaction();
                        $pessoa = criaPessoa( $dbh, $idPessoa, $nomePessoa );
                        $dbh->commit();
                    }
                    catch (PDOException $e)
                    {
                        echo $e->getMessage();
                        //var_dump( $e );
                        //$dbh->rollBack();
                    }

                    $id = UUID::v4();
                    $id_grupo = $idTime;
                    $id_pessoa = $idPessoa;
                    try
                    {
                        $dbh->beginTransaction();
                        $rlgrupopessoa = criaRLGrupoPessoa( $dbh, $id, $id_grupo, $id_pessoa );
                        $dbh->commit();
                        //var_dump( $rlgrupopessoa );

                    }
                    catch (PDOException $e)
                    {
                        echo $e->getMessage();
                        //var_dump( $e );
                        //$dbh->rollBack();
                    }

                    $idConvite = $id;
                    try
                    {
                        $dbh->beginTransaction();
                        $convite = criaConvite( $dbh, $idConvite );
                        $dbh->commit();
                        //var_dump( $convite );
                    }
                    catch (PDOException $e)
                    {
                        echo $e->getMessage();
                        //var_dump( $e );
                        //$dbh->rollBack();
                    }
                }
            }
        }
    }
    else
    {
        echo "<p>Ops aconteceu algum problema, <a href='/'>tente novamente</a></p>";
    }