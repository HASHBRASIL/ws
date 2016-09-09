<?php
    require_once "connect.php";

    $updateIB = $dbh->prepare( "UPDATE tb_itembiblioteca SET valor = :value, dt_criacao = (select current_timestamp) WHERE id = :id_ib " );

    $dbh->beginTransaction();

    try {

        foreach ( $_POST['data'] as $value ) {

            $updateIB->bindParam( ':value', $value['value'] );
            $updateIB->bindParam( ':id_ib', $value['idib'] );
            $updateIB->execute();

            // echo"
            //  ##################\n
            //  idbi =  {$value['idib']}\n
            //  name =  {$value['name']}\n
            //  valor = {$value['value']}\n
            //  ##################\n\n
            // ";
        }
        $dbh->commit();
        echo "sucesso";

    } catch ( PDOException $e ) {
        echo "error";
        $dbh->rollBack();
        var_dump( $e );
    }
