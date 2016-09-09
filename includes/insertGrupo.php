<?php
    require_once "UUID.php";
    require_once "functions.php";

    $grupo           = new Grupo();
    $rl_grupo_pessoa = new RlGrupoPessoa();

    // Variáveis para insert
    $idgrupo   = UUID::v4();
    $nome      = $_REQUEST['nome'];
    $idCriador = $_SESSION['USUARIO']['ID'];
    $descricao = (empty($_REQUEST['descricao']))?null:$_REQUEST['descricao'];
    $nomehash  = "#".strtoupper( preg_replace("/[^a-zA-Z]/", "", $_REQUEST['nomehash']) );

    // Verifica nomehash
    $nh = $rl_grupo_pessoa->getNomeHashByValorAndIDUser( $idCriador, $nomehash );
    try {
        $dbh->beginTransaction();
        $insertGrupo = $grupo->createGrupo($idgrupo, $nome, $idCriador, $descricao);
        if( $insertGrupo ){
            if( empty($nh) )
            {
                $idRLGP = UUID::v4();
                $insertRLGrupoPessoa = $rl_grupo_pessoa->criaRLGrupoPesso($idRLGP, $idgrupo, $idCriador, $nomehash);
                if( $insertRLGrupoPessoa )
                {

                    $dbh->commit();

                    $flashMsg = new flashMsg();
                    $flashMsg->success('Grupo criado com sucesso');

                    parseJsonTarget($SERVICO['id_pai']);
                }else
                {
                    throw new Exception( "Erro ao criar relacionamento" );
                }
            }else
            {
                throw new Exception( "Já existe este nomehash {$nomehash} escolha outro." );
            }

        }else{
            throw new Exception( "Falha ao criar grupo" );
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
