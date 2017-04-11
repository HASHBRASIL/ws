<?php

/**
 * User: eric
 * Date: 28/01/16
 * Time: 17:00
 */
class RlVinculoPessoa extends Base
{
	function criarVinculo($arClassificacao, $id_pessoa, $id_grupo, $id_vinculado = NULL)	{

		$arRetorno	=	array();
		foreach ( explode(',' , $arClassificacao) as $id => $classificacao) {
			$objClassificacao	=	new Classificacao();
			$rsClassificacao	=	$objClassificacao->pegarClassificacaoPorMetanome( $classificacao );
			
			$uuidVinculo	=	UUID::v4();
			
			$i = 1;
			if ( is_null($id_vinculado )){
				$stmt	=	$this->dbh->prepare("INSERT INTO rl_vinculo_pessoa (id, id_classificacao, id_pessoa, id_grupo) VALUES (?,?,?,?)");			
			} else {
				$stmt	=	$this->dbh->prepare("INSERT INTO rl_vinculo_pessoa (id_vinculado, id, id_classificacao, id_pessoa, id_grupo) VALUES (?,?,?,?,?)");
				$stmt->bindValue($i++,		$id_vinculado);
			}	
			
			$stmt->bindValue($i++,	$uuidVinculo);
			$stmt->bindValue($i++,	$rsClassificacao['id']);
			$stmt->bindValue($i++,	$id_pessoa);
			$stmt->bindValue($i++,	$id_grupo);
			$stmt->execute();
			
			$arRetorno[ $id ]	=	$uuidVinculo;	
		}
		
		return $arRetorno;
	}
	
}