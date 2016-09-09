<?php 

	require_once "connect.php";
	require_once "UUID.php";

	$servico = $SERVICO['id_tib'];
	$itemBiblioteca		=	new ItemBiblioteca();
	$tpItemBiblioteca	=	new TpItemBiblioteca();
	
	// pegar a tib_pai
	$ib		=	$itemBiblioteca->getById( $_REQUEST['id'] );
	$ibFilhos = $itemBiblioteca->getFilhosByIdPai($_REQUEST['id']);
	$idAnterior = array();
	$anterior = array();
	foreach($ibFilhos as $filho) {
		$idAnterior[$filho['id_tib']] = $filho['id'];
		$anterior[$filho['id_tib']] = $filho['valor'];
	}
	//new dBug($idAnterior);
	//die();
	//logica dos checks marcados / desmarcados
	$templateItemBiblioteca	=	new TpItemBiblioteca();
	$rowsetTemplate = $templateItemBiblioteca->getTemplateByIdTibPai( $ib[0]['id_tib'] );

	$estrutura = array();
	foreach($rowsetTemplate as $campo) {
		$estrutura[$campo['id']] = $campo['metanome'];
	}
	//new dBug($estrutura);
	$arrChecks	=	array();
	foreach ( $rowsetTemplate as $key => $template ){
		if ( $template['tipo'] == 'checkbox'){
			$arrChecks[$template['id']]	=	$template;
		}
	}
	$itemBiblioteca = new ItemBiblioteca();

	//$dbh->beginTransaction();
	try{
		foreach ( $_REQUEST as $chave	=> $item){

			$a = explode('_',$chave);
			if ( isset($a[1]) ){
				if ( $a[1] == 'boolean' ){
					$item = '1';
					//removendo check marcado do controle
					//$arrItem	=	$itemBiblioteca->getById($a[0]);
					unset( $arrChecks[ $arrItem[0]['id_tib'] ]);
					$queryTbItemBilbioteca	= $dbh->prepare("UPDATE	tb_itembiblioteca SET valor = :valor WHERE id = :id");
					$queryTbItemBilbioteca->bindParam('valor',		$item);
					$queryTbItemBilbioteca->bindParam('id',			$idAnterior[$a[0]]);
					$queryTbItemBilbioteca->execute();
					//echo 'Valor: ' . $item . ' Ib: ' . $idAnterior[$a[0]];
				}else{
					$queryTbItemBilbioteca	= $dbh->prepare("UPDATE	tb_itembiblioteca SET valor = :valor WHERE id = :id");
					$queryTbItemBilbioteca->bindParam('valor',		$item);
					$queryTbItemBilbioteca->bindParam('id',			$idAnterior[$a[0]]);
					$ret = $queryTbItemBilbioteca->execute();
					//echo 'Valor: ' . $item . ' Ib: ' . $idAnterior[$a[0]] . ' - ' . $ret . '<br>';
				}
			}
		}

		// salvando checks não marcados
//		foreach ( $arrChecks as $key => $check ){
//			$valor	=	'0';
//
//			$queryTbItemBilbioteca	= $dbh->prepare("UPDATE	tb_itembiblioteca SET valor = :valor WHERE id_ib_pai = :id_ib_pai and id_tib = :id_tib");
//			$queryTbItemBilbioteca->bindParam('valor',		$valor);
//			$queryTbItemBilbioteca->bindParam('id_ib_pai',	$irmao[0]['id_ib_pai']);
//			$queryTbItemBilbioteca->bindParam('id_tib',		$check['id']);
//			$queryTbItemBilbioteca->execute();
//		}
		
		//$dbh->commit();
		if (isset($SERVICO['metadata']['ws_target']) && ($SERVICO['metadata']['ws_target'])) {
	        	$servico = new Servico();
	        	$servicoDestino = $servico->getServiceByMetanome($SERVICO['metadata']['ws_target']);
	        } elseif (isset($SERVICO['metadata']['ws_target']) && (!$SERVICO['metadata']['ws_target'])) {
	        	$servicoDestino = $SERVICO['id_pai'];
	        }
        
	        $servicoDestino = current($servicoDestino);

	        if ($servicoDestino) {
	            $flashMsg = new flashMsg();
	            $flashMsg->success('Salvo com sucesso!');
	            parseJsonTarget($servicoDestino);
	        } else {
	            parseJson();
	        }
	}catch( Exception $e ){
	
		$dbh->rollBack();
		var_dump($e);
		echo "error";
	
	}; 
