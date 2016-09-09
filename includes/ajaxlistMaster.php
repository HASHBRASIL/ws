<?php
    include "connect.php";

    $startCount  = ( isset( $_POST['pag'] ) ) ? $_POST['pag'] : 0;
    $limitResult = 30;

    $listQuery = $dbh->prepare("
        WITH RECURSIVE ib_temp ( id , id_ib_pai , id_tib , valor , nome, ordem  ) AS (
            (
                SELECT ib.id, ib.id_ib_pai, ib.id_tib, ib.valor, tib.nome, '-1'
                FROM tb_itembiblioteca ib
                    JOIN tp_itembiblioteca tib ON (ib.id_tib = tib.id)
                WHERE id_tib = :master ORDER BY ib.dt_criacao DESC OFFSET :startCount LIMIT :limitResult
            )
        UNION
            SELECT ib.id, ib.id_ib_pai, ib.id_tib, ib.valor, tib.nome, timb.valor AS ordem
            FROM tb_itembiblioteca ib JOIN ib_temp ibt ON ( ib.id_ib_pai = ibt.id )
                JOIN tp_itembiblioteca tib ON ( ib.id_tib = tib.id )
                JOIN tp_itembiblioteca_metadata timb ON ( ib.id_tib = timb.id_tib )
            WHERE timb.metanome = 'ws_ordemLista'
        )
        SELECT * from ib_temp"
    );

    $listQuery->bindParam( ':master',      $_POST['id_master'] );
    $listQuery->bindParam( ':startCount',  $startCount );
    $listQuery->bindParam( ':limitResult', $limitResult );

    function resortArray( $a, $b ){

        if( $a['ordem'] > $b['ordem'] ){
            return 1;
        }else{
            return 0;
        }
    }

    try {

        $listQuery->execute();
        $listItens = $listQuery->fetchAll( PDO::FETCH_ASSOC );

        foreach( $listItens as $key => $value ){

            if( !empty( $value['id_ib_pai'] ) ){
                $arOrdenado[$value['id_ib_pai']][] = $value;
            }

        }

        $novoArray = array();

        if( isset( $arOrdenado ) ){

            foreach( $arOrdenado as $key => $value ){
                usort( $value, 'resortArray' );
                $novoArray[$key] = $value;
            }

        }

        echo JSON_encode( $novoArray );

    } catch ( PDOException $e ) {
        // var_dump($e);
        echo "error";
    }
?>
