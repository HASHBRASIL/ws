<?php
    include "connect.php";
    $grupo = $_POST['rowid'];

    $querySelectRL = $dbh->prepare(
        "SELECT
            RL.id,
            RL.id_grupo,
            RL.id_item,
            IB.id_tib
        FROM rl_grupo_item AS RL LEFT OUTER JOIN
            tb_itembiblioteca AS IB
        ON ( RL.id_item = IB.id )
        WHERE
            RL.id_grupo = ?"
            );

    $queryDeleteGrupo = $dbh->prepare( "DELETE FROM tb_grupo WHERE id = :id" );
    // $queryDeleteGrupo ->bindParam(':id', $id);

    $queryDeleteRL    = $dbh->prepare( "DELETE FROM rl_grupo_item WHERE id = :id " );
    // $queryDeleteRL ->bindParam(':id', $id);

    $dbh->beginTransaction();
    try {
        if( $querySelectRL->execute( array($grupo) ) ){
            $rl = $querySelectRL->fetchAll();
            if( !empty( $rl ) ){
                foreach( $rl as $key => $value ){
                    if( $queryDeleteRL->execute( array( $value['id'] ) ) ){

                    }else{

                    }
                }
            }

            if($queryDeleteGrupo->execute(array( $grupo ))){
                echo "funcionou";
            }else{
                echo 'error';
            }

        }else{

        }

        $dbh->commit();

    } catch (PDOException $e) {
        $dbh->rollBack();
        // var_dump($e);
        echo "error";
    }
