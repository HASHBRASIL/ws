<?php
require_once "importacaoDeDados.php";

$objGrupo                   =   new Grupo();
$objItemBiblioteca          =   new ItemBiblioteca();
$objTpItemBiblioteca        =   new TpItemBiblioteca();
$objItemBibliotecaMetadata  =   new ItemBibliotecaMetadata();
$objRlGI                    =   new RlGrupoItem();

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

$caminhoDoArquivo   =   localUpload( $arquivo, $_SESSION['TIME']['ID'], $grupo);

chmod('upload_dir/'.$caminhoDoArquivo,0777);
$linhas				= file('upload_dir/'.$caminhoDoArquivo);
$contentsOriginal	= file_get_contents( 'upload_dir/'.$caminhoDoArquivo );

$importa	= new importacaoDeDados();
$importa->importOFX( $linhas , $caminhoDoArquivo );

if(isset($SERVICO['metadata']['ws_url'])){
	$sucesso = getUrlContent($SERVICO['metadata']['ws_url'] . $id_ib_master);
}