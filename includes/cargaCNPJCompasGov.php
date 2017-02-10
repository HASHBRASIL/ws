<?php

require_once "importacaoDeDados.php";

set_time_limit('18000');
$this->dbh = DatabaseConnection::getInstance()->getConnection();

$offSet			=	0;
$incremento		=	500;
$objPessoa		=	new Pessoa();
$objInformacao	=	new Informacao();
$objTpInfo		=	new TpInformacao();
$objRlVincPes	=	new RlVinculoPessoa();
$objGrupo		=	new Grupo();
$grupo		=	$objGrupo->getGrupoByMetanome( 'HASH' );
while ( $offSet < 333977 )
{
	$xml	=	simplexml_load_file('http://compras.dados.gov.br/fornecedores/v1/fornecedores.xml?offset='.$offSet);
	$i		=	0;
	foreach ( $xml as $fornecedor) {
		if ( $i == 0){
			// não faz nada, cabeçalho do XML
		} else {
			
			//if (isset($fornecedor->resource->cnpj) || isset($fornecedor->resource->cpf)){
			if( isset($fornecedor->resource->cnpj) ){
				$id_pessoa = $objInformacao->CPFouCNPJJaCadastrado((string)$fornecedor->resource->cnpj, null, true);
			} 
			
			if( isset($fornecedor->resource->cpf) ){
				$id_pessoa = $objInformacao->CPFouCNPJJaCadastrado((string)$fornecedor->resource->cpf, true, true);
			}

			if ( is_bool( $id_pessoa ) === true || !isset( $id_pessoa ) ){
				$id_pessoa	=	UUID::v4();
				$objPessoa->criaPessoa($id_pessoa, (string)$fornecedor->resource->nome);
				if( isset($fornecedor->resource->cnpj) ) {
					$objInformacao->ciarNova($objTpInfo->getByMetanome('CNPJ'), $id_pessoa, (string)$fornecedor->resource->cnpj, 'TbInformacao');
					$objRlVincPes->criarVinculo('ENTIDADE', $id_pessoa, $grupo['id']);
				}
				if( isset($fornecedor->resource->cpf) ) {
					$objInformacao->ciarNova($objTpInfo->getByMetanome('CPF'), $id_pessoa, (string)$fornecedor->resource->cpf, 'TbInformacao');
					$objRlVincPes->criarVinculo('CONTATO,PF', $id_pessoa, $grupo['id']);
				}
			}
			
			$objInformacao->ciarNova($objTpInfo->getByMetanome('OFFSETCOMPRASGOV'), $id_pessoa, $offSet,'TbInformacao');

			if( $info	=	$objInformacao->informacaoJaCadastrada($objTpInfo->getByMetanome('UF'), $id_pessoa, true ) ){
				if( isset($fornecedor->resource->uf) ) {$objInformacao->updateValor($info['id'], (string)$fornecedor->resource->uf); }
			} else {
				if( isset($fornecedor->resource->uf) ) {$objInformacao->ciarNova($objTpInfo->getByMetanome('UF'), $id_pessoa, (string)$fornecedor->resource->uf, 'TbInformacao'); }
			}
			
			if( $info	=	$objInformacao->informacaoJaCadastrada($objTpInfo->getByMetanome('CODCNAE'), $id_pessoa, true ) ){
				if ( isset($fornecedor->resource->id_cnae)){
					if ( !$objInformacao->informacaoComValorJaCadastrado($objTpInfo->getByMetanome('CODCNAE'), $id_pessoa, $fornecedor->resource->id_cnae) ) {
						$objInformacao->ciarNova($objTpInfo->getByMetanome('CODCNAE'), $id_pessoa, (string)$fornecedor->resource->id_cnae, 'TbInformacao');
					}
				}
				if ( isset($fornecedor->resource->id_cnae2)){
					if ( !$objInformacao->informacaoComValorJaCadastrado($objTpInfo->getByMetanome('CODCNAE'), $id_pessoa, $fornecedor->resource->id_cnae2) ) {
						$objInformacao->ciarNova($objTpInfo->getByMetanome('CODCNAE'), $id_pessoa, (string)$fornecedor->resource->id_cnae2, 'TbInformacao');
					}
				}
			} else {
				if ( isset($fornecedor->resource->id_cnae)){$objInformacao->ciarNova($objTpInfo->getByMetanome('CODCNAE'), $id_pessoa, (string)$fornecedor->resource->id_cnae, 'TbInformacao');}
				if ( isset($fornecedor->resource->id_cnae2)){$objInformacao->ciarNova($objTpInfo->getByMetanome('CODCNAE'), $id_pessoa, (string)$fornecedor->resource->id_cnae2, 'TbInformacao');}
			}
			
			if( $info	=	$objInformacao->informacaoJaCadastrada($objTpInfo->getByMetanome('STATUSATIVOCOMPRASGOV'), $id_pessoa, true ) ){
				if( isset($fornecedor->resource->ativo) ) { $objInformacao->updateValor($info['id'], (string)$fornecedor->resource->ativo); }
			} else {
				if( isset($fornecedor->resource->ativo) ) { $objInformacao->ciarNova($objTpInfo->getByMetanome('STATUSATIVOCOMPRASGOV'), $id_pessoa, (string)$fornecedor->resource->ativo, 'TbInformacao'); }
			}
			
			if( $info	=	$objInformacao->informacaoJaCadastrada($objTpInfo->getByMetanome('RECADASTRADONOCOMPRASGOV'), $id_pessoa, true ) ){
				if( isset($fornecedor->resource->recadastrado) ) { $objInformacao->updateValor($info['id'], (string)$fornecedor->resource->recadastrado); }
			} else {
				if( isset($fornecedor->resource->recadastrado) ) { $objInformacao->ciarNova($objTpInfo->getByMetanome('RECADASTRADONOCOMPRASGOV'), $id_pessoa, (string)$fornecedor->resource->recadastrado, 'TbInformacao'); }
			}
			
			if( $info	=	$objInformacao->informacaoJaCadastrada($objTpInfo->getByMetanome('CODMUNICIPIO'), $id_pessoa, true ) ){
				if( isset($fornecedor->resource->id_municipio) ) { $objInformacao->updateValor($info['id'], (string)$fornecedor->resource->id_municipio); }
			} else {
				if( isset($fornecedor->resource->id_municipio) ) { $objInformacao->ciarNova($objTpInfo->getByMetanome('CODMUNICIPIO'), $id_pessoa, (string)$fornecedor->resource->id_municipio, 'TbInformacao'); }
			}
			
			if( $info	=	$objInformacao->informacaoJaCadastrada($objTpInfo->getByMetanome('CODUNIDADECADASTRADORA'), $id_pessoa, true ) ){
				if( isset($fornecedor->resource->id_unidade_cadastradora) ) { $objInformacao->updateValor($info['id'], (string)$fornecedor->resource->id_unidade_cadastradora); }
			} else {
				if( isset($fornecedor->resource->id_unidade_cadastradora) ) { $objInformacao->ciarNova($objTpInfo->getByMetanome('CODUNIDADECADASTRADORA'), $id_pessoa, (string)$fornecedor->resource->id_unidade_cadastradora, 'TbInformacao'); }
			}
			
			if( $info	=	$objInformacao->informacaoJaCadastrada($objTpInfo->getByMetanome('STATUSHABILITADOALICITAR'), $id_pessoa, true ) ){
				if( isset($fornecedor->resource->habilitado_licitar) ) { $objInformacao->updateValor($info['id'], (string)$fornecedor->resource->habilitado_licitar); }
			} else {
				if( isset($fornecedor->resource->habilitado_licitar) ) { $objInformacao->ciarNova($objTpInfo->getByMetanome('STATUSHABILITADOALICITAR'), $id_pessoa, (string)$fornecedor->resource->habilitado_licitar, 'TbInformacao'); }
			}
								
			// atualiza a informacao cadastrada e adiciona as demais
			foreach ( $fornecedor->resource->_links->link as $link){
				switch ((string)$link['rel']) {
					case 'ramo_negocio':
						if( $info	=	$objInformacao->informacaoJaCadastrada($objTpInfo->getByMetanome('RAMODENEGOCIO'), $id_pessoa, true ) ){
							if ( isset($fornecedor->resource->id_ramo_negocio)){ $objInformacao->updateValor($info['id'], (string)$link['title']); }
						} else {
							if ( isset($fornecedor->resource->id_ramo_negocio)){ $objInformacao->ciarNova($objTpInfo->getByMetanome('RAMODENEGOCIO'), $id_pessoa, (string)$link['title'], 'TbInformacao'); }
						}
						break;
					case 'porte_empresa':
						if( $info	=	$objInformacao->informacaoJaCadastrada($objTpInfo->getByMetanome('PORTEDAEMPRESA'), $id_pessoa, true ) ){
							if ( isset($fornecedor->resource->id_porte_empresa)){ $objInformacao->updateValor($info['id'], (string)$link['title']); }
						} else {
							if ( isset($fornecedor->resource->id_porte_empresa)){ $objInformacao->ciarNova($objTpInfo->getByMetanome('PORTEDAEMPRESA'), $id_pessoa, (string)$link['title'], 'TbInformacao'); }
						}
						break;
					case 'natureza_juridica':
						if( $info	=	$objInformacao->informacaoJaCadastrada($objTpInfo->getByMetanome('NATJURCOMPRASGOV'), $id_pessoa, true ) ){
							if ( isset($fornecedor->resource->id_natureza_juridica)){ $objInformacao->updateValor($info['id'], (string)$link['title']); }
						} else {
							if ( isset($fornecedor->resource->id_natureza_juridica)){ $objInformacao->ciarNova($objTpInfo->getByMetanome('NATJURCOMPRASGOV'), $id_pessoa, (string)$link['title'], 'TbInformacao'); }
						}
						break;
				}
			}						
		}				
		$i++;
	}	
	$offSet	=	$offSet + $incremento;				
}