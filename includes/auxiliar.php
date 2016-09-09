<?php
$inicio	=	microtime(TRUE);
echo $inicio;
set_time_limit('36000');
$this->dbh = DatabaseConnection::getInstance()->getConnection();

$i	= 10000;
$arrInfo	=	array();
while ( $i < 999999 ){
	@$a	= curlRequest('http://www.comprasnet.gov.br/livre/uasg/Estrutura.asp?coduasg='.$i);
	/*
	x($a,0);
	if ( $i == 10005){
		x($a);
	}
	*/
	if ( !strripos( $a, 'An error occurred') ){
		$a	=	trim($a);
		$a	=	substr($a, 112, -1);
		$a	=	utf8_encode($a);
		$arrInfo =	explode('<br>', $a);
		$strA = $arrInfo[0];
		$cara	= substr( $strA, 22);
		$uasg	= substr( $strA, 0, 5);
		$indiceToRemove	= count( $arrInfo);
/*x($arrInfo);	
		$indiceToRemove--;
		unset($arrInfo[$indiceToRemove]);
*/		
		$controle	=	0;
		$paiDoCara	=	count($arrInfo);
		$paiDoCara--;
		foreach ( $arrInfo as $nome){
			$id	=	UUID::v4();
			if ( isset($arrInfo[$controle])){
				switch ($controle){
					case '2':
						$stmt		=	$this->dbh->prepare("INSERT INTO ing_comprasnet_uasg (id,nome,nome_do_pai,codigo_uasg_relacionado) VALUES (?,?,?,?)");
						$stmt->bindValue(1, $id);
						$stmt->bindValue(2, $arrInfo[2]);
						$stmt->bindValue(3, $arrInfo[1]);
						$stmt->bindValue(4, $uasg);
						$stmt->execute();						
						break;
					case '3':
						$stmt		=	$this->dbh->prepare("INSERT INTO ing_comprasnet_uasg (id,nome,nome_do_pai,codigo_uasg_relacionado) VALUES (?,?,?,?)");
						$stmt->bindValue(1, $id);
						$stmt->bindValue(2, $arrInfo[3]);
						$stmt->bindValue(3, $arrInfo[2]);
						$stmt->bindValue(4, $uasg);
						$stmt->execute();						
						break;
					case '4':
						$stmt		=	$this->dbh->prepare("INSERT INTO ing_comprasnet_uasg (id,nome,nome_do_pai,codigo_uasg_relacionado) VALUES (?,?,?,?)");
						$stmt->bindValue(1, $id);
						$stmt->bindValue(2, $arrInfo[4]);
						$stmt->bindValue(3, $arrInfo[3]);
						$stmt->bindValue(4, $uasg);
						$stmt->execute();
						break;
					case '5':
						$stmt		=	$this->dbh->prepare("INSERT INTO ing_comprasnet_uasg (id,nome,nome_do_pai,codigo_uasg_relacionado) VALUES (?,?,?,?)");
						$stmt->bindValue(1, $id);
						$stmt->bindValue(2, $arrInfo[5]);
						$stmt->bindValue(3, $arrInfo[4]);
						$stmt->bindValue(4, $uasg);
						$stmt->execute();
						break;
					case '1':
						$stmt		=	$this->dbh->prepare("INSERT INTO ing_comprasnet_uasg (id,nome,nome_do_pai,codigo,codigo_uasg_relacionado) VALUES (?,?,?,?,?)");
						$stmt->bindValue(1, $id);
						$stmt->bindValue(2, $cara);
						$stmt->bindValue(3, $arrInfo[$paiDoCara]);
						$stmt->bindValue(4, $uasg);
						$stmt->bindValue(5, $uasg);
						$stmt->execute();
						break;						
				}
			}
			$controle++;
		}
	}
	$i++;
}
x('O processamento teve inicio em '.$inicio. ' e acabou em ' . $fim );