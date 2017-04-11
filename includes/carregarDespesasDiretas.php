<?php
$inicio	=	microtime(TRUE);
set_time_limit('36000');
ini_set('memory_limit', '5000M');
$this->dbh = DatabaseConnection::getInstance()->getConnection();

$path		=	'para_importacao/gastos_diretos/';
$diretorio	=	dir($path);
$arArquivos	=	array();
while ($arquivo = $diretorio-> read() ){
	if ( substr($arquivo, -3, 3) == 'csv'){
		
		$stmt = $this->dbh->prepare("select	* from	ing_gd_gastos_diretos where	nome_do_arquivo = :nomeArq");
		$stmt->bindValue(':nomeArq',		$arquivo);
		$stmt->execute();
		
		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		 
		if ( !isset($rs[0]) ){
			$arArquivos[]	=	$arquivo;
		}		
	}
}
foreach ($arArquivos as $caminhoDoArquivo){
	$linhas				= file($path.$caminhoDoArquivo);
	unset($linhas[0]);
	foreach ( $linhas as $linha ){
		$linha	=	utf8_encode($linha);
		$dados	=	explode('	', $linha);
		
		$id	=	UUID::v4();
		$stmt = $this->dbh->prepare("
				INSERT INTO ing_gd_gastos_diretos (
				id,
				codigo_orgao_superior,
				nome_orgao_superior,
				codigo_orgoo,
				nome_orgao,
				codigo_unidade_gestora,
				nome_unidade_gestora,
				codigo_grupo_despesa,
				nome_grupo_despesa,
				codigo_elemento_despesa,
				nome_elemento_despesa,
				codigo_funcao,
				nome_funcao,
				codigo_subfuncao,
				nome_subfuncao,
				codigoprograma,
				nome_programa,
				codigo_acao,
				nome_acao,
				linguagem_cidada,
				codigo_favorecido,
				nome_favorecido,
				numero_documento,
				gestao_pagamento,
				data_pagamento,
				valor,
				nome_do_arquivo) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$stmt->bindValue(1,  $id);
		$stmt->bindValue(2,$dados[0]);
		$stmt->bindValue(3,$dados[1]);
		$stmt->bindValue(4,$dados[2]);
		$stmt->bindValue(5,$dados[3]);
		$stmt->bindValue(6,$dados[4]);
		$stmt->bindValue(7,$dados[5]);
		$stmt->bindValue(8,$dados[6]);
		$stmt->bindValue(9,$dados[7]);
		$stmt->bindValue(10,$dados[8]);
		$stmt->bindValue(11,$dados[9]);
		$stmt->bindValue(12,$dados[10]);
		$stmt->bindValue(13,$dados[11]);
		$stmt->bindValue(14,$dados[12]);
		$stmt->bindValue(15,$dados[13]);
		$stmt->bindValue(16,$dados[14]);
		$stmt->bindValue(17,$dados[15]);
		$stmt->bindValue(18,$dados[16]);
		$stmt->bindValue(19,$dados[17]);
		$stmt->bindValue(20,$dados[18]);
		$stmt->bindValue(21,$dados[19]);
		$stmt->bindValue(22,$dados[20]);
		$stmt->bindValue(23,$dados[21]);
		$stmt->bindValue(24,$dados[22]);
		$stmt->bindValue(25,$dados[23]);
		$stmt->bindValue(26,$dados[24]);
		$stmt->bindValue(27,$caminhoDoArquivo);
		$stmt->execute();		
	}	
}
$fim = microtime(TRUE);

die('O processamento teve inicio em '.$inicio. ' e acabou em ' . $fim );
