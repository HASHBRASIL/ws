<?php

    $pathRastro   = new Rastro();
    $tpInformacao = new TpInformacao();
    $ib       = new ItemBiblioteca();
    $campos = $tpInformacao->getTpInformacaoByPerfis($SERVICO['metadata']['ws_perfil']);
    $rastro = $pathRastro->getPath($SERVICO['id']);

    $arPerfil = explode(',', $SERVICO['metadata']['ws_perfil']);

    foreach ($campos as $campo) {
        $campo['metadatas'] = json_decode($campo['metadatas']);
        $arCampos[$campo['perfil']][] = $campo;
    }

    foreach ($arCampos as $chave => $campo) {
        foreach ($arCampos[$chave] as $key => $field) {
            if ($field['tipo']=='ref_itemBiblioteca' && $field['lista']==true) {
            $items = $ib->getAllByTib($field['metadatas']->ws_tib, $field['metadatas']->ws_comboordem);
            $field['items'] = array();
            $countItem = 0;
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
        if ( !empty($field['id_pai']) ) {
                $arrayCampos[$chave][$field['nome_pai']][] = $field;
            } else {
                if ( $field['tipo'] != 'Master' ) {
                    $arrayCampos[$chave][$key] = $field;
                } else {
                    $arrayCampos['master'][$field['nome']] = $field;
                }
            }
        }
    }

    $twig->addGlobal('servico', $SERVICO);
    $twig->addGlobal('rastro', $rastro);
    echo $twig->render('form.html.twig', array('perfis' => $arPerfil, 'campos' => $arrayCampos));
