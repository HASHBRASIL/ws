<?php

    $pathRastro   = new Rastro();
    $rastro           = $pathRastro->getPath($SERVICO['id']);
    if (isset($SERVICO['id_grupo'])){
        $grupo = $SERVICO['id_grupo'];
    } else {
        $grupo = $identity->time['id'];
    }

    if (isset($SERVICO['metadata']['ws_id']) && ($SERVICO['metadata']['ws_id'])) {
        $param = array ('usr' => $identity->id, 'time' => $identity->time['id_representacao']);
        $uuidPessoa = $param[$SERVICO['metadata']['ws_id']];
    } elseif ($_REQUEST['id']) {
        $uuidPessoa = $_REQUEST['id'];
    } else {
                // @todo erro!
        parseJson(true, 'É necessário selecionar uma pessoa para editar!');
    }

    $tpInformacao = new TpInformacao();
    $pessoa = new Pessoa();
    $itemBiblioteca = new ItemBiblioteca();
    $tib = new TpItemBiblioteca();

    $rsPessoa = $pessoa->getPessoaById($uuidPessoa);

    $rowPessoa = current($rsPessoa);

    $campos = $tpInformacao->getTpInformacaoByPerfisByPessoaByGrupo($SERVICO['metadata']['ws_perfil'], $uuidPessoa, $grupo);

    $arPerfil = explode(',', $SERVICO['metadata']['ws_perfil']);

    foreach ($campos as $campo) {
        $campo['metadatas'] = json_decode($campo['metadatas']);
        $arCampos[$campo['perfil']][] = $campo;
    }

    foreach ($arCampos as $chave => $campo) {
        foreach ($arCampos[$chave] as $key => $field) {
            if($field['tipo'] == 'ref_itemBiblioteca') {
                if( $field['valor']) {
                    $reg = $itemBiblioteca->getById($field['valor']);
                    $padrao = $tib->getCampoPadrao(current($reg)['id_tib']);
                    $iblabel = $itemBiblioteca->getByPaiETIB($field['valor'],current($padrao)['id']);
                }
                if(!empty($iblabel)) {
                    $field['label'] = current($iblabel)['valor'];
                }
                if($field['lista']) {
                    $field['items'] = array();
                    $countItem = 0;
                    if(!$field['obrigatorio']){
                        $field['items'][$countItem]['id'] = '';
                        $field['items'][$countItem]['valor'] = '';
                        $countItem++;
                    }
                    $items = $itemBiblioteca->getAllByTib($field['metadatas']->ws_tib, $field['metadatas']->ws_comboordem);
                    foreach($items as $idItem => $valorItem) {
                        $field['items'][$countItem] = array();
                        $field['items'][$countItem]['id'] = $idItem;
                        $textoValor = $field['metadatas']->ws_comboform;
                        foreach($valorItem as $metanome => $txt){
                            $textoValor = str_replace($metanome,$txt,$textoValor);
                        }
                        $field['items'][$countItem]['valor'] = $textoValor;
                        $countItem++;
                    }
                }
            } else if ($field['tipo'] == 'ref_pessoa') {
                if(preg_match('/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/', $field['valor'])){
		    $itemPessoa = $pessoa->getPessoaById($field['valor']);
		    $itemPessoa = current($itemPessoa);
                    $field['items'] = array(array('id'=>$field['valor'],'valor'=>$itemPessoa['nome']));
                }
            }
            if ( !empty($field['id_pai']) ) {
                if (!empty($field['tbinfopaiid'])) {
                    $arrayCampos[$chave][$field['nome_pai']][$field['tbinfopaiid']][] = $field;
                } else {
                    $arrayCampos[$chave][$field['nome_pai']][] = $field;
                }
            } else {
                if ( $field['tipo'] != 'Master' ) {
                    $arrayCampos[$chave][$key] = $field;
                } else {
                    $arrayCampos['master'][$field['nome']] = $field;
                }
            }
        }       
    }

    // new dBug($arrayCampos);
    //die();

    $twig->addGlobal('servico', $SERVICO);
    $twig->addGlobal('rastro', $rastro);
    echo $twig->render('form.html.twig', array('perfis' => $arPerfil, 'campos' => $arrayCampos, 'id' => $uuidPessoa, 'pessoa' => $rowPessoa));
