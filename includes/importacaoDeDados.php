<?php
/**
 * User: ericcomcmudo
 * Date: 20/01/15
 * Time: 14:36
 */

class importacaoDeDados extends Base
{
 
    	function transacaoBancariaOFXJaExistente($fitid, $checknum){
    		$stmt = $this->dbh->prepare("
    				select	*
					from	tb_itembiblioteca ib
					inner	join	tb_itembiblioteca ib2	on	ib2.id_ib_pai	=	ib.id_ib_pai
					where	ib.valor	=	:FITID
					and	ib.id_tib in 
						(
						select	id
						from	tp_itembiblioteca
						where	metanome	=	'FITID'
						)
					and	ib2.valor	=	:CHECKNUM
					and	ib2.id_tib	in
						(
						select	id
						from	tp_itembiblioteca
						where	metanome	=	'CHECKNUM'
						)");
    		$stmt->bindValue(':FITID',		$fitid);
    		$stmt->bindValue(':CHECKNUM',	$checknum);
    		$stmt->execute();
    		
    		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    		 
    		if ( isset($rs[0]) ){    			
    			return true;
    		} else {
    			return false; 
    		}
    	}
    	
		function arquivoOFXJaImportado( $fileofxsize, $dtserver, $retornarId = null ){
    		$stmt = $this->dbh->prepare("
    				select	*
					from	tb_itembiblioteca ib
					inner	join	tb_itembiblioteca ib2	on	ib2.id_ib_pai	=	ib.id_ib_pai
					where	ib.valor	=	:DTSERVER
					and	ib.id_tib in
						(
						select	id
						from	tp_itembiblioteca
						where	metanome	=	'DTSERVER'
						)
					and	ib2.valor	=	:FILEOFXSIZE
					and	ib2.id_tib	in
						(
						select	id
						from	tp_itembiblioteca
						where	metanome	=	'FILEOFXSIZE'
						)");
    		$stmt->bindValue(':DTSERVER',		$dtserver);
    		$stmt->bindValue(':FILEOFXSIZE',	$fileofxsize);
    		$stmt->execute();
    		
    		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
   		 
    		if ( is_null( $retornarId ) ) {
	    		if ( isset($rs[0]) ){
	    			return true;
	    		} else {
	    			return false;
	    		}
    		} else {
    			return $rs[0]['id'];
    		}
    	}
    	
    	function bancoJaCadastrado( $idBanco, $retornarId = NULL){
    		
    		$stmt = $this->dbh->prepare("
    				select	*
		    		from	tb_itembiblioteca
		    		where	valor = :BANKID and
    				id_tib in
		    		(
		    				select	id
		    				from	tp_itembiblioteca
		    				where	metanome	= 'BANKID'	
    				)");
    		$stmt->bindValue(':BANKID',	$idBanco);
    		$stmt->execute();
    		
    		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    		
    		if ( is_null( $retornarId ) ){
	    		if ( isset($rs[0]) ){
	    			return true;
	    		} else {
	    			return false;
	    		}
    		} else {
    			return $rs[0]['id_ib_pai'];
    		}
    	}
    	
    	function agenciaJaCadastrada( $numeroDaAgencia, $idBanco, $retornarId = NULL ){
    		
    		$idBanco = intval($idBanco);
    		$idBanco = (string) $idBanco;
    		
    		$stmt = $this->dbh->prepare("
    				select	id_ib_vinculado
					from	rl_vinculo_item
					where	id_ib_principal in
					(
						select	id_ib_pai
						from	tb_itembiblioteca
						where	id_tib in
						(
							select	id	
							from	tp_itembiblioteca
							where	metanome	= 'BANKID'
						)
						and	valor = :BANKID
					)
					and	id_ib_vinculado in 
					(
						select	id_ib_pai
						from	tb_itembiblioteca
						where	id_tib in
						(
							select	id	
							from	tp_itembiblioteca
							where	metanome	= 'BRANCHID'
						)
						and	valor = :BRANCHID
					)");
    		$stmt->bindValue(':BANKID',	$idBanco);
    		$stmt->bindValue(':BRANCHID',	$numeroDaAgencia);
    		$stmt->execute();
    		
    		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    		if ( is_null($retornarId) ){
    			if ( isset($rs[0]) ){
    				return true;
    			} else {
    				return false;
    			}
    		} else {    			
    			return $rs[0]['id_ib_vinculado'];    			
    		}
    		
    	}
    	
    	function contaJaCadastrada( $numeroDaConta, $numeroAgencia, $numeroDoBanco, $retornarId = NULL ){
    		$numeroDoBanco = intval($numeroDoBanco);
    		$numeroDoBanco = (string) $numeroDoBanco;
    		
    		$stmt = $this->dbh->prepare("
    				select	id_ib_vinculado
					from	rl_vinculo_item
					where	id_ib_principal in
					(
						select	id_ib_pai
						from	tb_itembiblioteca
						where	id_tib in
						(
							select	id	
							from	tp_itembiblioteca
							where	metanome	= 'BRANCHID'
						)
						and	valor = :BRANCHID
					)
					and	id_ib_vinculado in 
					(
						select	id_ib_pai
						from	tb_itembiblioteca
						where	id_tib in
						(
							select	id	
							from	tp_itembiblioteca
							where	metanome	= 'ACCTID'
						)
						and	valor = :ACCTID
					)
					union
					select	id_ib_vinculado
					from	rl_vinculo_item
					where	id_ib_principal in
					(
						select	id_ib_pai
						from	tb_itembiblioteca
						where	id_tib in
						(
							select	id	
							from	tp_itembiblioteca
							where	metanome	= 'BANKID'
						)
						and	valor = :BANKID
					)
					and	id_ib_vinculado in 
					(
						select	id_ib_pai
						from	tb_itembiblioteca
						where	id_tib in
						(
							select	id	
							from	tp_itembiblioteca
							where	metanome	= 'BRANCHID'
						)
						and	valor = :BRANCHID2
					)");
    		
    		$stmt->bindValue(':BRANCHID',	$numeroAgencia);
    		$stmt->bindValue(':BRANCHID2',	$numeroAgencia);
    		$stmt->bindValue(':ACCTID',		$numeroDaConta);
    		$stmt->bindValue(':BANKID',		$numeroDoBanco);
    		$stmt->execute();

    		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    		if ( is_null($retornarId) ){
    			if ( isset( $rs[0]) ){
    				return true;
    			} else {
    				return false;
    			}
    		} else {
    			return $rs[0]['id_ib_vinculado'];
    		}
    	}
    	
    	function buscarInformacoesDeContaDoERP( $conta, $idBanco ){
    		$tam	=	stripos( $conta, "-" );
    		$numeroDaConta	=	substr( $conta, 0, $tam );
    		$digConta		=	substr( $conta, -1);
   		
    		$stmt = $this->dbh->prepare("    				
					select *
					from	fin_tb_contas conta
					inner	join fin_tb_bancos as banco on banco.bco_id = conta.bco_id
					where	conta.con_numero = :CONNUM
					and	conta.con_digito = :CONDIG
					and	banco.bco_comp = :BANKID ");
    		$stmt->bindValue(':CONNUM',	$numeroDaConta);
    		$stmt->bindValue(':CONDIG',	$digConta);
    		$stmt->bindValue(':BANKID',	$idBanco);
    		$stmt->execute();
    		
    		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    		
    		return $rs[0];
    	}
    	
     	function  importOFX( $linhas , $caminhoArquivo ) {

    		set_time_limit('1200');
    		    		   		    		
    		$arryCabecalho			=	array();
    		$arryNovasTransacoes	=	array();    		
    		$contador				=	0;
    		foreach ( $linhas as $linha ){
    		
    			$linha = trim( $linha );
    			
    			if ( substr($linha, 0,1) == "<" ){
    				$tamTag	=	stripos( $linha, ">" );
    				$tamTag++;
    				$tag = substr( $linha, 0, $tamTag );

    				$tag = str_replace('>', '', $tag);
    				$tag = str_replace('<', '', $tag);
    				
    				$conteudoDaTag	=	str_replace('<'.$tag.'>', '', $linha);
    				$conteudoDaTag	=	str_replace('</'.$tag.'>', '', $conteudoDaTag);
    				
    				$tag = utf8_encode($tag);
    				$conteudoDaTag = utf8_encode($conteudoDaTag);
    				
    				switch ( $tag ){
    					case 'CURDEF':
    						$arryCabecalho['CURDEF']	=	$conteudoDaTag;
    						break;
    					case 'DTSERVER':
    						$arryCabecalho['DTSERVER']	=	$conteudoDaTag;
    						break;
   						case 'ORG':
   							$arryCabecalho['ORG']	=	$conteudoDaTag;
   							break;
   						case 'BANKID':
   							$arryCabecalho['BANKID']	=	$conteudoDaTag;
   							break;
						case 'BRANCHID':
							$arryCabecalho['BRANCHID']	=	$conteudoDaTag;
							break;
						case 'ACCTID':
							$arryCabecalho['ACCTID']	=	$conteudoDaTag;
							break;
						case 'STMTTRN':
							$id_master_transacao	=	UUID::v4();
							break;		
						case '/STMTTRN':
							$contador++;
							unset($id_master_transacao);
							break;
						case '/BANKTRANLIST':
							unset($id_master_transacao);																
    				}
    				
    				
    				
    				if ( isset( $id_master_transacao ) ){
						
    					if ( !isset($arryNovasTransacoes[$id_master_transacao]) ){
   							$arryNovasTransacoes[ $contador ][ 'UUID_transacao' ]	=	$id_master_transacao;
    					}
    					
    					switch ( $tag ){
    						case 'MEMO':
    							$arryNovasTransacoes[ $contador ]['MEMO'] = $conteudoDaTag;    							    							
    							break;
    						case 'REFNUM':
    							$arryNovasTransacoes[ $contador ]['REFNUM'] = $conteudoDaTag;
    							break;
   							case 'TRNTYPE':
   								$arryNovasTransacoes[ $contador ]['TRNTYPE'] = $conteudoDaTag;
   								break;
   							case 'CHECKNUM':
  								$arryNovasTransacoes[ $contador ]['CHECKNUM'] = $conteudoDaTag;
   								break;
   							case 'FITID':
   								$arryNovasTransacoes[ $contador ]['FITID'] = $conteudoDaTag;
   								break;
   							case 'DTPOSTED':
   								$arryNovasTransacoes[ $contador ]['DTPOSTED'] = $conteudoDaTag;
   								break;
   							case 'TRNAMT':
   								$arryNovasTransacoes[ $contador ]['TRNAMT'] = $conteudoDaTag; 
    					}
    				}
   					
    			}
    	} 
    	
    	$objItemBiblioteca		=	new ItemBiblioteca();
    	$objTpItemBiblioteca	=	new TpItemBiblioteca();
    	$objRlVinculoItem		=	new RlVinculoItem();
    	
    	if ( !$this->arquivoOFXJaImportado( filesize( 'upload_dir/'.$caminhoArquivo ) , $arryCabecalho['DTSERVER'] ) ) {
    		$id_master_arquivo	=	UUID::v4();
    		$objItemBiblioteca->criarItem($id_master_arquivo, '', $objTpItemBiblioteca->getIdTibByMetanome('OFX') );
    		$objItemBiblioteca->criarItem(UUID::v4(), $caminhoArquivo, $objTpItemBiblioteca->getIdTibByMetanome('CAMINHOARQOFX' ), $id_master_arquivo );
    		$objItemBiblioteca->criarItem(UUID::v4(), filesize( 'upload_dir/'.$caminhoArquivo ), $objTpItemBiblioteca->getIdTibByMetanome('FILEOFXSIZE' ), $id_master_arquivo );
    		$objItemBiblioteca->criarItem(UUID::v4(), $arryCabecalho['CURDEF'], $objTpItemBiblioteca->getIdTibByMetanome('CURDEF' ), $id_master_arquivo );
    		$objItemBiblioteca->criarItem(UUID::v4(), $arryCabecalho['DTSERVER'], $objTpItemBiblioteca->getIdTibByMetanome('DTSERVER' ), $id_master_arquivo );
    	} else {
    		$id_master_arquivo	= $this->arquivoOFXJaImportado( filesize( 'upload_dir/'.$caminhoArquivo ) , $arryCabecalho['DTSERVER'] , true );
    	}
    	
    	if( !$this->bancoJaCadastrado( $arryCabecalho['BANKID'])){
    		$id_master_banco	=	UUID::v4();
    		$objItemBiblioteca->criarItem($id_master_banco, '', $objTpItemBiblioteca->getIdTibByMetanome('BANKACCTFROM' ) );
    		$objItemBiblioteca->criarItem(UUID::v4(), $arryCabecalho['BANKID'], $objTpItemBiblioteca->getIdTibByMetanome('BANKID' ), $id_master_banco );
    		$objItemBiblioteca->criarItem(UUID::v4(), $arryCabecalho['ORG'], $objTpItemBiblioteca->getIdTibByMetanome('ORG' ), $id_master_banco );
    		$objRlVinculoItem->criarVinculo( $id_master_arquivo, $id_master_banco);
    	} else {
    		$id_master_banco	= $this->bancoJaCadastrado( $arryCabecalho['BANKID'] , true );
    	}

    	if ( isset($arryCabecalho['BRANCHID']) ){
	    	if( !$this->agenciaJaCadastrada($arryCabecalho['BRANCHID'], $arryCabecalho['BANKID'])){
		    	$id_master_agencia = 	UUID::v4();
		    	$objItemBiblioteca->criarItem($id_master_agencia, '', $objTpItemBiblioteca->getIdTibByMetanome('AGENCIABANCARIA' ) );
		    	$objItemBiblioteca->criarItem(UUID::v4(), $arryCabecalho['BRANCHID'], $objTpItemBiblioteca->getIdTibByMetanome('BRANCHID' ), $id_master_agencia );
		    	$objRlVinculoItem->criarVinculo($id_master_banco, $id_master_agencia);
	    	} else {
	    		$id_master_agencia = $this->agenciaJaCadastrada($arryCabecalho['BRANCHID'], $arryCabecalho['BANKID'], true);
	    	}
    	} else {
    		$dadosBancariosDoERP	=	$this->buscarInformacoesDeContaDoERP($arryCabecalho['ACCTID'], $arryCabecalho['BANKID']);
    		$id_master_agencia = 	UUID::v4();
    		$objItemBiblioteca->criarItem($id_master_agencia, '', $objTpItemBiblioteca->getIdTibByMetanome('AGENCIABANCARIA' ) );
    		$objItemBiblioteca->criarItem(UUID::v4(), $dadosBancariosDoERP['con_agencia'].'-'.$dadosBancariosDoERP['con_age_digito'], $objTpItemBiblioteca->getIdTibByMetanome('BRANCHID' ), $id_master_agencia );
    		$objRlVinculoItem->criarVinculo($id_master_banco, $id_master_agencia);   		
    	}
    		
    	if ( !$this->contaJaCadastrada( $arryCabecalho['ACCTID'], $arryCabecalho['BRANCHID'], $arryCabecalho['BANKID'] ) ){
    		$id_master_conta = 	UUID::v4();
    		$objItemBiblioteca->criarItem($id_master_conta, '', $objTpItemBiblioteca->getIdTibByMetanome('CONTABANCARIA' ) );
    		$objItemBiblioteca->criarItem(UUID::v4(), $arryCabecalho['ACCTID'], $objTpItemBiblioteca->getIdTibByMetanome('ACCTID' ), $id_master_conta );
    		$objRlVinculoItem->criarVinculo($id_master_agencia, $id_master_conta);
    	} else {    		
    		$id_master_conta = $this->contaJaCadastrada( $arryCabecalho['ACCTID'], $arryCabecalho['BRANCHID'], $arryCabecalho['BANKID'], true);
    	}

    	foreach ( $arryNovasTransacoes as $transacao => $itens ) {
    		if ( !$this->transacaoBancariaOFXJaExistente( $itens['FITID'], $itens['CHECKNUM']) ){
	   			$objItemBiblioteca->criarItem($itens['UUID_transacao'], '', $objTpItemBiblioteca->getIdTibByMetanome('STMTTRN' ) );
    			$objItemBiblioteca->criarItem(UUID::v4(), $itens['MEMO'], $objTpItemBiblioteca->getIdTibByMetanome('MEMO' ), $itens['UUID_transacao'] );
    			$objItemBiblioteca->criarItem(UUID::v4(), $itens['REFNUM'], $objTpItemBiblioteca->getIdTibByMetanome('REFNUM' ), $itens['UUID_transacao'] );
    			$objItemBiblioteca->criarItem(UUID::v4(), $itens['TRNTYPE'], $objTpItemBiblioteca->getIdTibByMetanome('TRNTYPE' ), $itens['UUID_transacao'] );
    			$objItemBiblioteca->criarItem(UUID::v4(), $itens['CHECKNUM'], $objTpItemBiblioteca->getIdTibByMetanome('CHECKNUM' ), $itens['UUID_transacao'] );
    			$objItemBiblioteca->criarItem(UUID::v4(), $itens['FITID'], $objTpItemBiblioteca->getIdTibByMetanome('FITID' ), $itens['UUID_transacao'] );
    			$objItemBiblioteca->criarItem(UUID::v4(), $itens['DTPOSTED'], $objTpItemBiblioteca->getIdTibByMetanome('DTPOSTED' ), $itens['UUID_transacao'] );
    			$objItemBiblioteca->criarItem(UUID::v4(), $itens['TRNAMT'], $objTpItemBiblioteca->getIdTibByMetanome('TRNAMT' ), $itens['UUID_transacao'] );
    			$objRlVinculoItem->criarVinculo($id_master_conta, $itens['UUID_transacao']);
    			unset( $arryNovasTransacoes[ $transacao ]);
    		} else {
    			unset( $arryNovasTransacoes[ $transacao ]);
    		}   			
    	}
    }
    
    function importCNAE( $linhas , $caminhoDoArquivo ){
		
    	set_time_limit('1200');
    	    	
    	$i	=	0;
    	$objItemBiblioteca	=	new ItemBiblioteca();
    	$objTPitem			=	new TpItemBiblioteca();
    	
    	$arrTIBs			=	array();
    	$arrTIBs[]			=	$objTPitem->getIdTibByMetanome('CNAECODSEC');
    	$arrTIBs[]			=	$objTPitem->getIdTibByMetanome('CNAEDESCSEC');
    	$arrTIBs[]			=	$objTPitem->getIdTibByMetanome('CNAECODSUBCLAS');
    	$arrTIBs[]			=	$objTPitem->getIdTibByMetanome('CNAEDESCSUBCLAS');
    	$arrTIBs[]			=	$objTPitem->getIdTibByMetanome('TPCNAE');
    	$objItemBiblioteca->apagarPorTIB( $arrTIBs );
    	
    	foreach ( $linhas as $linha ){
    		
    		if( $i > 0 ){
    			$linha = utf8_encode($linha);
  				$id_master		=	UUID::v4();
    			$arrRegistro	=	split('	', $linha);
    			if ( count($arrRegistro) == 4){
	   				$tpcnae				=	$objTPitem->getIdTibByMetanome('TPCNAE');
	   				$objItemBiblioteca->criarItem($id_master, '', $tpcnae);
	   				
	   				$cnaecodsec			=	$objTPitem->getIdTibByMetanome('CNAECODSEC');
	   				$objItemBiblioteca->criarItem(UUID::v4(), $arrRegistro[0], $cnaecodsec, $id_master);
	   				
	   				$cnaedescsec		=	$objTPitem->getIdTibByMetanome('CNAEDESCSEC');
	   				$objItemBiblioteca->criarItem(UUID::v4(), $arrRegistro[1], $cnaedescsec, $id_master);
	   				
	   				$cnaecodsubclas		=	$objTPitem->getIdTibByMetanome('CNAECODSUBCLAS');
	   				$objItemBiblioteca->criarItem(UUID::v4(), $arrRegistro[2], $cnaecodsubclas, $id_master);
	   				
	   				$cnaedescsubclas	=	$objTPitem->getIdTibByMetanome('CNAEDESCSUBCLAS');
	   				$objItemBiblioteca->criarItem(UUID::v4(), $arrRegistro[3], $cnaedescsubclas, $id_master);
	   				
	   				unset( $id_master );
    			}
    		}
    		$i++;
    	}
    }
    
    function importNatJur(){
    	
    	if ($linhas				= file('para_importacao/natjur.csv') ){
    	
	    	$i	=	0;
	    	$objItemBiblioteca	=	new ItemBiblioteca();
	    	$objTPitem			=	new TpItemBiblioteca();
	    	
	    	$arrTIBs			=	array();
	    	$arrTIBs[]			=	$objTPitem->getIdTibByMetanome('DTCRIACAONATJIRIDIC');
	    	$arrTIBs[]			=	$objTPitem->getIdTibByMetanome('CODNATJIRIDIC');
	    	$arrTIBs[]			=	$objTPitem->getIdTibByMetanome('DESCNATJIRIDIC');
	    	$arrTIBs[]			=	$objTPitem->getIdTibByMetanome('NATJIRIDIC');
	    	$objItemBiblioteca->apagarPorTIB( $arrTIBs );
	    	foreach ( $linhas as $linha ){
	    	
	    		if( $i > 0 ){
	    			$linha = utf8_encode($linha);
	    			$id_master		=	UUID::v4();
	    			$arrRegistro	=	explode('	', $linha);
	    			if ( count($arrRegistro) == 2){
	    				$natJurMaster			=	$objTPitem->getIdTibByMetanome('NATJIRIDIC');
	    				$objItemBiblioteca->criarItem($id_master, '', $natJurMaster);
	    				
	    				$natJurCod				=	$objTPitem->getIdTibByMetanome('CODNATJIRIDIC');
	    				$objItemBiblioteca->criarItem(UUID::v4(), $arrRegistro[0], $natJurCod, $id_master);
	    				
	    				$natJurDesc			=	$objTPitem->getIdTibByMetanome('DESCNATJIRIDIC');
	    				$objItemBiblioteca->criarItem(UUID::v4(), $arrRegistro[1], $natJurDesc, $id_master);
	    				
	    				$natJurDataDeInclusao =	$objTPitem->getIdTibByMetanome('DTCRIACAONATJIRIDIC');
	    				$objItemBiblioteca->criarItem(UUID::v4(), date("Ymd"), $natJurDataDeInclusao, $id_master);
	    				
	    			}	    			
	    		} $i++;
	    	}
    	} else {
    		echo "Arquivo não encontrado! Grave o arquivo de Natureza Jurídica antes de executar o serviço.";
    		die();
    	}
    }
    
    function importCNPJ(){

    	set_time_limit('18000');
    	
		$objTpInforma	=	new TpInformacao();
		$objPessoa		=	new Pessoa();
		$objInformacao	=	new Informacao();
		$objRlVincPes	=	new RlVinculoPessoa();
		$objGrupo		=	new Grupo();
		
		$info_cnae		=	$objTpInforma->getByMetanome('CODCNAE');
		$info_natjur	=	$objTpInforma->getByMetanome('CODNATJUR');
		$info_cnpj		=	$objTpInforma->getByMetanome('CNPJ');
		$info_nome_fan	=	$objTpInforma->getByMetanome('NOMEFANTASIA');
		$info_cnae		=	$objTpInforma->getByMetanome('CODCNAE');
		$info_natjur	=	$objTpInforma->getByMetanome('CODNATJUR');
    	if ($linhas				= file('para_importacao/CNPJ.csv') ){
    		
    		$i		=	0;
    		$iMail	=	1;
    		foreach ( $linhas as $linha ){
    
    			if( $i > 0 ){
    				$linha = utf8_encode($linha);
    				$linha = str_replace('"', '', $linha);
    				$linha = str_replace(';', '', $linha);
    				$id_master		=	UUID::v4();
    				$arrRegistro	=	explode('	', $linha);
    				if ( count($arrRegistro) == 5){
    					
    					if ( $this->CNPJJaCadastrado($arrRegistro[0]) ){
    						
    						$id_pessoa	=	$this->CNPJJaCadastrado($arrRegistro[0],true);
    						
    						if ( !$this->jaPossuiCNAE( $arrRegistro[3] ) ){    							
    							$objInformacao->ciarNova($info_cnae['id'], $id_pessoa, $arrRegistro[3],'TbInformacao');
    						}
    						
    						if ( !$this->jaPossuiNaturezaJuridica($arrRegistro[4]) ){    							
    							$objInformacao->ciarNova($info_natjur['id'], $id_pessoa, $arrRegistro[4],'TbInformacao');
    						}    						
    					} else {
    						$id_pessoa	=	UUID::v4();
    						$grupo		=	$objGrupo->getGrupoByMetanome( 'HASH' );
    						$objPessoa->criaPessoa($id_pessoa, $arrRegistro[1]);
    						$objRlVincPes->criarVinculo('ENTIDADE', $id_pessoa, $grupo['id']);
    						
    						$objInformacao->ciarNova($info_cnpj['id'], $id_pessoa, $arrRegistro[0],'TbInformacao');   						
    						$objInformacao->ciarNova($info_nome_fan['id'], $id_pessoa, $arrRegistro[2],'TbInformacao');
    						$objInformacao->ciarNova($info_cnae['id'], $id_pessoa, $arrRegistro[3],'TbInformacao');						
    						$objInformacao->ciarNova($info_natjur['id'], $id_pessoa, $arrRegistro[4],'TbInformacao');
	    						
    					}
    				}
    			} 
    			$i++;
    			
    			if ( $iMail == 1000){
    				//manda email
    				//zera o contador
    			}
    			$iMail++;
    		}
    		die('Dados Importados com Sucesso!');
    	} else {
    		echo "Arquivo não encontrado! Grave o arquivo de CNPJ antes de executar o serviço.";
    		die();
    	}
    }
    
    function CNPJJaCadastrado( $cnpj, $retornaDados = NULL ){
    	$objTpInfo	=	new TpInformacao();
    	$tinfo		=	$objTpInfo->getByMetanome('CNPJ');
    	$stmt = $this->dbh->prepare("SELECT	*	FROM	tb_informacao	WHERE	valor	=	? and id_tinfo = ? ");
    	$stmt->bindValue(1, $cnpj);
    	$stmt->bindValue(2, $tinfo['id']);
    	$stmt->execute();
    	
    	$rs	=	 $stmt->fetchAll(PDO::FETCH_ASSOC);
    	
    	if ( isset( $rs[0] ) ){
    		if ( is_null( $retornaDados ) ){
    			return true;
    		} else {
    			return $rs[0];
    		}    		
    	} else {
    		return false;
    	}
    }

    function jaPossuiCNAE( $CNAE ){
    	$objTpInfo	=	new TpInformacao();
    	$tinfo		=	$objTpInfo->getByMetanome('CODCNAE');
    	$stmt = $this->dbh->prepare("SELECT	*	FROM	tb_informacao	WHERE	valor	=	? and id_tinfo = ? ");
    	$stmt->bindValue(1, $CNAE);
    	$stmt->bindValue(2, $tinfo['id']);
    	$stmt->execute();
    
    	$rs	=	 $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    	if ( isset( $rs[0] ) ){
    		return true;
    	} else {
    		return false;
    	}
    }

    function jaPossuiNaturezaJuridica( $naturezaJuridica ){
    	$objTpInfo	=	new TpInformacao();
    	$tinfo		=	$objTpInfo->getByMetanome('CODNATJUR');
    	$stmt = $this->dbh->prepare("SELECT	*	FROM	tb_informacao	WHERE	valor	=	? and id_tinfo = ? ");
    	$stmt->bindValue(1, $naturezaJuridica);
    	$stmt->bindValue(2, $tinfo['id']);
    	$stmt->execute();
    
    	$rs	=	 $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    	if ( isset( $rs[0] ) ){
    		return true;
    	} else {
    		return false;
    	}
    }
}