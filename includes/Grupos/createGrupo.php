<?php
    $twig->addGlobal('servico', $SERVICO);
    echo $twig->render('form-grupo.html.twig', array('grupo' => true));
