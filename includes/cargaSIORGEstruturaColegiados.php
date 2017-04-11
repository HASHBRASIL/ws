<?php
set_time_limit('18000');
$this->dbh = DatabaseConnection::getInstance()->getConnection();

$xml_aux = simplexml_load_file('para_importacao/SIORG/Estrutura_Organizacional_Colegiados.xml');

foreach ( $xml_aux as $aux){
	switch ( $aux->getName()) {
		case 'Colegiado':			
			$stmt = $this->dbh->prepare("
					INSERT INTO ing_gd_siorg_colegiado (
					codigo,
					nome)
					VALUES (?,?)");
			$stmt->bindValue(1,  $aux->Codigo);
			$stmt->bindValue(2,  $aux->Nome);
			$stmt->execute();
			foreach ( $aux->Componente as $componente){
				$stmt = $this->dbh->prepare("
					INSERT INTO ing_gd_siorg_colegiado_componente (
					codigo,
					nome,
					codigo_colegiado)
					VALUES (?,?,?)");
				$stmt->bindValue(1,  $componente->Codigo);
				$stmt->bindValue(2,  $componente->Nome);
				$stmt->bindValue(3,  $aux->Codigo);
				$stmt->execute();				
			}
			break;
	}
}