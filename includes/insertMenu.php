<?php

    require_once "connect.php";
    require_once "UUID.php";

    $arConteudo = explode( ',', $_POST['idConteudo'] );

    echo "<pre>";
    var_dump($arConteudo);
    die();

    $queryUser= $dbh->prepare ("SELECT id FROM tb_pessoa WHERE metanome = 'DEPMODELO'" );
    $User = $queryUser->fetchAll( PDO::FETCH_ASSOC );


    $query                = $dbh->prepare("INSERT INTO tb_grupo VALUES ( :id, ( SELECT current_timestamp ), :nome, :publico, :id_criador, :id_pai );" );
    $queryRL              = $dbh->prepare("INSERT INTO rl_grupo_item (id, id_grupo, id_item ) VALUES (:id, :id_grupo, :id_item);" );


    $dbh->beginTransaction();

    try {
        $tokenIDMaster = UUID::v4();
        $query->bindParam(':id', $tokenIDMaster);
        $query->bindParam(':nome', $_POST['nome']);
        $query->bindParam(':id', $_POST['publico']);
        $query->bindParam(':id', $User);
        $query->bindParam(':id', $_POST['idSite']);
        $query->execute();


        foreach($arConteudo as $values){
            $tokenRL = UUID::v4();
            $query->bindParam(':id', $tokenRL);
            $query->bindParam(':id_grupo', $tokenIDMaster);
            $query->bindParam('id_item',$values);
            $query->execute();


        }

        $dbh->commit();
        echo "Dados salvos com sucesso.\n";

    } catch (PDOException $e) {

        $dbh->rollBack();
        echo 'error';


    }
