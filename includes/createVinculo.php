<?php

$pathRastro = new Rastro();
$rastro = $pathRastro->getPath($SERVICO['id']);

$qryCls = $dbh->prepare("select id,nome as valor from tb_classificacao order by nome");
$qryCls->execute();
$cls = $qryCls->fetchAll(PDO::FETCH_ASSOC);
$arrCampos = array();
$arrCampos['Vínculo'][0] = array(
		'id' 		=> 'pessoa',
		'ordem' 	=> '0',
		'obrigatorio' 	=> true,
		'multiplo' 	=> false,
		'nome' 		=> 'Pessoa',
		'descricao' 	=> 'Nome da Pessoa',
		'metanome'	=> 'PESSOA',
		'tipo'		=> 'ref_itemBiblioteca',
		'perfil'	=> 'Vínculo',
		'metadatas'	=> array(
			'ws_ordemLista'		=> '0',
			'ws_style'		=> 'col-md-8',
		)
);
$arrCampos['Vínculo'][1] = array(
                'id'            => 'pessoa',
                'ordem'         => '0',
                'obrigatorio'   => true,
                'multiplo'      => false,
                'nome'          => 'Classificação',
                'descricao'     => 'Classificação a ser utilizada',
                'metanome'      => 'CLASSIF',
                'tipo'          => 'ref_itemBiblioteca',
                'perfil'        => 'Vínculo',
		'items'		=> $cls,
                'metadatas'     => array(
                        'ws_ordemLista'         => '1',
                        'ws_style'              => 'col-md-4',
                        'ws_style_object'	=> 'select2-skin'
                )
);

$arrPerfil = array('Vínculo');
$twig->addGlobal('servico', $SERVICO);
$twig->addGlobal('rastro', $rastro);
echo $twig->render('form.html.twig', array('perfis' => $arrPerfil, 'campos' => $arrCampos));
