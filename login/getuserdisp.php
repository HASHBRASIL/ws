<?php
    require_once("../includes/connect.php");
    $qry = $dbh->prepare("select 1 from tb_usuario where nomeusuario = ?;");
        $qry->execute(array($_GET["usuario"]));
        $ret = $qry->fetchAll();
    echo count($ret);
