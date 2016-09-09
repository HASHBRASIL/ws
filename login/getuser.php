<?php

    require_once("../includes/databaseconnect.php");
    require_once('../includes/connect.php');
    require_once('../includes/Random.php');

    $qry = $dbh->prepare("SELECT u.salt, u.password_encrypted, u.id, u.foto, p.nome FROM tb_usuario u JOIN tb_pessoa p ON (u.id = p.id) where u.nomeusuario = :usuario;");
    $qry->bindParam(':usuario', $_GET["usuario"]);
    $qry->execute();
    $ret = $qry->fetchAll(PDO::FETCH_ASSOC);
    $retorno = array();

    if (count($ret) > 0) {
        $salt = $ret[0]['salt'];
        $pwd  = $ret[0]['password_encrypted'];
        $id   = $ret[0]['id'];

        session_start();

        $_SESSION['USUARIO']['SALSESSAO']   = Random::random_str(8);
        $_SESSION['USUARIO']['SENHA']       = hash_pbkdf2('sha1',$pwd,$_SESSION['USUARIO']['SALSESSAO'],10000,40);
        $_SESSION['USUARIO']['ID']          = $id;
        $_SESSION['USUARIO']['NOMEUSUARIO'] = $_GET["usuario"];
        $_SESSION['USUARIO']['NOME']        = $ret[0]['nome'];
        $_SESSION['USUARIO']['FOTO']        = $ret[0]['foto'];
        $retorno['dbsalt']                  = $salt;
        $retorno['sessionsalt']             = $_SESSION['USUARIO']['SALSESSAO'];
    } else {
        $retorno['dbsalt'] = Random::random_str(8);
        $retorno['sessionsalt'] = Random::random_str(8);
    }

    echo json_encode($retorno);
