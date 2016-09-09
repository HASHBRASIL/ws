<?php
set_time_limit('18000');
$this->dbh = DatabaseConnection::getInstance()->getConnection();

$xml_aux = simplexml_load_file('para_importacao/SIORG/Dominios_Estrutura_Organizacional.xml');

foreach ( $xml_aux as $aux){
	$id_aux	=	UUID::v4();
	switch ( $aux->getName()) {
		case 'Tipo_Orgao':
			$stmt = $this->dbh->prepare("
					INSERT INTO ing_gd_siorg_tipo_orgao (
					id,
  					codigo,
  					descricao,
  					indicador_extincao)
					VALUES (?,?,?,?)");
			$stmt->bindValue(1,  $id_aux);
			$stmt->bindValue(2,  $aux->Codigo);
			$stmt->bindValue(3,  $aux->Descricao);
			$stmt->bindValue(4,  $aux->Indicador_Extincao);
			$stmt->execute();
			break;
		case 'Natureza_Juridica':
			$stmt = $this->dbh->prepare("
					INSERT INTO ing_gd_siorg_natureza_juridica (
					id,
  					codigo,
  					descricao,
  					indicador_extincao)
					VALUES (?,?,?,?)");
			$stmt->bindValue(1,  $id_aux);
			$stmt->bindValue(2,  $aux->Codigo);
			$stmt->bindValue(3,  $aux->Descricao);
			$stmt->bindValue(4,  $aux->Indicador_Extincao);
			$stmt->execute();				
			break;
		case 'Orgao_Topo':
			$stmt = $this->dbh->prepare("
					INSERT INTO ing_gd_siorg_orgao_topo (
					id,
  					codigo,
					nome,
					indicador_poder,
					indicador_esfera,
					codigo_principal,
					codigo_auxiliar)
					VALUES (?,?,?,?,?,?,?)");
			$stmt->bindValue(1,  $id_aux);
			$stmt->bindValue(2,  $aux->Codigo);
			$stmt->bindValue(3,  $aux->Nome);
			$stmt->bindValue(4,  $aux->Indicador_Poder);
			$stmt->bindValue(5,  $aux->Indicador_Esfera);
			$stmt->bindValue(6,  $aux->Codigo_Principal);
			$stmt->bindValue(7,  $aux->Codigo_Auxiliar);
			$stmt->execute();
			break;
	}
}

$xml = simplexml_load_file('para_importacao/SIORG/Estrutura_Organizacional.xml');
$i	= 0;
foreach ( $xml->Orgao as $orgao ){
	
	
	$codigo_competencia = '';
	$desc_competencia = '';
	$indx	=	0;
	foreach ( $orgao->Competencia as $a ){
		if ( $indx != 0 ){
			$codigo_competencia .= ' | ';
			$desc_competencia .= ' | ';
		}
		$indx++;
		$codigo_competencia .= $a->Codigo;
		$desc_competencia .= $a->Descricao;
	}
	
	$id	=	UUID::v4();
	$stmt = $this->dbh->prepare("
			INSERT INTO ing_gd_siorg (
			id,
  			dados_cadastro_codigo,
  			dados_cadastro_nome,
  			dados_cadastro_texto_origem,
  			dados_cadastro_sigla,
  			dados_cadastro_codigo_tipo_orgao,
 			dados_cadastro_codigo_natureza_juridica,
  			dados_cadastro_codigo_pai,
  			dados_cadastro_codigo_orgao_topo,
  			dados_cadastro_ddd,
  			dados_cadastro_telefones,
  			dados_cadastro_fax,
  			dados_cadastro_telex,
  			dados_cadastro_email,
  			dados_cadastro_site,
  			dados_cadastro_indicador_organizacao,
  			dados_cadastro_indicador_extincao,
  			dados_cadastro_observacoes,
  			competencia_codigo,
  			competencia_descricao,
  			finalidade_codigo,
  			finalidade_descricao,
  			localidade_nome_pais,
  			localidade_nome_uf,
  			localidade_sigla_uf,
  			localidade_nome_cidade,
  			localidade_descricao_bairro,
  			localidade_descricao_endereco,
  			localidade_descricao_complemento,
  			localidade_numero_cep,
  			base_legal_codigo,
  			base_legal_numero,
  			base_legal_observacao,
  			base_legal_codigo_tipo,
  			base_legal_descricao_tipo,
  			base_legal_data,
  			base_legal_data_publicacao,
  			base_legal_codigo_orgao_criador
			)
		    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bindValue(1,  $id);
	$stmt->bindValue(2,  $orgao->Dados_Cadastro->Codigo);
	$stmt->bindValue(3,  $orgao->Dados_Cadastro->Nome);
	$stmt->bindValue(4,  $orgao->Dados_Cadastro->Texto_Origem);
	$stmt->bindValue(5,  $orgao->Dados_Cadastro->Sigla);
	$stmt->bindValue(6,  $orgao->Dados_Cadastro->Codigo_Tipo_Orgao);
	$stmt->bindValue(7,  $orgao->Dados_Cadastro->Codigo_Natureza_Juridica);
	$stmt->bindValue(8,  $orgao->Dados_Cadastro->Codigo_Pai);
	$stmt->bindValue(9,  $orgao->Dados_Cadastro->Codigo_Orgao_Topo);
	$stmt->bindValue(10,  $orgao->Dados_Cadastro->DDD);
	$stmt->bindValue(11,  $orgao->Dados_Cadastro->Telefones);
	$stmt->bindValue(12,  $orgao->Dados_Cadastro->Fax);
	$stmt->bindValue(13,  $orgao->Dados_Cadastro->Telex);
	$stmt->bindValue(14,  $orgao->Dados_Cadastro->Email);
	$stmt->bindValue(15,  $orgao->Dados_Cadastro->Site);
	$stmt->bindValue(16,  $orgao->Dados_Cadastro->Indicador_Organizacao);
	$stmt->bindValue(17,  $orgao->Dados_Cadastro->Indicador_Extincao);
	$stmt->bindValue(18,  $orgao->Dados_Cadastro->Observacoes);
	$stmt->bindValue(19,  $codigo_competencia);
	$stmt->bindValue(20,  $desc_competencia);
	$stmt->bindValue(21,  $orgao->Finalidade->Codigo);
	$stmt->bindValue(22,  $orgao->Finalidade->Descricao);
	$stmt->bindValue(23,  $orgao->Localidade->Nome_Pais);
	$stmt->bindValue(24,  $orgao->Localidade->Nome_UF);
	$stmt->bindValue(25,  $orgao->Localidade->Sigla_UF);
	$stmt->bindValue(26,  $orgao->Localidade->Nome_Cidade);
	$stmt->bindValue(27,  $orgao->Localidade->Descricao_Bairro);
	$stmt->bindValue(28,  $orgao->Localidade->Descricao_Endereco);
	$stmt->bindValue(29,  $orgao->Localidade->Descricao_Complemento);
	$stmt->bindValue(30,  $orgao->Localidade->Numero_CEP);
	$stmt->bindValue(31,  $orgao->Base_Legal->Codigo);
	$stmt->bindValue(32,  $orgao->Base_Legal->Numero);
	$stmt->bindValue(33,  $orgao->Base_Legal->Observacao);
	$stmt->bindValue(34,  $orgao->Base_Legal->Codigo_Tipo);
	$stmt->bindValue(35,  $orgao->Base_Legal->Descricao_Tipo);
	$stmt->bindValue(36,  $orgao->Base_Legal->Data);
	$stmt->bindValue(37,  $orgao->Base_Legal->Data_Publicacao);
	$stmt->bindValue(38,  $orgao->Base_Legal->Codigo_Orgao_Criador);
	$stmt->execute();
	
	$i++;
}