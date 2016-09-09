<?php
set_time_limit('36000');
ini_set('memory_limit', '50000M');

$this->dbh = DatabaseConnection::getInstance()->getConnection();

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
$conteudo			=	file('upload_dir/'.$caminhoDoArquivo);
$vinteeum			=	0;
$outros				=	array();
unset( $conteudo[0] );
foreach ( $conteudo as $filiado){
	
	$filiado	=	trim($filiado);
	$filiado	.=	';"'.$_FILES['file']['name'].'";"'.date("Ymd").'";"'.$_SESSION['TIME']['ID'].'";"'.$grupo.'";"'.$_SESSION['USUARIO']['ID'].'"';
	$filiado	=	str_replace("'", "''", $filiado);
	$filiado	=	str_replace('"', "'", $filiado);
	$filiado	=	str_replace(';', ",", $filiado);
	$filiado	=	utf8_encode($filiado);
	$stmt		=	$this->dbh->prepare("INSERT INTO ing_filiados_sead VALUES (" . $filiado . ")");
	$stmt->execute();

}

if(isset($SERVICO['metadata']['ws_url'])){
	$sucesso = getUrlContent($SERVICO['metadata']['ws_url'] . $id_ib_master);
}