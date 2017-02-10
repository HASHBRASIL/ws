<?php
    //require_once "../connect.php";
    //require_once "UUID.php";
    //require_once "functions.php";
    x($SERVICO);
    $fieldsPessoa      = str_replace( ' ', '', "id,        metanome,                  nome, dt_criacao" );
    $valuesPessoa      = str_replace( ' ', '', ":idPessoa, 'teste_criacao_insertime', :nome" );
    $queryPessoa       = $dbh->prepare( "INSERT INTO tb_pessoa( $fieldsPessoa ) VALUES( $valuesPessoa, ( select current_timestamp ) )" );

    $fieldsTime        = str_replace( ' ', '', "id,       nome,   id_criador,   id_representacao,   descricao,   id_pai, dt_inclusao" );
    $valuesTime        = str_replace( ' ', '', ":idTimeT, :nomeT, :id_criadorT, :id_representacaoT, :descricaoT, :idPaiT" );
    $queryTime         = $dbh->prepare( "INSERT INTO tb_grupo( $fieldsTime ) VALUES( $valuesTime, ( select current_timestamp ) )" );

    $fieldsGG          = str_replace( ' ', '', "id,    nome,    id_criador,   id_pai,  dt_inclusao" );
    $valuesGG          = str_replace( ' ', '', ":idGG, :nomeGG, :idCriadorGG, :idPaiGG" );
    $queryGrupoGeral   = $dbh->prepare( "INSERT INTO tb_grupo( $fieldsGG ) VALUES( $valuesGG, ( select current_timestamp ) )" );

    $fields_RL_PG      = str_replace( ' ', '', "id,      id_pessoa,     id_grupo,     permissao, dt_inicio" );
    $values_RL_PG      = str_replace( ' ', '', ":idRLPG, :idPessoaRLPG, :idGrupoRLPG, :permissaoRLGP" );
    $queryTimePessoa  = $dbh->prepare( "INSERT INTO rl_grupo_pessoa( $fields_RL_PG ) VALUES( $values_RL_PG, ( select current_timestamp ) )" );

    $fields_RL_PT      = str_replace( ' ', '', "id,      id_grupo,     id_pessoa,     nomehash,      permissao, dt_inicio" );
    $values_RL_PT      = str_replace( ' ', '', ":idRLPT, :idGrupoRLPT, :idPessoaRLPT, :nomehashRLPT, :permissaoRLPT" );
    $queryPessoaGrupo   = $dbh->prepare( "INSERT INTO rl_grupo_pessoa( $fields_RL_PT ) VALUES( $values_RL_PT, ( select current_timestamp ) )" );

    $fieldsMeta        = str_replace( ' ', '', "id,      metanome,      valor,      id_grupo" );
    $paransMeta        = str_replace( ' ', '', ":idMeta, :metanomeMeta, :valorMeta, :idGrupoMeta" );
    $queryTimeMetadata = $dbh->prepare( "INSERT INTO tb_grupo_metadata( $fieldsMeta ) VALUES( $paransMeta )" );

    $queryModuloTime   = $dbh->prepare("INSERT INTO rl_grupo_servico (id,id_grupo,id_servico) values (:id,:idgrupo,:idsvc)");

    $idTimePaiForm     = ( isset ( $_POST['id_time_pai'] ) )       ? $_POST['id_time_pai'] : getGrupoByMetanome( 'HASH', $dbh )[0]['id'];

    $nomeForm          = ( isset ( $_POST['nome'] ) )             ? $_POST['nome']             : false;
    $idCriadorForm     = ( isset ( $_SESSION['USUARIO']['ID'] ) ) ? $_SESSION['USUARIO']['ID'] : false;
    $descricaoForm     = ( isset ( $_POST['descricao'] ) )        ? $_POST['descricao']        : false;
    $dtypeForm         = 'TbGrupo';

    if( $nomeForm && $idTimePaiForm && $idCriadorForm )
    {
        try
        {
            $dbh->beginTransaction();
            // Cria pessoa
            // todo: 02/12/2015 - Requisito abaixo não cumprido, documentado no trello
            // a) Nome do Time (será o nome da pessoa/entidade a ser utilizada/criada durante o processo)
            // 2 - Durante a validação da pessoa representada pelo time, deve ser considerado:
            // 2.1 - Se a pessoa/entidade citada no requisito 'a' não existir na tabela tb_pessoa, deve ser criada e seu id armazenado.
            // 2.2 - Se a pessoa/entidade citada no requisito 'a' existir, deve-se descobrir o id dessa pessoa na tabela tb_pessoa para uso no próximo passo.
            $tokenPessoa = UUID::v4();
            $nomePessoa  = $nomeForm;

            $queryPessoa->bindParam( ':idPessoa', $tokenPessoa );
            $queryPessoa->bindParam( ':nome',     $nomePessoa  );
            $pessoa = $queryPessoa->execute();

            // Cria Time
            $tokemTime           = UUID::v4();
            $nomeTime            = $nomePessoa;
            $idCriadorTime       = $idCriadorForm;
            $idRepresentacaoTime = $tokenPessoa;
            $idPaiTime           = $idTimePaiForm;
            $descricaoTime       = strip_tags( $descricaoForm );

            $queryTime->bindValue( ':idTimeT',           $tokemTime           );
            $queryTime->bindValue( ':nomeT',             $nomeTime            );
            $queryTime->bindValue( ':id_criadorT',       $idCriadorTime       );
            $queryTime->bindValue( ':id_representacaoT', $idRepresentacaoTime );
            $queryTime->bindValue( ':descricaoT',        $descricaoTime       );
            $queryTime->bindValue( ':idPaiT',            $idPaiTime           );
            $qt = $queryTime->execute();

            // Cria RL Time Pessoa
            $tokemRLPG    = UUID::v4();
            $permissaoRLGP = 'X';

            $queryTimePessoa->bindParam( ":idRLPG",       $tokemRLPG     );
            $queryTimePessoa->bindParam( ":idGrupoRLPG",  $tokemTime     );
            $queryTimePessoa->bindParam( ":idPessoaRLPG", $idCriadorForm );
            $queryTimePessoa->bindParam( ":permissaoRLGP", $permissaoRLGP );
            $queryTimePessoa->execute();

            // Cria Grupo geral
            $tokemGrupoGeral = UUID::v4();
            $ggNome          = "Geral";

            $queryGrupoGeral->bindParam( ":idGG",        $tokemGrupoGeral );
            $queryGrupoGeral->bindParam( ":nomeGG",      $ggNome          );
            $queryGrupoGeral->bindParam( ":idCriadorGG", $idCriadorForm   );
            $queryGrupoGeral->bindParam( ":idPaiGG",     $tokemTime       );
            $queryGrupoGeral->execute();

            // Cria RL Pessoa Grupo
            $tokemRLPT     = UUID::v4();
            $rlptIDPessoa  = $idCriadorForm;
            $rlptIDGrupo   = $tokemGrupoGeral;
            $rlptNomeHash  = '#geral';
            $rlptPermissao = 'X';

            $queryPessoaGrupo->bindParam( ":idRLPT",        $tokemRLPT     );
            $queryPessoaGrupo->bindParam( ":idGrupoRLPT",   $rlptIDGrupo   );
            $queryPessoaGrupo->bindParam( ":idPessoaRLPT",  $rlptIDPessoa  );
            $queryPessoaGrupo->bindParam( ":nomehashRLPT",  $rlptNomeHash  );
            $queryPessoaGrupo->bindParam( ":permissaoRLPT", $rlptPermissao );
            $queryPessoaGrupo->execute();

            $idmdltime = UUID::v4();

            $queryModuloTime->bindParam(':id',$idmdltime);
            $queryModuloTime->bindParam(':idgrupo',$tokemTime);
            $queryModuloTime->bindParam(':idpessoa',$tokenPessoa);
            $queryModuloTime->execute();



            $dbh->commit();
            $flashMsg = new flashMsg();
            $flashMsg->success('Time Criado com sucesso!');

            parseJsonTarget($SERVICO['id_pai']);

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
    }else
    {
        $flashMsg = new flashMsg();
        $flashMsg->error("Erro ao criar Time");

        parseJsonTarget($SERVICO['id_pai']);
    }
