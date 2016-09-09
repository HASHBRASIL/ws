<?php

    $rlgrupopessoa = new RlGrupoPessoa();
    $grupo         = new Grupo();

    $times         = $grupo->getProdutosByIDUser( $_SESSION['USUARIO']['ID'] );

    $twig->addGlobal('servico', $SERVICO);
    echo $twig->render('time.html.twig', array('times' => $times));

