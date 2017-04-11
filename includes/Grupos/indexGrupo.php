<?php
/**
 * Created by PhpStorm.
 * User: jamessom
 * Date: 17/12/15
 * Time: 14:28
 */


$Grupo  = new Grupo();
$header = array(array('campo' => 'nomehash', 'label' => 'Nomehash'));
$data   = $gruposHash = $Grupo->getGruposHash();

$twig->addGlobal('servico', $SERVICO);
echo $twig->render('paginator.html.twig'/*'index.html.twig'*/, array('data' => $data, 'header' => $header));