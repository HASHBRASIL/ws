<?php

/**
 * Created by PhpStorm.
 * User: solbisio
 * Date: 9/12/15
 * Time: 17:57
 */
class Servico extends Base
{
    function getServiceByMetanome($metanome)
    {
        $stmt = $this->dbh->prepare(
            "select id from tb_servico where metanome = :metanome"
        );

        $stmt->bindValue("metanome", $metanome);

        $stmt->execute();
        $idServico = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $idServico;
    }

	/**
	  * User: ericcomcmudo
	 * Date: 04/01/16
	 * Time: 17:46
	 */
	
	function getFilhosById($id_pai)
	{
		$stmt = $this->dbh->prepare(
				"select * from tb_servico where id_pai = :id_pai"
				);
	
		$stmt->bindValue("id_pai", $id_pai);
	
		$stmt->execute();
		$arrFilhos = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $arrFilhos;
	}
	
	function pegaPai($id) {
		$stmt = $this->dbh->prepare(
				"select	*
				from	tb_servico
				where	id in (select	id_pai from	tb_servico where	id = :id)"
				);
		
		$stmt->bindValue("id", $id);
		$stmt->execute();
		
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
}