<?php

    $id = ( isset( $_POST["id"] ) ) ? $_POST["id"] : FALSE;
    
    $queryDeleteRlGrupo		=	$dbh->prepare( "DELETE	FROM rl_grupo_item WHERE id_item = :id;" );
    $queryDeleteRlVinculo	=	$dbh->prepare( "DELETE	FROM rl_vinculo_item WHERE id_ib_vinculado = :id;" );
    $queryDeleteTbMeta      =   $dbh->prepare( "DELETE  FROM tb_itembiblioteca_metadata WHERE id_ib = :id;" );
    $queryDeleteFilhos		=	$dbh->prepare( "DELETE	FROM tb_itembiblioteca WHERE id_ib_pai = :id;" );
    $queryDeleteMaster		=	$dbh->prepare( "DELETE	FROM tb_itembiblioteca WHERE id = :id;" );
    $dbh->beginTransaction();

    try {

        $queryDeleteRlGrupo->bindParam( ':id', $id );
        $queryDeleteRlGrupo->execute();
        
        // $queryDeleteRlVinculo->bindParam( ':id', $id );
        // $queryDeleteRlVinculo->execute();

        // $queryDeleteTbMeta->bindParam( ':id', $id );
        // $queryDeleteTbMeta->execute();
        
        // $queryDeleteFilhos->bindParam( ':id', $id );
        // $queryDeleteFilhos->execute();
        
        // $queryDeleteMaster->bindParam( ':id', $id );
        // $queryDeleteMaster->execute();

        $dbh->commit();
        
        if (isset($SERVICO['metadata']['ws_target']) && ($SERVICO['metadata']['ws_target'])) {
            $servico = new Servico();
            $servicoDestino = $servico->getServiceByMetanome($SERVICO['metadata']['ws_target']);
        } else {
            $servicoDestino = $SERVICO['id_pai'];
        }
        
        $servicoDestino = current($servicoDestino);

        if ($servicoDestino) {
            $flashMsg = new flashMsg();
            $flashMsg->success('Excluido com sucesso!');

            parseJsonTarget($servicoDestino);
        } else {
            parseJson();
        }

    } catch ( PDOException $e ) {
        $dbh->rollBack();
        # var_dump($e);
        parseJson(true, $e->getMessage(), $e->getTraceAsString());
    }
?>