<?php

    $grupo         = new Grupo();
    $times         = $grupo->getProdutosByIDUser( $_SESSION['USUARIO']['ID'] );

    $twig->addGlobal('servico', $SERVICO);
    echo $twig->render('form-grupo.html.twig', array('times' => $times));

