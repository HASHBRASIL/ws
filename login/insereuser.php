<?php
    require_once("../includes/connect.php");
    require_once("../includes/UUID.php");
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Preparação das consultas
    $arrayOfValues = array("EMAIL","TELCEL","CEP","UF","CIDADE","ENDERECO");
    $questionMarks = join(",", array_pad(array(), count($arrayOfValues), "?"));
    $instpes = $dbh->prepare("insert into tb_pessoa(id,nome,dt_inclusao) values (?,?,now());");
    $instusr = $dbh->prepare("insert into tb_usuario(id,nomeusuario,password_encrypted,salt,tentativas_erradas) values (?,?,?,?,0);");
    $instinfo = $dbh->prepare("insert into tb_informacao(id,id_pessoa,id_tinfo,id_criador,valor) values (?,?,?,?,?);");
    $instgrp = $dbh->prepare("insert into tb_grupo(id,nome,metanome,id_pai,id_criador,dt_inclusao) values (?,?,?,?,?,now());");
    $insrlgp = $dbh->prepare("insert into rl_grupo_pessoa(id,id_pessoa,id_grupo) values (?,?,?);");
    $selhash = $dbh->prepare("select id from tb_grupo where metanome = ?;");
    $selinfo = $dbh->prepare("select id,metanome from tp_informacao where metanome in (" . $questionMarks . ");");

    //Preparacao dos dados iniciais
    $idpessoa = UUID::v4();
    $idgrpp = UUID::v4();
    $idgrpg = UUID::v4();
    $idrlgp = UUID::v4();
    $idrlgg = UUID::v4();
    $idinfoemail = UUID::v4();
    $idinfocel = UUID::v4();
    $idinfocep = UUID::v4();
    $idinfouf = UUID::v4();
    $idinfocid = UUID::v4();
    $idinfoend = UUID::v4();
    //Recuperação dos dados por metanome
    //HASH ID
    $selhash->execute(array("HASH"));
    $arrHash = $selhash->fetchAll();
//  var_dump($arrHash);
    $idhash = $arrHash[0][0];

    //INFOS PESSOA
//  echo '2';
    $selinfo->execute($arrayOfValues);
    $arrInfos = $selinfo->fetchAll();
    $arrIdInfo = array();
//  var_dump($arrInfos);
    foreach($arrInfos as $value) {
        $arrIdInfo[$value['metanome']] = $value['id'];
    }

    //INICIANDO A TRANSACAO
    $dbh->beginTransaction();
    try {
        $instpes->execute(array($idpessoa,$_POST['nome']));
        $instusr->bindParam(1, $idpessoa);
        $instusr->bindParam(2, $_POST['usuario']);
        $instusr->bindParam(3, $_POST['senha'], PDO::PARAM_STR);
        $instusr->bindParam(4, $_POST['sal'], PDO::PARAM_STR);
        $instusr->execute();
        //$instusr->execute(array($idpessoa,$_POST['usuario'],$senha,$_POST['sal']));
        $instgrp->execute(array($idgrpp,'PESSOAL',$_POST['nome'] . ' - Grupo Pessoal',$idhash,$idpessoa));
        $instgrp->execute(array($idgrpg,'GERAL',$_POST['nome'] . ' - Grupo Geral',$idhash,$idpessoa));
        $insrlgp->execute(array($idrlgp,$idpessoa,$idgrpp));
        $insrlgp->execute(array($idrlgg,$idpessoa,$idgrpg));
        $instinfo->execute(array($idinfoemail,$idpessoa,$arrIdInfo['EMAIL'],$idpessoa,$_POST['email']));
        $instinfo->execute(array($idinfocel,$idpessoa,$arrIdInfo['TELCEL'],$idpessoa,$_POST['telcel']));
        $instinfo->execute(array($idinfocep,$idpessoa,$arrIdInfo['CEP'],$idpessoa,$_POST['cep']));
        $dbh->commit();
        echo 'home.php';

    } catch(Exception $e) {
        $dbh->rollBack();
        var_dump($e);
//      echo 'QUEEEEN';

    }
