<?php
/**
 * User: ericcomcmudo
 * Date: 15/12/15
 * Time: 10:04
 */

class ItemBibliotecaMetadata extends Base
{
	/**
	 * @param $idItem
	 * @return array
	 */

	function getMetadadosByIdItem($idItem)
	{
		$stmt = $this->dbh->prepare(
				"SELECT *
				FROM tb_itembiblioteca_metadata
				WHERE id_ib = :idItem"
				);
	
		$stmt->bindValue(':idItem', $idItem);
	
		$stmt->execute();
	
		$rsMetadados = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		return $rsMetadados;
	}
	
	/**
	 * @param	$arrMetadata
	 * @param	$id_ib
	 * @return	array
	 */
	
	function criarItemBibliotecaMetadata( $arrMetadata, $id_ib ){
		
		$retorno	=	array();
		$i			=	0;
 
		foreach ( $arrMetadata as $metanome => $valor){
			//desconsiderando os campos erro e tmp_name em caso de metadata de upload de arquivo
			$ignore	=	false;
			switch ( $metanome ){
				case 'error':
					$ignore	=	true;
					break;
				case 'tmp_name':
					$ignore	=	true;
					break;
			}

			if ( !$ignore ){
				$stmt = $this->dbh->prepare("INSERT INTO tb_itembiblioteca_metadata ( id, metanome, valor, id_ib ) VALUES ( ?, ?, ?, ? )");
									
				$tokenMaster = UUID::v4();
				$stmt->bindParam(1,	$tokenMaster);
				$stmt->bindParam(2,	$metanome);
				$stmt->bindParam(3,	$valor);
				$stmt->bindParam(4,	$id_ib);
				$stmt->execute();
	
				$retorno[$i]['id']			=	$tokenMaster;				
				$retorno[$i]['metanome']	=	$metanome;
				$retorno[$i]['valo']		=	$valor;
				$retorno[$i]['id_ib']		=	$id_ib;
	
				$i++;
			}
		}
		return $retorno;		
	}
	
	
}
?>