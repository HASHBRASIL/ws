<?php

/**
 * User: eric
 * Date: 28/01/16
 * Time: 17:00
 */
class Classificacao extends Base
{
	function pegarClassificacaoPorMetanome( $metanome )	{
		$stmt = $this->dbh->prepare("SELECT * FROM tb_classificacao WHERE metanome = ?");
		$stmt->bindValue(1,	$metanome);
		
		$stmt->execute();
	
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
}