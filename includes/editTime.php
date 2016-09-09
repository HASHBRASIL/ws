<?php

    $rlgrupopessoa = new RlGrupoPessoa();
    $grupo         = new Grupo();
    $times         = $grupo->getProdutosByIDUser( $_SESSION['USUARIO']['ID'] );
    $data          = $grupo->getTimeByID($_SESSION['TIME']['ID']);

    $twig->addGlobal('servico', $SERVICO);
    echo $twig->render('time.html.twig', array('times' => $times, "data" => $data, "editar" => true, 'timeid' => $_SESSION['TIME']['ID']));
