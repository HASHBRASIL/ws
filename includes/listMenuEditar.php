<?php
    include "connect.php";

    $nome        = $_POST['nome'];
    $id          = $_POST['idSite'];
    $selecionado = $_POST['idSelecionado'];

    $query = $dbh->prepare( "UPDATE tb_grupo SET nome = ? WHERE id = ?;" );

    if($query->execute(array( $nome, $selecionado ))){
        echo "Sucesso!\n";
    }else{
        echo "Erro!\n";
    }
