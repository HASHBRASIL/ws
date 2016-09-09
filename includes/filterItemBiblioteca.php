<?php
$return_arr = array();
$row_array = array();
if ( isset($_GET['search']) && strlen($_GET['search']) > 0) {
	$page = 1;
	if(isset($_REQUEST['page'])) {
		$page = $_REQUEST['page'];
	}
	$itemBiblioteca = new ItemBiblioteca();
	$rsItens = $itemBiblioteca->getByValor($SERVICO['id_tib'],$SERVICO['ws_comboordem'],$_GET['search'], 10,$page);
	$ret = array();
	foreach ($rsItens as $valor) {
		$ret[$valor['id_ib_pai']][$valor['metanome']] = $valor['valor'];
	}
	foreach ($ret as $key => $row) {
		$row_array['id'] = $key;
		$retText = $SERVICO['ws_comboform'];
		foreach ($row as $chave => $texto) {
			$retText = str_replace($chave,$texto,$retText);
		}
		$row_array['text'] = $retText;
		array_push($return_arr, $row_array);
	}

} else {
	$row_array['id'] = 0;
	$row_array['text'] = utf8_encode($SERVICO['ws_tibcampo']);

	array_push($return_arr, $row_array);
}

parseResults($return_arr,count($ret)>=10);
