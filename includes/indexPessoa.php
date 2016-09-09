<?php

$pathRastro   = new Rastro();
$grupo = new Grupo();
$perfil = new Perfil();
$ib = new ItemBiblioteca();
$tib = new TpItemBiblioteca();
$tinf = new TpInformacao();
$tinfm = new TpInformacaoMetadata();
$pessoa = new Pessoa();

if (isset($SERVICO['id_grupo'])){
    $grupo = $SERVICO['id_grupo'];
} else {
    $grupo = $identity->time['id'];
}

$req_id = null;
if(isset($_REQUEST['id'])) {
	$req_id = $_REQUEST['id'];
}

$ws_classificacao = $SERVICO['ws_classificacao'];
$rastro           = $pathRastro->getPath($SERVICO['id']);
$rowsetData       = $perfil->getPessoasByGrupoByPerfil($grupo, $SERVICO['metadata']['ws_perfil'], $ws_classificacao, null, true);
$header = array();
$data = array();

if (count($rowsetData) > 0) {
    foreach ($rowsetData as $key => $row) {
        $rowsetData[$key]['nomes'] = json_decode($row['nomes']);
        $rowsetData[$key]['valores'] = json_decode($row['valores']);
        $rowsetData[$key]['metanomes'] = json_decode($row['metanomes']);
        $rowsetData[$key]['ordem'] = json_decode($row['ordem']);
        $rowsetData[$key]['tpinfo'] = json_decode($row['tpinfo_id']);

        $data[$key]['id'] = $row['id'];

        foreach ($rowsetData[$key]['tpinfo'] as $cnt => $inf) {
            $objTinf = $tinf->getById($inf);
            if($objTinf['tipo']=='ref_itemBiblioteca'){
                $idtib = $tinfm->getMetadatasByTpinf($inf);
                foreach($idtib as $meta) {
                    if($meta['metanome']=='ws_tib') {
                        $cmp = current($tib->getCampoPadrao($meta['valor']));
                        $lbl = current($ib->getByPaiETIB($rowsetData[$key]['valores'][$cnt],$cmp['id']));
                        $rowsetData[$key]['valores'][$cnt] = $lbl['valor'];
                    }
                }
            } else if($objTinf['tipo']=='ref_pessoa'){
        		if(preg_match('/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/', $rowsetData[$key]['valores'][$cnt])){
                   $rsPessoa = $pessoa->getPessoaById($rowsetData[$key]['valores'][$cnt]);
                   $rowPessoa = current($rsPessoa);
                   $rowsetData[$key]['valores'][$cnt] = $rowPessoa['nome'];
                }
            }
        }

        foreach ($rowsetData[$key]['metanomes'] as $k => $v) {
            $data[$key][$v] = $rowsetData[$key]['valores'][$k];
        }
    }
    if(isset($SERVICO['ws_gridordem'])) {
        $campos = $perfil->getCamposByGridOrdem($SERVICO['ws_gridordem']);
    } else {
        $campos = $perfil->getCamposLista($SERVICO['metadata']['ws_perfil']);
    }

    $header = array();
    foreach ($campos as $key=>$valor) {
            $header[$key]['campo'] = $valor['metanome'];
            $header[$key]['label'] = $valor['nome'];
    }

}

$twig->addGlobal('servico', $SERVICO);
$twig->addGlobal('rastro', $rastro);
echo $twig->render('paginator.html.twig'/*'index.html.twig'*/, array('data' => $data, 'header' => $header, 'req_id' => $req_id));

