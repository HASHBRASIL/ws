<?php

    $entidade = $_POST['entidade'];
    session_start();

    $_SESSION['TIME']['ID'] = $entidade;
    echo $_SESSION['TIME']['ID'];

?>