<?php
$inicio	=	microtime(TRUE);
echo $inicio;
set_time_limit('36000');
ini_set('memory_limit', '5000M');
$this->dbh = DatabaseConnection::getInstance()->getConnection();

$path		=	'para_importacao/dne/';
$diretorio	=	dir($path);
$arArquivos	=	array();
$i			=	0;
while ($arquivoDNE = $diretorio-> read() ){
	if ( substr($arquivoDNE, -3, 3) == 'TXT'){
			$arArquivos[$i]['caminho']	=	$path.$arquivoDNE;
			if( substr($arquivoDNE, 0, -7) == 'LOG_LOGRADOURO' ){
				$arArquivos[$i]['tabela']	=	'dne_'.strtolower(substr($arquivoDNE, 0, -7));
			} else {
				$arArquivos[$i]['tabela']	=	'dne_'.strtolower(substr($arquivoDNE, 0, -4));
			}
			$i++;
	}	
}
unset($i);

$inserts	=	array();
foreach ( $arArquivos as $arquivo ){
	$stmt		=	$this->dbh->prepare("TRUNCATE ".$arquivo['tabela']);
	$stmt->execute();
}

foreach ( $arArquivos as $arquivo ){
	$linhas	=	file( $arquivo['caminho']);
	
	foreach ( $linhas as $linha ){
		$linha		=	trim($linha);
		$linha		=	substr($linha, 0, -1);
		$linha		=	utf8_encode($linha);
		$campos		=	explode('@', $linha);
		$strinsert	=	'';
		foreach ( $campos as $campo ){
				$strinsert	.= '?,';
			}
		$stmt		=	$this->dbh->prepare("INSERT INTO " . $arquivo['tabela'] . " VALUES (" . substr($strinsert, 0, -1) . ")");
		
		$i			=	1;
		foreach ( $campos as $campo){
			$stmt->bindValue($i++,$campo);
		}
		$stmt->execute();	
	}
}
$fim = microtime(TRUE);
x('O processamento teve inicio em '.$inicio. ' e acabou em ' . $fim );