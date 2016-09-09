<?php
    $root_includes = $_SERVER['DOCUMENT_ROOT']."includes/";
    require_once $root_includes."connect.php";
    require_once $root_includes."UUID.php";
    require_once $root_includes."functions.php";

    // INSERT
    // campos e paramentros para $insertGrupoFilho
    $fieldsF = str_replace( ' ', '', "id,   nome,   publico,   id_criador,   id_pai,   descricao" );
    $paransF = str_replace( ' ', '', ":idF, :nomeF, :publicoF, :id_criadorF, :id_paiF, :descricaoF" );
    $insertGrupoFilho    = $dbh->prepare( "INSERT INTO tb_grupo( $fieldsF, dt_inclusao ) VALUES( $paransF, ( SELECT current_timestamp ) )" );

    // campos e paramentros para $insertRLGrupoPessoa
    $fieldsRLGP = str_replace( ' ', '', "id,      id_grupo,      id_pessoa,      nomehash,      permissao" );
    $paransRLGP = str_replace( ' ', '', ":idRLGP, :id_grupoRLGP, :id_pessoaRLGP, :nomehashRLGP, :permissaRLGP" );
    $insertRLGrupoPessoa = $dbh->prepare( "INSERT INTO rl_grupo_pessoa( $fieldsRLGP ) VALUES( $paransRLGP )" );

    $usuario      = $_POST['idUsuario'];
    $timePai      = $_POST['idTime'];
    $visibilidade = true;
    $nome         = $_POST['nome-modalNovoGrupo'];
    $alias        = strtoupper( "#".$_POST['alias-modalNovoGrupo'] );
    $descricao    = strip_tags( $_POST['desc-modalNovoGrupo'] );
    $permissao    = 'X';

    $dbh->beginTransaction();

    try {

        $tokenGrupo = UUID::v4();
        $insertGrupoFilho->bindParam( ':idF',         $tokenGrupo   );
        $insertGrupoFilho->bindParam( ':nomeF',       $nome         );
        $insertGrupoFilho->bindParam( ':publicoF',    $visibilidade );
        $insertGrupoFilho->bindParam( ':id_criadorF', $usuario      );
        $insertGrupoFilho->bindParam( ':id_paiF',     $timePai      );
        $insertGrupoFilho->bindParam( ':descricaoF',  $descricao    );
        $insertGrupoFilho->execute();
        echo "\ncriou grupo";


        $tokenRLGP = UUID::v4();
        $insertRLGrupoPessoa->bindParam( ':idRLGP',        $tokenRLGP  );
        $insertRLGrupoPessoa->bindParam( ':id_grupoRLGP',  $tokenGrupo );
        $insertRLGrupoPessoa->bindParam( ':id_pessoaRLGP', $usuario    );
        $insertRLGrupoPessoa->bindParam( ':nomehashRLGP',  $alias      );
        $insertRLGrupoPessoa->bindParam( ':permissaRLGP',  $permissao  );
        $insertRLGrupoPessoa->execute();
        echo "\ncriou relacionamento grupo pessoa";

        $dbh->commit();
        echo "\nDados Salvos com sucesso";
        // die();

    } catch (PDOException $e) {
        $dbh->rollBack();
        echo $e->getMessage();
    }
