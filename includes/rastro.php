<?php

    require_once "connect.php";

    $queryRastro = $dbh->prepare(
        "WITH RECURSIVE rastro ( id, nome, id_pai ) AS (
              SELECT ts.id, ts.nome, ts.id_pai FROM tb_servico ts WHERE ts.id = :id_servico
        UNION
              SELECT ts1.id, ts1.nome, ts1.id_pai FROM tb_servico ts1 JOIN rastro ras ON ( ts1.id = ras.id_pai )
        )
        SELECT * FROM rastro");

    $queryRastro->bindParam(':id_servico', $HASH_SERVICO['id']);
    $queryRastro->execute();
    $rastro     = $queryRastro->fetchAll(PDO::FETCH_ASSOC);
    $arrInverso = array_reverse($rastro);
?>

<nav class="rastro">
    <ul>
        <?php

            foreach (  $arrInverso as $key => $value) {
                if($value['id'] == $HASH_SERVICO['id'])
                    echo '<li class="active">' . $value['nome'] . '</li>';
                else
                    echo '<li><a href="home.php?servico='. $value['id'] .'">'. $value['nome'] .'</a><i class="fa fa-angle-right"></i></li>';
            }
        ?>
    </ul>
</nav>