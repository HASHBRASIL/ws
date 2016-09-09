<?php

$objGrupo                   =   new Grupo();
$objItemBiblioteca          =   new ItemBiblioteca();
$objTpItemBiblioteca        =   new TpItemBiblioteca();
$objItemBibliotecaMetadata  =   new ItemBibliotecaMetadata();
$objRlGI                    =   new RlGrupoItem();

$grupo = null;
if (isset($SERVICO['metadata']['ws_grupo'])){
    $grupos = $objGrupo->getGruposByIDPaiByMetanome($identity->time['id'],$SERVICO['metadata']['ws_grupo']);
    if(!empty($grupos)){
        $grupo = $grupos['id'];
    } else {
        echo "Grupo destino não encontrado. Favor verificar metadata.";
        die();
    }    
} else {
    $grupo = $identity->grupo['id'];
}

$time = $identity->time['id'];

$arrCampos              =   array();
$arrCampos['id']        =   $SERVICO['metadata']['ws_arqcampo'];
$item = $objTpItemBiblioteca->getTpItemBibliotecaBy( $arrCampos );
$arrCamposMaster              =   array();
$arrCamposMaster['id']        =   $item[0]['id_tib_pai'];
$itemmaster = $objTpItemBiblioteca->getTpItemBibliotecaBy($arrCamposMaster);

$id_ib      =   UUID::v4();
$id_ib_master = UUID::v4();

$arquivo    =   array();
$arquivo[$id_ib . '_enclosure' ]    = $_FILES['file'];

$caminhoDoArquivo   =   localUpload( $arquivo, $time, $grupo);

$objItemBiblioteca->criarItem($id_ib_master, null, $itemmaster[0]['id'] );
$objItemBiblioteca->criarItem($id_ib, $caminhoDoArquivo, $item[0]['id'], $id_ib_master);

$objItemBibliotecaMetadata->criarItemBibliotecaMetadata( $_FILES['file'], $id_ib);


$objRlGI->criaRlGrupoItem(UUID::v4(),$grupo,$id_ib_master);

if(isset($SERVICO['metadata']['ws_url'])){
	$sucesso = getUrlContent($SERVICO['metadata']['ws_url'] . $id_ib_master);
} 