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
$rastro           = $pathRastro->getPath($SERVICO['id']);

$itemBiblioteca = new ItemBiblioteca();
$tib = new TpItemBiblioteca();

$grupo = new Grupo();

$rowsetDataItemBiblioteca = $itemBiblioteca->pegarTodasPorTib($SERVICO['id_tib']);
$rowsetTib = $tib->getTemplateCabecalho($SERVICO['id_tib']);

$header = array();
$data = array();

foreach($rowsetTib as $key => $row) {
    $header[$key]['campo'] = $row['metanome'];
    $header[$key]['label'] = $row['nome'];
}

if (count($rowsetDataItemBiblioteca) > 0) {
    foreach ($rowsetDataItemBiblioteca as $key => $row) {
    	//x($row);
        $valores = json_decode($row['valores']);
        $campos = json_decode($row['campos']);
        foreach($campos as $chave => $campo) {
                $data[$key][$campo] = $valores[$chave];
        }
        $data[$key]['id'] = $row['id_ib_pai'];
    }
}
//x($data[0]);
$twig->addGlobal('servico', $SERVICO);
$twig->addGlobal('rastro', $rastro);
echo $twig->render('paginator.html.twig'/*'index.html.twig'*/, array('data' => $data, 'header' => $header));
