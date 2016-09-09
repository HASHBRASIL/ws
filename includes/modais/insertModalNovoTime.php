<?php
    $root_includes = $_SERVER['DOCUMENT_ROOT']."includes/";
    require_once $root_includes."connect.php";
    require_once $root_includes."UUID.php";
    require_once $root_includes."functions.php";

    var_dump( $_POST  );
    die();

    $nomeForm          = $_POST['nome'];
    $metanomeForm      = $_POST['metanome'];
    $visibilidadeForm  = $_POST['visibilidade'];
    $idCriadorForm     = $_POST['id_criador'];
    $descricaoForm     = $_POST['descricao'];
    $userId            = $_POST['userID'];

    $fieldsPessoa      = str_replace( ' ', '', "id,        metanome,                  nome, dt_inclusao" );
    $valuesPessoa      = str_replace( ' ', '', ":idPessoa, 'teste_criacao_insertime', :nome" );
    $queryPessoa       = $dbh->prepare( "INSERT INTO tb_pessoa( $fieldsPessoa ) VALUES( $valuesPessoa, ( select current_timestamp ) )" );

    $fieldsTime        = str_replace( ' ', '', "id, dtype, metanome, nome, publico, id_criador, id_representacao, descricao, id_pai, dt_inclusao" );
    $valuesTime        = str_replace( ' ', '', ":idGrupoT, :dtypeT, :metanomeT, :nomeT, :publicoT, :id_criadorT, :id_representacaoT, :descricaoT, :idPaiT" );
    $queryTime         = $dbh->prepare( "INSERT INTO tb_grupo( $fieldsTime ) VALUES( $valuesTime, ( select current_timestamp ) )" );

    $fieldsGG          = str_replace( ' ', '', "id,    dtype,    nome,    publico, id_criador,    dt_inclusao" );
    $valuesGG          = str_replace( ' ', '', ":idGG, :dtypeGG, :nomeGG, 't',     :id_criadorGG" );
    $queryGrupoGeral   = $dbh->prepare( "INSERT INTO tb_grupo( $fieldsGG ) VALUES( $valuesGG, ( select current_timestamp ) )" );

    $fields_RL_PG      = str_replace( ' ', '', "id,      id_grupo,     id_pessoa,     nomehash, permissao" );
    $values_RL_PG      = str_replace( ' ', '', ":idRLPG, :idGrupoRLPG, :idPessoaRLPG" );
    $queryPessoaGrupo  = $dbh->prepare( "INSERT INTO rl_grupo_pessoa( $fields_RL_PG ) VALUES( $values_RL_PG, '#GERAL', 'X' )" );

    $fields_RL_PT      = str_replace( ' ', '', "id,      id_grupo,     id_pessoa,     permissao" );
    $values_RL_PT      = str_replace( ' ', '', ":idRLPT, :idGrupoRLPT, :idPessoaRLPT" );
    $queryPessoaTime   = $dbh->prepare( "INSERT INTO rl_grupo_pessoa( $fields_RL_PT ) VALUES( $values_RL_PT, 'X' )" );

    $fieldsMeta        = str_replace( ' ', '', "id,      metanome,      valor,      id_grupo" );
    $paransMeta        = str_replace( ' ', '', ":idMeta, :metanomeMeta, :valorMeta, :idGrupoMeta" );
    $queryTimeMetadata = $dbh->prepare( "INSERT INTO tb_grupo_metadata( $fieldsMeta ) VALUES( $paransMeta )" );

    $dbh->beginTransaction();
    try {
        // Cria pessoa
        $tokenPessoa = UUID::v4();
        $nomePessoa  = $nomeForm;

        $queryPessoa->bindParam( ':idPessoa', $tokenPessoa );
        $queryPessoa->bindParam( ':nome',     $nomePessoa  );
        $queryPessoa->execute();
        echo "Criou Pessoa, \n";

        // Cria Time
        $tokemTime        = UUID::v4();
        $dtypeTime        = $dtypeForm;
        $metanomeTime     = $metanomeForm;
        $nomeTime         = $nomeForm;
        $visibilidadeTime = $visibilidadeForm;
        $idCriadorTime    = $idCriadorForm;
        $idRepresentacao  = $tokenPessoa;
        $descricaoTime    = $descricaoForm;
        $idPai            = getGrupoByMetanome( 'HASH', $dbh )['id'];

        $queryTime->bindParam( ':idGrupoT',          $tokemTime        );
        $queryTime->bindParam( ':dtypeT',            $dtypeTime        );
        $queryTime->bindParam( ':metanomeT',         $metanomeTime     );
        $queryTime->bindParam( ':nomeT',             $nomeTime         );
        $queryTime->bindParam( ':publicoT',          $visibilidadeTime );
        $queryTime->bindParam( ':id_criadorT',       $idCriadorTime    );
        $queryTime->bindParam( ':id_representacaoT', $idRepresentacao  );
        $queryTime->bindParam( ':idPaiT',            $idPai            );
        $queryTime->bindParam( ':descricaoT',        $descricaoTime    );
        $queryTime->execute();
        echo "Criou Time, \n";

        // Cria Grupo geral
        $tokemGrupoGeral = UUID::v4();
        $ggDtype         = $dtypeForm;
        $ggNome          = "geral-".preg_replace( '/[^a-zA-Z0-9]/', '', $nomeTime );
        $ggIdCriador     = $idCriadorForm;

        $queryGrupoGeral->bindParam( ":idGG",         $tokemGrupoGeral );
        $queryGrupoGeral->bindParam( ":dtypeGG",      $ggDtype         );
        $queryGrupoGeral->bindParam( ":nomeGG",       $ggNome          );
        $queryGrupoGeral->bindParam( ":id_criadorGG", $ggIdCriador     );
        $queryGrupoGeral->execute();
        echo "Criou Grupo Geral, \n";

        // Cria RL Pessoa Grupo
        $tokemRLPG    = UUID::v4();
        $rlpgIdGrupo  = $tokemGrupoGeral;
        $rlpgIDPessoa = $idCriadorForm;
        $queryPessoaGrupo->bindParam( ":idRLPG",       $tokemRLPG    );
        $queryPessoaGrupo->bindParam( ":idGrupoRLPG",  $rlpgIdGrupo  );
        $queryPessoaGrupo->bindParam( ":idPessoaRLPG", $rlpgIDPessoa );
        $queryPessoaGrupo->execute();
        echo "Criou RL Grupo Pessoa, \n";

        // Cria RL Pessoa Time
        $tokemRLPT    = UUID::v4();
        $rlptIDGrupo  = $tokemGrupoGeral;
        $rlptIDPessoa = $idCriadorForm;
        $queryPessoaTime->bindParam( ":idRLPT",       $tokemRLPT    );
        $queryPessoaTime->bindParam( ":idGrupoRLPT",  $rlptIDGrupo  );
        $queryPessoaTime->bindParam( ":idPessoaRLPT", $rlptIDPessoa );
        $queryPessoaTime->execute();
        echo "Criou RL Grupo Time, \n";

        /*$upImg = uploadFile( 'files', '../upload_dir/', array( 'jpg', 'jpeg', 'png', 'gif' ), false, $userId );
        var_dump( $upImg );
        if( isset( $upImg['fezUpload'] ) ){
            $tokemGrupoMetadata = UUID::v4();
            $insertGrupoMetadata->bindParam( ':idMeta',       $tokemGrupoMetadata );
            $insertGrupoMetadata->bindParam( ':metanomeMeta', $metanomeMeta       );
            $insertGrupoMetadata->bindParam( ':valorMeta',    $upImg['url']       );
            $insertGrupoMetadata->bindParam( ':idGrupoMeta',  $tokenGrupo         );
            $insertGrupoMetadata->execute();
            echo "\ncriou imagem do grupo filho";
        }*/

        $dbh->commit();
        echo "Foi";

    } catch ( PDOException $e ) {
        $dbh->rollBack;
        echo "error";
        var_dump( $e->getMessage() );
    }
