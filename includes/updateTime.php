<?php

    require_once "connect.php";

    $id = $_POST['idtime'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];

    $grupo = new Grupo();

    try
    {
        $dbh->beginTransaction();
        if( $grupo->getTimeByNome($nome) ){
            throw new Exception( "Nome {$nome} já existe, escolha outro" );
        }else{
            $grupo->updateTime($id, $nome, $descricao);

            $dbh->commit();
            $flashMsg = new flashMsg();
            $flashMsg->success('Time atualizado com sucesso!');

            parseJsonTarget($SERVICO['id_pai']);
        }
    }
    catch (PDOException $e)
    {
        $dbh->rollBack();
        parseJson(true, $e->getMessage(), $e->getTraceAsString());
    }
    catch ( Exception $erro )
    {
        $dbh->rollBack();
        parseJson(true, $erro->getMessage(), $erro->getTraceAsString());
    }