<?php

    $pathRastro = new Rastro();
    $rastro = $pathRastro->getPath($SERVICO['id']);

    $qryTabs = $dbh->prepare("select table_name as id, table_name as valor from information_schema.tables where table_schema = 'hash' and table_type = 'BASE TABLE' order by table_name");
    $qryTabs->execute();
    $rsTabs=$qryTabs->fetchAll(PDO::FETCH_ASSOC);

    $qryGroups = $dbh->prepare("select nome as valor, id as id from tb_grupo where id_representacao is not null");
    $qryGroups->execute();
    $rsGroups=$qryGroups->fetchAll(PDO::FETCH_ASSOC);
//x($rsGroups);
    $arrCampos = array();
    $arrCampos['Tabelas'][0] = array(
        'id'            => 'conteudo',
        'ordem'         => '0',
        'obrigatorio'   => 'true',
        'multiplo'      => false,
        'nome'          => 'Tabela',
        'descricao'     => 'Tabela a ser transformada',
        'metanome'      => 'Tabela',
        'tipo'          => 'ref_itemBiblioteca',
        'perfil'        => 'Tabelas',
        'items'         => $rsTabs,
        'metadatas'     => array(
                'ws_ordemLista'         => '0',
                'ws_style'              => 'col-md-12',
                'ws_style_object'   => 'select2-skin'
        )
    );
    $arrCampos['Tabelas'][1] = array(
    		'id'            => 'id',
    		'ordem'         => '0',
    		'obrigatorio'   => 'true',
    		'multiplo'      => false,
    		'nome'          => 'Grupo',
    		'descricao'     => 'Grupo a ser vinculado',
    		'metanome'      => 'grupo',
    		'tipo'          => 'ref_itemBiblioteca',
    		'perfil'        => 'Tabelas',
    		'items'         => $rsGroups,
    		'metadatas'     => array(
    				'ws_ordemLista'         => '1',
    				'ws_style'              => 'col-md-12',
    				'ws_style_object'   => 'select2-skin'
    		)
    );
    $arrCampos['Tabelas'][2] = array(
    		'id'            => 'data',
    		'ordem'         => '1',
    		'obrigatorio'   => 'true',
    		'multiplo'      => false,
    		'nome'          => 'Data da Carga',
    		'metanome'      => 'Carga',
    		'tipo'          => 'date',
    		'perfil'        => 'Tabelas',
    		'metadatas'     => array(
    				'ws_ordemLista'         => '2',
    				'ws_style'              => 'col-md-12'
    		)
    );
    $arrCampos['Tabelas'][3] = array(
    		'id'            => 'origem',
    		'ordem'         => '2',
    		'obrigatorio'   => 'true',
    		'multiplo'      => false,
    		'nome'          => 'Origem dos dados',
    		'metanome'      => 'Dados',
    		'tipo'          => 'text',
    		'perfil'        => 'Tabelas',
    		'metadatas'     => array(
    				'ws_ordemLista'         => '3',
    				'ws_style'              => 'col-md-12'
    		)
    );

    $arrPerfil = array('Tabelas');
    $twig->addGlobal('servico', $SERVICO);
    $twig->addGlobal('rastro', $rastro);
    echo $twig->render('form.html.twig', array('perfis' => $arrPerfil, 'campos' => $arrCampos));