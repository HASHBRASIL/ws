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


    #UPDATE QUERIES
    $queryUpdateTib         = $dbh->prepare( "UPDATE tp_itembiblioteca SET nome = :nome, descricao = :descricao, dt_criacao = ( SELECT CURRENT_TIMESTAMP ), tipo = :tipo WHERE id = :id");
    $queryUpdateVisivel     = $dbh->prepare( "UPDATE tp_itembiblioteca_metadata SET valor = :valor WHERE id_tib = :id_tib AND id_tib_pai = :id_tib_pai AND metanome = 'ws_visivel';");
    $queryUpdateOrdemLista  = $dbh->prepare( "UPDATE tp_itembiblioteca_metadata SET valor = :valor WHERE id_tib = :id_tib AND id_tib_pai = :id_tib_pai AND metanome = 'ws_ordemLista';");
    $queryUpdateOrdem       = $dbh->prepare( "UPDATE tp_itembiblioteca_metadata SET valor = :valor WHERE id_tib = :id_tib AND id_tib_pai = :id_tib_pai AND metanome = 'ws_ordem';");
    #INSERT QUERIES
    $queryInsertTib         = $dbh->prepare( "INSERT INTO tp_itembiblioteca (id, descricao, dt_criacao, nome, tipo, id_tib_pai) VALUES ( :id, :descricao, ( SELECT CURRENT_TIMESTAMP ), :nome, :tipo, :id_tib_pai )" );
    $queryInsertVisivel     = $dbh->prepare( "INSERT INTO tp_itembiblioteca_metadata (id, metanome, valor, id_tib, id_tib_pai) VALUES ( :id, 'ws_visivel', :valor, :id_tib, :id_tib_pai )" );
    $queryInsertOrdemLista  = $dbh->prepare( "INSERT INTO tp_itembiblioteca_metadata (id, metanome, valor, id_tib, id_tib_pai) VALUES ( :id, 'ws_ordemLista', :valor, :id_tib, :id_tib_pai )" );
    $queryInsertOrdem       = $dbh->prepare( "INSERT INTO tp_itembiblioteca_metadata (id, metanome, valor, id_tib, id_tib_pai) VALUES ( :id, 'ws_ordem', :valor, :id_tib, :id_tib_pai )" );
    #DELETE QUERIES
    $queryDeleteTib         = $dbh->prepare( "DELETE FROM tp_itembiblioteca WHERE id = :id");
    $queryDeleteVisivel     = $dbh->prepare( "DELETE FROM tp_itembiblioteca_metadata WHERE id_tib = :id_tib AND id_tib_pai = :id_tib_pai AND metanome = 'ws_visivel'" );
    $queryDeleteOrdemLista  = $dbh->prepare( "DELETE FROM tp_itembiblioteca_metadata WHERE id_tib = :id_tib AND id_tib_pai = :id_tib_pai AND metanome = 'ws_ordemLista'" );
    $queryDeleteOrdem       = $dbh->prepare( "DELETE FROM tp_itembiblioteca_metadata WHERE id_tib = :id_tib AND id_tib_pai = :id_tib_pai AND metanome = 'ws_ordem'" );

    $dbh->beginTransaction();

    try {
        # UPDATE!
        foreach( $array as $key => $value ){
            if( $key != 'help' ){
                $tokenTib = '';
                if( $value['new'] == 'true' ){
                    # AQUI TEM QUE FAZER O INSERT NO BANCO
                    $newToken = UUID::v4();
                    $queryInsertTib->bindParam( ':id', $newToken );
                    $queryInsertTib->bindParam( ':descricao', $value['descricao'] );
                    $queryInsertTib->bindParam( ':nome', $value['nome'] );
                    $queryInsertTib->bindParam( ':tipo', $value['tipo'] );
                    $queryInsertTib->bindParam( ':id_tib_pai', $array['help']['idMaster'] );
                    $testeRonaldo  = $queryInsertTib->execute();

                    $tokenTib = $newToken;

                }else if( $value['new'] == 'false' ){
                    # AQUI TEM QUE FAZER O UPDATE
                    $queryUpdateTib->bindParam( ':nome', $value['nome'] );
                    $queryUpdateTib->bindParam( ':descricao', $value['descricao'] );
                    $queryUpdateTib->bindParam( ':tipo', $value['tipo'] );
                    $queryUpdateTib->bindParam( ':id', $key );
                    $queryUpdateTib->execute();
                    $tokenTib      = $key;
                }

                #ORDEMLISTA
                if( !( empty( $value['wslista'] ) && !is_numeric( $value['wslista'] ) ) ){
                    if( !( empty($value['lista'] ) && !is_numeric( $value['lista'] ) ) ){
                        $queryUpdateOrdemLista->bindParam( ':valor', $value['lista'] );
                        $queryUpdateOrdemLista->bindParam( ':id_tib', $key );
                        $queryUpdateOrdemLista->bindParam( ':id_tib_pai', $array['help']['idMaster'] );
                        $queryUpdateOrdemLista->execute();
                    }else{
                        $queryDeleteOrdemLista->bindParam( ':id_tib', $key) ;
                        $queryDeleteOrdemLista->bindParam( ':id_tib_pai', $array['help']['idMaster'] );
                        $queryDeleteOrdemLista->execute();
                    }
                }else{
                    if( !( empty( $value['lista'] ) && !is_numeric( $value['lista'] ) ) ){
                        $newTokenMetadata = UUID::v4();
                        $queryInsertOrdemLista->bindParam( ':id', $newTokenMetadata );
                        $queryInsertOrdemLista->bindParam( ':valor', $value['lista'] );
                        $queryInsertOrdemLista->bindParam( ':id_tib', $tokenTib );
                        $queryInsertOrdemLista->bindParam( ':id_tib_pai', $array['help']['idMaster'] );
                        $queryInsertOrdemLista->execute();
                    }
                }

                #VISIVEL
                if( !( empty($value['wsvisivel']) && !is_numeric($value['wsvisivel'])) ){

                    if( $value['visivel'] == '1' ){
                        $queryUpdateVisivel->bindParam( ':valor', $value['visivel'] );
                        $queryUpdateVisivel->bindParam( ':id_tib', $key );
                        $queryUpdateVisivel->bindParam( ':id_tib_pai', $array['help']['idMaster'] );
                        $queryUpdateVisivel->execute();
                    }else{
                        $queryDeleteVisivel->bindParam( ':id_tib', $key );
                        $queryDeleteVisivel->bindParam( ':id_tib_pai', $array['help']['idMaster'] );
                        $queryDeleteVisivel->execute();
                    }
                }else{
                    if( $value['visivel'] == '1'){
                        $newTokenMetadata = UUID::v4();
                        $queryInsertVisivel->bindParam( ':id', $newTokenMetadata );
                        $queryInsertVisivel->bindParam( ':valor', $value['visivel'] );
                        $queryInsertVisivel->bindParam( ':id_tib', $tokenTib );
                        $queryInsertVisivel->bindParam( ':id_tib_pai', $array['help']['idMaster'] );
                        $queryInsertVisivel->execute();
                    }
                }

                #ORDEM
                //var_dump($value['wsordem'] != '-1');
                if( $value['wsordem'] != '-1' ){
                    // if( !( empty($value['ordem'] ) && !is_numeric($value['ordem']) ) ){
                        var_dump($value);
                        $queryUpdateOrdem->bindParam( ':valor', $value['ordem'] );
                        $queryUpdateOrdem->bindParam( ':id_tib', $key );
                        $queryUpdateOrdem->bindParam( ':id_tib_pai', $array['help']['idMaster'] );
                        $queryUpdateOrdem->execute();
                    // }
                }else{
                    // if( !( empty( $value['ordem'] ) && !is_numeric( $value['ordem'] ) ) ){
                        $newTokenMetadata = UUID::v4();
                        $queryInsertOrdem->bindParam( ':id', $newTokenMetadata );
                        $queryInsertOrdem->bindParam( ':valor', $value['ordem'] );
                        $queryInsertOrdem->bindParam( ':id_tib', $tokenTib );
                        $queryInsertOrdem->bindParam( ':id_tib_pai', $array['help']['idMaster'] );
                        $queryInsertOrdem->execute();
                    // }
                }
            }else if($key == 'help'){
                # AQUI
                if( !empty( $value['deleted'] ) ){
                    $arDeleted = explode( ',', $value['deleted'] );

                    foreach( $arDeleted as $chave => $valor ){
                        $queryDeleteVisivel->bindParam( ':id_tib', $valor );
                        $queryDeleteVisivel->bindParam( ':id_tib_pai', $array['help']['idMaster'] );
                        $queryDeleteVisivel->execute();

                        $queryDeleteOrdem->bindParam( ':id_tib', $valor );
                        $queryDeleteOrdem->bindParam( ':id_tib_pai', $array['help']['idMaster'] );
                        $queryDeleteOrdem->execute();

                        $queryDeleteOrdemLista->bindParam( ':id_tib', $valor );
                        $queryDeleteOrdemLista->bindParam( ':id_tib_pai', $array['help']['idMaster'] );
                        $queryDeleteOrdemLista->execute();

                        $queryDeleteTib->bindParam( ':id', $valor );
                        $queryDeleteTib->execute();
                    }
                }
            }
        }

        $dbh->commit();

    } catch (PDOException $e) {

        $dbh->rollBack();
        // var_dump($e);
        echo "error";
    }
