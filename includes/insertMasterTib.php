<?php

    require_once "connect.php";
    include_once "UUID.php";

    $array        = array();
    $arrayDeleted = array();

    foreach( $_POST as $key => $value ){
        $str = explode('_', $key);
        $array[$str[0]][$str['1']] = $value;
    }

    // echo "<pre>";
    // var_dump($array);
    // die();

    #INSERT QUERIES
    $queryInsertTibMaster   = $dbh->prepare( "INSERT INTO tp_itembiblioteca (id, descricao, dt_criacao, nome, tipo, id_heranca) VALUES ( :id, :descricao, ( SELECT CURRENT_TIMESTAMP ), :nome, 'Master', :id_heranca)" );
    $queryInsertTib         = $dbh->prepare( "INSERT INTO tp_itembiblioteca (id, descricao, dt_criacao, nome, tipo, id_tib_pai) VALUES ( :id, :descricao, ( SELECT CURRENT_TIMESTAMP ), :nome, :tipo, :id_tib_pai )" );
    $queryInsertVisivel     = $dbh->prepare( "INSERT INTO tp_itembiblioteca_metadata (id, metanome, valor, id_tib, id_tib_pai)  VALUES ( :id, 'ws_visivel', :valor, :id_tib, :id_tib_pai )" );
    $queryInsertOrdemLista  = $dbh->prepare( "INSERT INTO tp_itembiblioteca_metadata (id, metanome, valor, id_tib, id_tib_pai)  VALUES ( :id, 'ws_ordemLista', :valor, :id_tib, :id_tib_pai )" );
    $queryInsertOrdem       = $dbh->prepare( "INSERT INTO tp_itembiblioteca_metadata (id, metanome, valor, id_tib, id_tib_pai)  VALUES ( :id, 'ws_ordem', :valor, :id_tib, :id_tib_pai )" );


    $dbh->beginTransaction();

    try {
        $tokenNewMaster = UUID::v4();

        $queryInsertTibMaster->bindParam(':id', $tokenNewMaster);
        $queryInsertTibMaster->bindParam(':descricao', $array['tibMaster']['descricao']);
        $queryInsertTibMaster->bindParam(':nome', $array['tibMaster']['nome']);
        $queryInsertTibMaster->bindParam(':id_heranca', $array['heranca']['id']);
        $queryInsertTibMaster->execute();

        foreach( $array as $key => $value ){

            if($key != 'tibMaster' && $key != 'heranca'){
                // var_dump($value);
                #SE FOR UMA NOVA TIB
                if($value['new'] == 'true'){

                    $tokenNewTib = UUID::v4();
                    $queryInsertTib->bindParam(':id', $tokenNewTib);
                    $queryInsertTib->bindParam(':descricao', $value['descricao'] );
                    $queryInsertTib->bindParam(':nome', $value['nome']);
                    $queryInsertTib->bindParam(':tipo', $value['tipo']);
                    $queryInsertTib->bindParam(':id_tib_pai', $tokenNewMaster);
                    $queryInsertTib->execute();

                    $chave = $tokenNewTib;
                }else{
                    $chave = $key;
                }

                #LISTA
                if( !( empty( $value['lista'] ) && !is_numeric( $value['lista'] ) ) ){
                    $newTokenMetadata = UUID::v4();
                    $queryInsertOrdemLista->bindParam( ':id', $newTokenMetadata );
                    $queryInsertOrdemLista->bindParam( ':valor', $value['lista'] );
                    $queryInsertOrdemLista->bindParam( ':id_tib', $chave );
                    $queryInsertOrdemLista->bindParam( ':id_tib_pai', $tokenNewMaster );
                    $queryInsertOrdemLista->execute();
                }

                #ORDEM
                $newTokenMetadata = UUID::v4();
                $queryInsertOrdem->bindParam( ':id', $newTokenMetadata );
                $queryInsertOrdem->bindParam( ':valor', $value['ordem'] );
                $queryInsertOrdem->bindParam( ':id_tib', $chave );
                $queryInsertOrdem->bindParam( ':id_tib_pai', $tokenNewMaster );
                $queryInsertOrdem->execute();

                #VISIVEL
                if( $value['visivel'] == '1'){
                    $newTokenMetadata = UUID::v4();
                    $queryInsertVisivel->bindParam( ':id', $newTokenMetadata );
                    $queryInsertVisivel->bindParam( ':valor', $value['visivel'] );
                    $queryInsertVisivel->bindParam( ':id_tib', $chave );
                    $queryInsertVisivel->bindParam( ':id_tib_pai', $tokenNewMaster );
                    $queryInsertVisivel->execute();
                }

            }
        }

        $dbh->commit();
        echo 'FOI SEM ERRO';

    } catch (PDOException $e) {

        $dbh->rollBack();
        // var_dump($e);
        echo "error";
    }
