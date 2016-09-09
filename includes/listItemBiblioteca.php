<?php
$request = $_REQUEST;
$grupo = new Grupo();
$ib = new ItemBiblioteca();

if (isset($SERVICO['id_grupo'])){
    $grupo = $SERVICO['id_grupo'];
} elseif (isset($SERVICO['metadata']['ws_grupo'])){
    $grupos = $objGrupo->getGruposByIDPaiByMetanome($identity->time['id'],$SERVICO['metadata']['ws_grupo']);
    if(!empty($grupos)){
        $grupo = current($grupos)['id'];
    } else {
        echo "Grupo destino n�o encontrado. Favor verificar metadata.";
        die();
    }
} else {
    $grupo = $identity->grupo['id'];
}

$rowsetDataItemBiblioteca = $ib->getItemBibliotecaByGrupo($grupo, $SERVICO['id_tib'],null,$request['total']);
$data = array();

if (count($rowsetDataItemBiblioteca) > 0) {
    foreach ($rowsetDataItemBiblioteca as $key => $row) {
        $valores = json_decode($row['valores']);
        $campos = json_decode($row['campos']);
        foreach($campos as $chave => $campo) {
                $data[$key][$campo] = $valores[$chave];
        }
        $data[$key]['id'] = $row['id_ib_pai'];
    }
}

$twig->addGlobal('servico', $SERVICO);

parseJson(false, null, $data);
