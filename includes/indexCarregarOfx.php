<?php
$pathRastro   = new Rastro();
$rastro           = $pathRastro->getPath($SERVICO['id']);
$objServico         =   new Servico();
$objServicoMetadata =   new ServicoMetadata();
$objGrupo           =   new Grupo();

$arrServicosFilho = $objServico->getFilhosById( $SERVICO['id']);
$servicoAjax    =   '';
foreach ( $arrServicosFilho as $chave => $servicoFilho ){
    if ( !empty( $objServicoMetadata->getMetadataByServico( $servicoFilho['id'] ) ) ) {
        foreach ( $objServicoMetadata->getMetadataByServico( $servicoFilho['id'] ) as $key => $meta ){
            if ($meta['valor'] == 'filter'){
                $servicoAjax    =   $meta['id_servico'];
            }
        }       
    }       
}

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

$data = array();
$data['servico']  = $servicoAjax;
$header = null;
$twig->addGlobal('servico', $SERVICO);
$twig->addGlobal('rastro', $rastro);
echo $twig->render('drag-and-drop.html.twig', array('data' => $data, 'header' => $header)); 
