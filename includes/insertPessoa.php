<?php

try {
        $masters = array();
        $dbh->beginTransaction();
        require_once "UUID.php";

        $data = $_REQUEST;
        $tpInformacao = new TpInformacao();

        foreach ($data as $chave => $valor) {
        	preg_match('/_/', $chave, $r);
        	if( count( $r ) > 0 ){
        		$c	=	explode( '_', $chave);
        		$strdata[ $c[1] ] = $valor;
        	}
        }
        
        if ( in_array('PF', explode(',', $SERVICO['ws_perfil']))){
        	$nome		=	$strdata['NOMEPESSOA'];
        	$nomemae	=	$strdata['NOMEMAE'];
        	$nomepai	=	$strdata['NOMEPAI'];
        } else {
        	// em caso de entidade o nome da tabela pessoa vai ser vazio
        	$nome		=	'';
        }
        
		if (isset($SERVICO['id_grupo'])){
        	$grupo = $SERVICO['id_grupo'];
        } else {
        	$grupo = $identity->time['id'];
        }

		preg_match('/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/', $nome, $nome_uuid);
 
		if ( count( $nome_uuid ) == 2 ){
           $rsPessoa = $tpInformacao->getTpInformacaoByPerfisByPessoaByGrupo($SERVICO['metadata']['ws_perfil'], $nome, $grupo,true);
           
            if (count($rsPessoa) > 0) {
                $flashMsg = new flashMsg();
	            $flashMsg->error('já existem dados salvos para esta pessoa/entidade como '.$SERVICO['ws_target'].' em seu grupo.',true);
	    	
	            $s = new Servico();
	            $p = $s->pegaPai($SERVICO['id_pai']);	             
	            parseJsonTarget($p['id']);
            }
        }
        
        $campos = $tpInformacao->getTpInformacaoByPerfis($SERVICO['metadata']['ws_perfil']);
        $arPerfil = explode(',', $SERVICO['metadata']['ws_perfil']);
       
        foreach($campos as $campo) {
            $arCampos[$campo['perfil']][] = $campo;
        }
       
        
        if ( count( $nome_uuid ) == 2 ){
        	$uuidPessoa = $nome;
        	} else {
        	$uuidPessoa = UUID::v4();
            $queryInsertPessoa = $dbh->prepare("INSERT INTO tb_pessoa (id, dtype, dt_inclusao, nome) VALUES (:id, 'TbPessoa', (select current_timestamp), :nome);");
            $queryInsertPessoa->bindParam(':id', $uuidPessoa);
            $queryInsertPessoa->bindParam(':nome', $nome);
            $queryInsertPessoa->execute();
            if ( in_array('PF', explode(',', $SERVICO['ws_perfil']))){
                foreach($campos as $campo) {
                    if($campo['metanome']=='NOMEPESSOA'){
                        $data[$campo['id'].'_'.$campo['metanome']] = $uuidPessoa;
                    }
                }
            }
        }
        
		$objRlVinculoPessoa =	new	RlVinculoPessoa();
        $objPessoa			=	new Pessoa(); 
        if ( in_array('PF', explode(',', $SERVICO['ws_perfil']))){
        	
        	preg_match('/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/', $nomemae, $mae);
        	preg_match('/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/', $nomepai, $pai);
        	
        	if ( count( $mae ) == 2 ){
				$objRlVinculoPessoa->criarVinculo('MAE', $uuidPessoa, $grupo, $nomemae );			
        	}
        	else{
        		$uuidMae = UUID::v4();
        		$objPessoa->criaPessoa( $uuidMae, $nomemae);
        		$objRlVinculoPessoa->criarVinculo('MAE', $uuidPessoa, $grupo, $uuidMae );
                foreach($campos as $campo) {
                    if($campo['metanome']=='NOMEMAE'){
                        $data[$campo['id'].'_'.$campo['metanome']] = $uuidMae;
                    }
                }
        	}
        	
        	if ( count( $pai ) == 2 ){
				$objRlVinculoPessoa->criarVinculo('PAI', $uuidPessoa, $grupo, $nomepai );    		
        	}
        	else{
        		$uuidPai = UUID::v4();
        		$objPessoa->criaPessoa( $uuidPai, $nomepai);
        		$objRlVinculoPessoa->criarVinculo('PAI', $uuidPessoa, $grupo, $uuidPai );
                foreach($campos as $campo) {
                    if($campo['metanome']=='NOMEPAI'){
                        $data[$campo['id'].'_'.$campo['metanome']] = $uuidPai;
                    }
                }
        	}
        	//$objRlVinculoPessoa->criarVinculo('CONTATO', $uuidPessoa, $grupo );
        }
       	$objRlVinculoPessoa->criarVinculo($SERVICO['ws_classificacao'], $uuidPessoa, $grupo );

        $idPaiTipo = null;
        $idNulo = null;

        foreach ($campos as $campo) {
            if($campo['tipo']!='Master') {
                //if (isset($data[$campo['id'].'_'.$campo['metanome']]) && $data[$campo['id'].'_'.$campo['metanome']]) {
                    if(is_array($data[$campo['id'].'_'.$campo['metanome']])) {
                        if (isset($data[$campo['id'].'_'.$campo['metanome']]) && $data[$campo['id'].'_'.$campo['metanome']]) {
                            $val = $data[$campo['id'].'_'.$campo['metanome']];
                        } else {
                            $val = '';
                        }
                        if($campo['id_pai']){
                            if(!isset($masters[$campo['id_pai']])){
                                $masters[$campo['id_pai']] = array();
                            } 
                            foreach($data[$campo['id'].'_'.$campo['metanome']] as $cnt => $valor){
                                if(!isset($masters[$campo['id_pai']][$cnt])){
                                    $masters[$campo['id_pai']][$cnt] = salvaInfo($dbh,$uuidPessoa,$grupo,$identity->id,$campo['id_pai'],$idNulo,$idNulo);
                                }
                                $idpai = $masters[$campo['id_pai']][$cnt];
                                salvaInfo($dbh,$uuidPessoa,$grupo,$identity->id,$campo['id'],$idpai,$valor);
                            }
                        } else {
                            foreach($data[$campo['id'].'_'.$campo['metanome']] as $valor){
                                salvaInfo($dbh,$uuidPessoa,$grupo,$identity->id,$campo['id'],$idNulo,$valor);
                            }
                        }
                    } else {
                        if (isset($data[$campo['id'].'_'.$campo['metanome']]) && $data[$campo['id'].'_'.$campo['metanome']]) {
                            if($campo['id_pai']){
                                if(!isset($masters[$campo['id_pai']])){
                                    $masters[$campo['id_pai']] = salvaInfo($dbh,$uuidPessoa,$grupo,$identity->id,$campo['id_pai'],$idNulo,$idNulo);
                                }
                                $idpai = $masters[$campo['id_pai']];
                                salvaInfo($dbh,$uuidPessoa,$grupo,$identity->id,$campo['id'],$idpai,$data[$campo['id'].'_'.$campo['metanome']]);
                            } else {
                                salvaInfo($dbh,$uuidPessoa,$grupo,$identity->id,$campo['id'],$idNulo,$data[$campo['id'].'_'.$campo['metanome']]);
                            }
                        }
                    }
                //} else {
                //    if ($campo['obrigatorio']) {
                //        parseJson(true, 'Campo obrigatório não preenchido ' . $campo['nome']);
                //    }
                //}
            }
        }
        
        $dbh->commit();
    
        if (isset($SERVICO['metadata']['ws_target']) && ($SERVICO['metadata']['ws_target'])) {
        	$servico = new Servico();
        	$servicoDestino = $servico->getServiceByMetanome($SERVICO['metadata']['ws_target']);
        } elseif (isset($SERVICO['metadata']['ws_target']) && (!$SERVICO['metadata']['ws_target'])) {
        	$servicoDestino = $SERVICO['id_pai'];
        }
        
        $servicoDestino = current($servicoDestino);

        if ($servicoDestino) {
            $flashMsg = new flashMsg();
            $flashMsg->success('Salvo com sucesso!');

            parseJsonTarget($servicoDestino);
        } else {
            parseJson();
        }

    } catch (PDOException $e) {
        // @todo verificar se vai ter algum tratamento especial para PDO.
        $dbh->rollBack();
        parseJson(true, $e->getMessage(), $e->getTraceAsString());
    } catch (exception $e) {
        $dbh->rollBack();
        parseJson(true, $e->getMessage(), $e->getTraceAsString());
    }

function salvaInfo($dbh,$idpessoa,$idgrupo,$idcriador,$idtinfo,$idpai,$valor) {

    $queryInsertInformacao = $dbh->prepare("INSERT INTO tb_informacao ( id, valor, id_criador, id_tinfo, id_pai, id_pessoa ) VALUES (:id, :valor, :id_criador, :id_tinfo, :id_pai, :id_pessoa);");
    $queryInsertRelacionamento = $dbh->prepare("INSERT INTO rl_grupo_informacao (id, id_grupo, id_pessoa, id_info) VALUES (:id, :id_grupo, :id_pessoa, :id_info);");

    $id = UUID::v4();

    $queryInsertInformacao->bindParam(':id', $id);
    $queryInsertInformacao->bindParam(':valor', $valor);
    $queryInsertInformacao->bindParam(':id_criador', $idcriador);
    $queryInsertInformacao->bindParam(':id_tinfo', $idtinfo);
    $queryInsertInformacao->bindParam(':id_pai', $idpai);
    $queryInsertInformacao->bindParam(':id_pessoa', $idpessoa);
    $queryInsertInformacao->execute();

    $uuidRelacionamento = UUID::v4();
    $queryInsertRelacionamento->bindParam(':id', $uuidRelacionamento);
    $queryInsertRelacionamento->bindParam(':id_grupo', $idgrupo);
    $queryInsertRelacionamento->bindParam(':id_pessoa', $idpessoa);
    $queryInsertRelacionamento->bindParam(':id_info', $id);
    $queryInsertRelacionamento->execute();    

    return $id;
}