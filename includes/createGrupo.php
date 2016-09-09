<?php
    $rlgrupopessoa = new RlGrupoPessoa();
    $grupo         = new Grupo();

    $userID          = $_SESSION['USUARIO']['ID'];
    $produtos        = $grupo->getProdutosByIDUser( $userID );

    $twig->addGlobal('servico', $SERVICO);
    echo $twig->render('grupo.html.twig');
