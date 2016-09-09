<?php

    require_once "connect.php";
    require_once "UUID.php";

    // var_dump($_POST);
    // die();

    $query          = $dbh->prepare( "INSERT INTO tb_itembiblioteca ( id, dtype, dt_criacao, id_tib ) VALUES ( :tokenIDMaster, 'TbItemBiblioteca', ( SELECT current_timestamp ), :idMaster );" );
    $queryDuplicate = $dbh->prepare( "INSERT INTO rl_vinculo_item   ( id, id_ib_principal, id_ib_vinculado) VALUES (:tokenVinculo, :idData, :tokenIDMaster)");
    $queryInsert    = $dbh->prepare( "INSERT INTO tb_itembiblioteca ( id, dtype, dt_criacao, valor, id_ib_pai, id_tib ) VALUES ( :token, 'TbItemBiblioteca', ( SELECT current_timestamp ), :value, :tokenIDMaster, :key );" );


    $query->bindParam( ":tokenIDMaster", $tokenIDMaster );
    $query->bindParam( ":idMaster", $_POST['idMaster'] );

    $dbh->beginTransaction();

    $tokenIDMaster = UUID::v4();

    try {

        if( $query->execute() ){
            foreach( $_POST as $key => $value ){
                if( ( $key != 'idMaster' ) && ( $key != 'idData') ){
                    $token = UUID::v4();

                    $queryInsert->bindParam( ':token', $token );
                    $queryInsert->bindParam( ':value', $value );
                    $queryInsert->bindParam( ':tokenIDMaster', $tokenIDMaster );
                    $queryInsert->bindParam( ':key', $key );

                    if( $queryInsert->execute() ){
                        echo "funfou\n";
                    }else {
                        echo "error filhos";
                        //return;
                        break;
                    }
                }
            }

            $tokenVinculo = UUID::v4();

            $queryDuplicate->bindParam( ":tokenVinculo", $tokenVinculo );
            $queryDuplicate->bindParam( ":idData", $_POST['idData'] );
            $queryDuplicate->bindParam( ":tokenIDMaster", $tokenIDMaster );

            if( $queryDuplicate->execute() ){
                echo "CERTO criar COPIA";
                // var_dump( $dbh->errorInfo() );
                //return;
            }else{
                echo "error criar COPIA";
                // var_dump( $dbh->errorInfo() );
                //return;
            }

        }else {
            echo "error criar pai";
            // var_dump( $dbh->errorInfo() );
            //return;
        }
        // $dbh->commit();

    } catch ( PDOException $e ) {
        $dbh->rollBack();
        // var_dump( $e->getMessage() );
        echo "error";
    }

?>
