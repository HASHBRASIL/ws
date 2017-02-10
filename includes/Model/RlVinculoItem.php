<?php

/**
 * User: eric
 * Date: 19/01/16
 * Time: 18:30
 */
class RlVinculoItem extends Base
{
	function criarVinculo( $id_ib_principal, $id_ib_vinculado)	{
			
			$id		=	 UUID::v4();
			
			$stmt	=	$this->dbh->prepare("INSERT INTO rl_vinculo_item (id, id_ib_principal, id_ib_vinculado) VALUES (?,?,?)");			
			$stmt->bindValue('1',	$id);
			$stmt->bindValue('2',	$id_ib_principal);
			$stmt->bindValue('3',	$id_ib_vinculado);
			$stmt->execute();

		return $id;
	}
	
}