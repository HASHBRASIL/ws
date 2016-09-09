<?php
    require_once 'connect.php';
    require_once 'UUID.php';

    // var_dump( $_POST );
    // die();

    if ( $_POST['action'] == 'insert' ) {
        $query = $dbh->prepare( "INSERT INTO tb_servico_metadata (id, metanome, valor, id_servico) VALUES ( :id, 'ws_descricao', :valor, :idservico )" );
        $dbh->beginTransaction();
        try {

            $tokenID = UUID::v4();
            $query->bindParam( ':id',        $tokenID );
            $query->bindParam( ':valor',     $_POST['dadosEditor'] );
            $query->bindParam( ':idservico', $_POST['id_servico'] );
            $query->execute();

            $dbh->commit();
            echo "Sucesso\n";
        } catch ( PDOException $e ) {
            $dbh->rollBack;
            echo "error";
            var_dump( $e->getMessage() );
        }
        exit;
    }

    if ( $_POST['action'] == 'update' ) {
        $query = $dbh->prepare( "UPDATE tb_servico_metadata SET valor = :valor WHERE metanome = 'ws_descricao' AND id_servico = :idservico" );
        $dbh->beginTransaction();
        try {
            $query->bindParam( ':valor',     $_POST['dadosEditor'] );
            $query->bindParam( ':idservico', $_POST['id_servico'] );
            $query->execute();

            $dbh->commit();
            echo "Sucesso\n";
        } catch ( PDOException $e ) {
            $dbh->rollBack;
            echo "error";
            var_dump( $e->getMessage() );
        }
        exit;
    }
