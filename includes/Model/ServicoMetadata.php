<?php

/**
 * User: ericcomcmudo
 * 
 * Date: 4/01/16
 * Time: 18:45
 */
class ServicoMetadata extends Base
{
    function getMetadataByServico($servico)
    {
    	$stmt = $this->dbh->prepare(
    			"select * from tb_servico_metadata where id_servico = :servico"
    			);
    	
    	$stmt->bindValue("servico", $servico);
    	
    	$stmt->execute();
        
    	$arrMeta = $stmt->fetchAll(PDO::FETCH_ASSOC);
    	
    	return $arrMeta;
    }
}