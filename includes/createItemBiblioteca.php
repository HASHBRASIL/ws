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
    $rastro                 = $pathRastro->getPath($SERVICO['id']);
    $templateItemBiblioteca = new TpItemBiblioteca();
    $itemBiblioteca         = new ItemBiblioteca();
    $filedir                = Zend_Registry::getInstance()->get('config')->get('filedir');

    //declarando vari�veis
    $grupo  = null;
    $servico = $SERVICO['id_tib']; //tib_pai do cara
    $perfil  = 'Conteúdo';

    // setando o grupo
    if (isset($SERVICO['metadata']['ws_filtro'])){
        $filtro = json_decode($SERVICO['metadata']['ws_filtro']);
    }
    if (isset($SERVICO['metadata']['ws_grupo'])){
        $grupo = $SERVICO['metadata']['ws_grupo'];
    } else {
        $grupo = $identity->grupo['id'];
    }

    //carregando template
    $rowsetTemplate = $templateItemBiblioteca->getTemplateByIdTibPai($servico);

    foreach ( $rowsetTemplate as $key => $value ) {
        $rowsetTemplate[$key]['metadatas'] = json_decode($value['metadatas']);
    }

    // montando campos
    $campos =   array();
    if (count($rowsetTemplate) > 0){
        foreach ($rowsetTemplate as $key => $row){
            $campos[$perfil][$key]['nome'] = $row['nome'];
            $campos[$perfil][$key]['id'] = $row['id'];
            $campos[$perfil][$key]['tipo'] = $row['tipo'];
            $campos[$perfil][$key]['metanome'] = $row['metanome'];
            $campos[$perfil][$key]['id_pai'] = $row['id_tib_pai'];
            $campos[$perfil][$key]['metadatas'] = $row['metadatas'];
            $campos[$perfil][$key]['perfil'] = $perfil;
            $campos[$perfil][$key]['mascara'] = $row['mascara'];
            if($row['tipo']=='ref_itemBiblioteca'){
                $items = $itemBiblioteca->getAllByTib($row['metadatas']->ws_tib, $row['metadatas']->ws_comboordem);
                
                $campos[$perfil][$key]['items'] = array();
                $qtd = 0;
                foreach($items as $idItem => $valorItem) {
                    $campos[$perfil][$key]['items'][$qtd] = array();
                    $campos[$perfil][$key]['items'][$qtd]['id'] = $idItem;
                    $textoValor = $row['metadatas']->ws_comboform;
                    foreach($valorItem as $metanome => $txt){
                        $textoValor = str_replace($metanome,$txt,$textoValor);
                    }
                    $campos[$perfil][$key]['items'][$qtd]['valor'] = $textoValor;
                    $qtd++;
                }
            }
        }
    }

    //carregando o twig
    $twig->addGlobal('servico', $SERVICO);
    $twig->addGlobal('rastro', $rastro);
    $twig->addGlobal('filedir', $filedir);
    echo $twig->render('form.html.twig', array('perfis' => array($perfil),  'campos' => $campos));