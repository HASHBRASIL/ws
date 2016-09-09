<?php

    include_once "connect.php";
    include_once "UUID.php";

    $queryUser= $dbh->prepare ("SELECT id FROM tb_pessoa WHERE metanome = 'DEPMODELO'" );
    $User = $queryUser->fetchAll( PDO::FETCH_ASSOC );

    die();


    $formData = explode( '&',  $_POST['serialize']);
    $arData = array();
    foreach($formData as $value){
        $arDivisor = explode('=', $value);
        $arData[$arDivisor[0]] = urldecode( $arDivisor[1] );
    }

    $arDeselected = explode( ',', urldecode( $arData['deselected'] ) );

    $query          = $dbh->prepare( "UPDATE tb_grupo SET dt_inclusao = ( SELECT current_timestamp ), nome = :nome, publico = :publico, id_criador = :id_criador WHERE id = :id;" );
    //$query->bindParam(':nome', $valor);
    //$query->bindParam(':publico', $valor);
    //$query->bindParam(':id_criador', $valor);
    //$query->bindParam(':id, $valor);

    $queryRL_update = $dbh->prepare( "UPDATE rl_grupo_item set id_grupo = :id_grupo, id_item = :id_item where id = :id" );
    //$queryRL_update->bindParam(':id_grupo', $valor);
    //$queryRL_update->bindParam(':id_item', $valor);
    //$queryRL_update->bindParam(':id', $valor);

    $queryRL_insert = $dbh->prepare( "INSERT INTO rl_grupo_item (id, id_grupo, id_item ) VALUES (:id, :id_grupo, :id_item);" );
    //$queryRL_insert->bindParam(':id', $valor);
    //$queryRL_insert->bindParam(':id_grupo', $valor);
    //$queryRL_insert->bindParam(':id_item', $valor);

    $queryRL_delete = $dbh->prepare( "INSERT INTO rl_grupo_item WHERE id_item = ? AND id_grupo = ?; " );



    $dbh->beginTransaction();

    try {
        if( $query->execute( array( $arData['nome'], $arData['publico'], 'd5e27a49-a3e0-4a3a-9aa0-dd0226899cb5', $arData['idSelecionado'] ) )  ){
            echo "UP DATE DO GRUPO CERTO\n";
        }else {
            echo "UP DATE DO GRUPO ERRADO \n";
        }

        foreach( $arDeselected as $key => $value ){
            if( !empty( $value ) ){
                if( $queryRL_delete->execute( array( $value, $arData['idSelecionado']) ) ){
                    echo "worked o delete\n";
                }else{
                    echo "worked o delete\n";
                }
            }
        }

        if( isset( $_POST['conteudo'] ) ){
            foreach( $_POST['conteudo'] as $values ){
                if( empty( $values['id_rl'] ) ){
                    $tokenRL = UUID::v4();

                    if( $queryRL_insert->execute( array( $tokenRL, $arData['idSelecionado'], $values['id_item'] ) )){
                        echo "worked o insert\n";
                    }else{
                        echo "deu ruim o insert\n";
                    }

                }else{
                    if( $queryRL_update->execute( array( $arData['idSelecionado'], $values['id_item'], $values['id_rl'] ) ) ){
                        echo "worked o update\n";
                    }else{
                        echo "not worked o update\n";
                    }
                }
            }
        }

        $dbh->commit();
        echo "sucesso\n";

    } catch (PDOException $e) {

        $dbh->rollBack();
        // var_dump($e);
        echo "error";

    }
