<?php
    require_once "connect.php";
    require_once "functions.php";

    // var_dump( $_POST );
    // die();

    $nomeGrupo  = $_POST['nome'];
    $aliasGrupo = strtoupper( $_POST['alias'] );
    $descGrupo  = $_POST['descricao_grupo'];
    $id_grupo   = ( isset( $_POST['idGrupo'] ) ) ? $_POST['idGrupo'] : FALSE;


    if ( $id_grupo ) {

        $dbh->beginTransaction();
        $queryUpdateGrupo         = $dbh->prepare( "UPDATE tb_grupo SET nome = :nomeGrupo, descricao = :descGrupo WHERE id = :idGrupo" );
        $queryUpdateRLGrupoPessoa = $dbh->prepare( "UPDATE rl_grupo_pessoa SET nomehash = :alias WHERE id_grupo = :idGrupoRLGP" );

        try {
            // Falta fazer o update de alias na tabela metadata para imagem
            # dados tabela grupo
            $queryUpdateGrupo->bindParam( ':nomeGrupo',  $nomeGrupo );
            $queryUpdateGrupo->bindParam( ':descGrupo',  $descGrupo );
            $queryUpdateGrupo->bindParam( ':idGrupo',    $id_grupo );
            $queryUpdateGrupo->execute();
            echo "atualizou dados do grupo.\n";

            $queryUpdateRLGrupoPessoa->bindParam( ':alias',       $aliasGrupo );
            $queryUpdateRLGrupoPessoa->bindParam( ':idGrupoRLGP', $id_grupo   );
            $queryUpdateRLGrupoPessoa->execute();
            echo "atualizou alias do grupo.\n";

            $dbh->commit();
            echo "\nAtualizado!\n";

        } catch ( PDOException $e ) {
            echo "error";
            echo $e->getMessage();
            $dbh->rollBack();
            // var_dump( $e );
        }
    }else{
        echo "error";
    }
