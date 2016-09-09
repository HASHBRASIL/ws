<?php
require_once "importacaoDeDados.php";

$objGrupo                   =   new Grupo();

$grupo = null;
if (isset($SERVICO['metadata']['ws_grupo'])){
	$grupos = $objGrupo->getGrupoByMetanome($SERVICO['metadata']['ws_grupo']);
	if(!empty($grupos)){
		$grupo = $grupos['id'];
	} else {
		echo "Grupo destino não encontrado. Favor verificar metadata.";
		die();
	}
} else {
	$grupo = $_SESSION['TIME']['ID'];
}

$importa	= new importacaoDeDados();
$importa->importNatJur();