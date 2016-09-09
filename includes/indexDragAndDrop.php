<?php
$pathRastro   = new Rastro();
$objServico         =   new Servico();
$objServicoMetadata =   new ServicoMetadata();
$objGrupo           =   new Grupo();
$rastro           = $pathRastro->getPath($SERVICO['id']);

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
$time = $identity->time['id'];
$grupo = null;
if (isset($SERVICO['metadata']['ws_grupo'])){
    $grupos = $objGrupo->getGruposByIDPaiByMetanome($time,$SERVICO['metadata']['ws_grupo']);
    if(!empty($grupos)){
        $grupo = $grupos[0]['id'];
    } else {
        echo "Grupo destino não encontrado. Favor verificar metadata.";
        die();
    }    
} else {
    $grupo = $identity->grupo['id'];
}

$data = array();
$data['servico']  = $servicoAjax;
$header = null;
$twig->addGlobal('servico', $SERVICO);
$twig->addGlobal('rastro', $rastro);
echo $twig->render('drag-and-drop.html.twig', array('data' => $data, 'header' => $header)); 