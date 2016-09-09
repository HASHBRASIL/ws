<?php

require_once "connect.php";
require_once "UUID.php";

// echo "<pre>";
// var_dump($_POST);
//  die();

$tokenIDMaster = UUID::v4();

$query      = $dbh->prepare( "INSERT INTO cms_estrutura VALUES ( :idEstrutura, :id_grupo, :id_grupoarea, :id_site)" );
$querybox   = $dbh->prepare( "INSERT INTO cms_box VALUES ( :idBox, :coluna, :linha, :ordem, :param, :id_estrutura, :id_tpbox)" );

$dbh->beginTransaction();



    try {

        $query->bindParam( ':idEstrutura',  $tokenIDMaster );
        $query->bindParam( ':id_grupo',     $_POST['id_grupo'] );
        $query->bindParam( ':id_grupoarea', $_POST['id_grupoarea'] );
        $query->bindParam( ':id_site',      $_POST['id_site'] );

        $executeQuery = $query->execute();

        if( $executeQuery ){
            echo "Salvo\n";
        }else {
            echo "Error!\n";
        }

        foreach($_POST['data'] as $key => $value){

            $tokenIDMasterbox = UUID::v4();

            $querybox->bindParam( ':idBox',        $tokenIDMasterbox );
            $querybox->bindParam( ':coluna',       $_POST['coluna'] );
            $querybox->bindParam( ':linha',        $_POST['linha'] );
            $querybox->bindParam( ':ordem',        $_POST['ordem'] );
            $querybox->bindParam( ':param',        $_POST['param'] );
            $querybox->bindParam( ':id_estrutura', $tokenIDMaster );
            $querybox->bindParam( ':id_tpbox',     $_POST['id_tpbox'] );

            $executeQueryBox = $querybox->execute();

            if( $executeQueryBox ){
                echo "Deu bom!\n";
            }else {
                echo "Deu ruim!\n";
            }
        }

        $dbh->commit();
        echo "sucesso\n";

    } catch (PDOException $e) {
        echo 'error';
        $dbh->rollBack();
        // var_dump($e);
    }
