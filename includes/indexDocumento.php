<?php

$itemBiblioteca = new ItemBiblioteca();

$filtro = null;
$grupo = null;
if (isset($SERVICO['metadata']['ws_filtro'])){
	$filtro = json_decode($SERVICO['metadata']['ws_filtro']);
}
if (isset($SERVICO['id_grupo'])){
	$grupo = $SERVICO['id_grupo'];
} else {
	$grupo = $_SESSION['TIME']['ID'];
}

//forçando array
$arrTipo = array();
$arrTipo[0]	=	'audio';
$arrTipo[1]	=	'doc';
$arrTipo[2]	=	'image';

$rowsetDataItemBiblioteca = $itemBiblioteca->getArquivosByGrupo($grupo,$arrTipo);
x( $rowsetDataItemBiblioteca );

$header = array();
$data = array();

if (count($rowsetDataItemBiblioteca) > 0) {
	//montando header
	$preHeader	=	json_decode($rowsetDataItemBiblioteca[0]['campos']);
	foreach ($preHeader as $n => $l){
		$header[$n]['campo'] = $n;
		$header[$n]['label'] = $l;
	}

	foreach ($rowsetDataItemBiblioteca as $key => $row) {
		$data[$key] = json_decode($row['valores']);
		$data[$key]['id'] = $row['id_ib_pai'];
	}
}

$twig->addGlobal('servico', $SERVICO);
echo $twig->render('paginator.html.twig'/*'index.html.twig'*/, array('data' => $data, 'header' => $header));