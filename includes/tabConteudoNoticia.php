<?php
if (isset($SERVICO['id_grupo'])){
	$grupo = $SERVICO['id_grupo'];
} else {
	$grupo = $identity->time['id'];
}

//for�ando id da camara - inicio - apagar
$grupo = '26a206ae-4acd-4de7-9a9d-29ba6fbe0ff4';
//for�ando id da camara - fim - apagar

$itemBiblioteca = new ItemBiblioteca();
$tib			= new TpItemBiblioteca();

$rowsetDataItemBiblioteca = $itemBiblioteca->getItemBibliotecaByGrupo($grupo, $SERVICO['id_tib'],$filtro);
$rowsetTib = $tib->getTemplateCabecalho($SERVICO['id_tib']);
$header = array();
$data = array();

foreach($rowsetTib as $key => $row) {
    $header[$key]['campo'] = $row['metanome'];
    $header[$key]['label'] = $row['nome'];
}

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
$twig->addGlobal('rastro', $rastro);
echo $twig->render('paginator.html.twig'/*'index.html.twig'*/, array('data' => $data, 'header' => $header));