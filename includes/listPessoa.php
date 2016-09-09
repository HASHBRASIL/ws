<?php

$request = $_REQUEST;
$grupo = new Grupo();
$perfil = new Perfil();

if (isset($SERVICO['id_grupo'])){
    $idgrupo = $SERVICO['id_grupo'];
} else {
    $idgrupo = $identity->time['id'];
}

if (isset($SERVICO['metadata']['ws_classificacao']) && ($SERVICO['metadata']['ws_classificacao'])) {
    $rowsetData = $perfil->getPessoasByGrupoByPerfil($idgrupo, $SERVICO['metadata']['ws_perfil'],
        $SERVICO['metadata']['ws_classificacao'],$request['total']);
} else {
    $rowsetData = $perfil->getPessoasByGrupoByPerfil($idgrupo, $SERVICO['metadata']['ws_perfil'],
        $request['total']);
}

$header = array(array('campo' => 'nome', 'label' => 'Nome')); // , array('campo' => 'id', 'label' => 'IDDDD')
$data = array();

if (count($rowsetData) > 0) {

    foreach ($rowsetData as $key => $row) {
        $rowsetData[$key]['nomes'] = json_decode($row['nomes']);
        $rowsetData[$key]['valores'] = json_decode($row['valores']);
        $rowsetData[$key]['metanomes'] = json_decode($row['metanomes']);
        $rowsetData[$key]['ordem'] = json_decode($row['ordem']);

        $data[$key]['nome'] = $row['nome'];
        $data[$key]['id'] = $row['id'];

        foreach ($rowsetData[$key]['metanomes'] as $k => $v) {
            $data[$key][$v] = $rowsetData[$key]['valores'][$k];
        }
    }
}

$twig->addGlobal('servico', $SERVICO);

parseJson(false, null, $data);
