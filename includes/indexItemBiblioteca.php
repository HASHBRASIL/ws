<?php
/*
 * Para a funcionalidade de IB genérica funcionar existe a necessidade de configuração dos metadatas das TIBs
 * Pois eles que organizam os formuláriuos de criação, edição e listagem de conteúdo
 * Tabela: tp_itembiblioteca_metadata
 * Os Metas necessários são:"ws_visivel", "ws_ordem" e "ws_ordemLista"
 *
 * Se você esta tendo problema com essa funcionalidade, revise tais dados.
 *
 * Paz, amor e um arm-lock voador.
 */

$pathRastro   = new Rastro();
$itemBiblioteca = new ItemBiblioteca();
$tib = new TpItemBiblioteca();
$objGrupo       = new Grupo();

$filtro = null;
$grupo = null;

$rastro           = $pathRastro->getPath($SERVICO['id']);

if (isset($SERVICO['metadata']['ws_filtro'])){
    $filtro = json_decode($SERVICO['metadata']['ws_filtro']);
}

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

$rowsetDataItemBiblioteca = $itemBiblioteca->getItemBibliotecaByGrupo($grupo, $SERVICO['id_tib'],$filtro);
$rowsetTib = $tib->getTemplateCabecalho($SERVICO['id_tib']);
$header = array();
$data = array();

foreach($rowsetTib as $key => $row) {
    $header[$key]['campo'] = $row['metanome'];
    $header[$key]['label'] = $row['nome'];
    $header[$key]['tipo']  = $row['tipo'];
    if($row['tipo']=='ref_itemBiblioteca'){
        $metas = $tib->getMetanomesByTIB($row['id']);
        foreach($metas as $meta){
            if($meta['metanome']=='ws_tib'){
                $header[$key]['padrao'] = current($tib->getCampoPadrao($meta['valor']));
            }
        }
    }
}

if (count($rowsetDataItemBiblioteca) > 0) {
    foreach ($rowsetDataItemBiblioteca as $key => $row) {
        $valores = json_decode($row['valores']);
        $campos = json_decode($row['campos']);
        foreach($campos as $chave => $campo) {
            $tipo = '';
            $padrao = '';
            foreach($header as $cmp){
                if($cmp['campo']==$campo){
                    $tipo = $cmp['tipo'];
                    if($tipo=='ref_itemBiblioteca'){
                        $padrao = $cmp['padrao'];
                    }                    
                }
            }
            if($tipo=='ref_itemBiblioteca'){
                $lbl = current($itemBiblioteca->getByPaiETIB($valores[$chave],$padrao['id']));
                $data[$key][$campo] = $lbl['valor'];
            } else {
                $data[$key][$campo] = $valores[$chave];
            }
        }
        $data[$key]['id'] = $row['id_ib_pai'];
    }
}

$twig->addGlobal('servico', $SERVICO);
$twig->addGlobal('rastro', $rastro);
echo $twig->render('paginator.html.twig'/*'index.html.twig'*/, array('data' => $data, 'header' => $header));
