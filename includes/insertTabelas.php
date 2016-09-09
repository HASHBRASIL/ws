<?php

    $tabela = $_REQUEST['conteudo_Tabela'];
	$dataCarga = $_REQUEST['data_Carga'];
	$origemDados = $_REQUEST['origem_Dados'];
	$grupo = $_REQUEST['id_grupo'];
	
    $sql = "select * from $tabela";

    $criador = $identity->id;

    $idNulo = null;

    $dbh->beginTransaction();

    try {

        $qryInsTIB = $dbh->prepare('insert into tp_itembiblioteca (id,nome,metanome,tipo,id_tib_pai,dt_criacao) values (:id,:nome,:nome,:tipo,:idpai,current_timestamp) ');
        $qryInsIB = $dbh->prepare('insert into tb_itembiblioteca (id,dt_criacao,valor,id_criador,id_ib_pai,id_tib) values (:id,current_timestamp,:valor,:criador,:idpai,:tipo)');
        $qryInsTIBMETA = $dbh->prepare('insert into tp_itembiblioteca_metadata (id,metanome,valor,id_tib,dt_criacao) values (:id,:metanome,:valor,:id_tib,current_timestamp)');
        $qryInsTIBMETAFILHO = $dbh->prepare('insert into tp_itembiblioteca_metadata (id,metanome,valor,id_tib,id_tib_pai,dt_criacao) values (:id,:metanome,:valor,:id_tib,:id_tib_pai,current_timestamp)');
        $qryInsRLIBGRUPO = $dbh->prepare('insert into rl_grupo_item (id, id_grupo, id_item) values (:id,:id_grupo,:id_item)');

        $idMaster = UUID::v4();
        $idTibs = array();

        $qryInsTIB->bindParam('id',$idMaster);
        $qryInsTIB->bindParam('nome',$tabela);
        $qryInsTIB->bindValue('tipo','Master');
        $qryInsTIB->bindParam('idpai',$idNulo);
        $qryInsTIB->execute();

        $idMeta	= UUID::v4();
        $qryInsTIBMETA->bindParam('id', $idMeta);
        $qryInsTIBMETA->bindValue('metanome', 'ws_dataImportacao');
        $qryInsTIBMETA->bindParam('valor', $dataCarga);
        $qryInsTIBMETA->bindParam('id_tib', $idMaster);
        $qryInsTIBMETA->execute();

        $idMeta	= UUID::v4();
        $qryInsTIBMETA->bindParam('id', $idMeta);
        $qryInsTIBMETA->bindValue('metanome', 'ws_origemDados');
        $qryInsTIBMETA->bindParam('valor', $origemDados);
        $qryInsTIBMETA->bindParam('id_tib', $idMaster);
        $qryInsTIBMETA->execute();
        
        $qry = $dbh->query($sql);
        
        for ($i = 0; $i < $qry->columnCount(); $i++) {
            $col = $qry->getColumnMeta($i);
            //$columns[] = $col['name'];
            $idTibFilho = UUID::v4();
            $idTibs[$col['name']] = $idTibFilho;
            $qryInsTIB->bindParam('id',$idTibs[$col['name']]);
            $qryInsTIB->bindParam('nome',$col['name']);
            $qryInsTIB->bindValue('tipo','text');
            $qryInsTIB->bindParam('idpai',$idMaster);
            $qryInsTIB->execute();
            
            $idMeta	= UUID::v4();
            $qryInsTIBMETAFILHO->bindParam('id', $idMeta);
            $qryInsTIBMETAFILHO->bindValue('metanome', 'ws_visivel');
            $qryInsTIBMETAFILHO->bindValue('valor', '1');
            $qryInsTIBMETAFILHO->bindParam('id_tib', $idTibFilho);
            $qryInsTIBMETAFILHO->bindParam('id_tib_pai', $idMaster);
            $qryInsTIBMETAFILHO->execute();

            $idMeta	= UUID::v4();
            $qryInsTIBMETAFILHO->bindParam('id', $idMeta);
            $qryInsTIBMETAFILHO->bindValue('metanome', 'ws_ordem');
            $qryInsTIBMETAFILHO->bindParam('valor', $i);
            $qryInsTIBMETAFILHO->bindParam('id_tib', $idTibFilho);
            $qryInsTIBMETAFILHO->bindParam('id_tib_pai', $idMaster);
            $qryInsTIBMETAFILHO->execute();

            $idMeta	= UUID::v4();
            $qryInsTIBMETAFILHO->bindParam('id', $idMeta);
            $qryInsTIBMETAFILHO->bindValue('metanome', 'ws_ordemLista');
            $qryInsTIBMETAFILHO->bindParam('valor', $i);
            $qryInsTIBMETAFILHO->bindParam('id_tib', $idTibFilho);
            $qryInsTIBMETAFILHO->bindParam('id_tib_pai', $idMaster);
            $qryInsTIBMETAFILHO->execute();
        }

        $rs = $qry->fetchAll(PDO::FETCH_ASSOC);

        foreach($rs as $linha){
            $idIbMaster = UUID::v4();
            $qryInsIB->bindParam('id',$idIbMaster);
            $qryInsIB->bindParam('criador',$criador);
            $qryInsIB->bindParam('idpai',$idNulo);
            $qryInsIB->bindParam('tipo',$idMaster);
            $qryInsIB->bindParam('valor',$idNulo);
            $qryInsIB->execute();
//
			$idRlIbGrupo = UUID::v4();
            $qryInsRLIBGRUPO->bindParam('id',$idRlIbGrupo);
            $qryInsRLIBGRUPO->bindParam('id_grupo',$grupo);
            $qryInsRLIBGRUPO->bindParam('id_item',$idIbMaster);
            $qryInsRLIBGRUPO->execute();
            
            foreach($linha as $campo=>$valor){
                $idIb = UUID::v4();
                $qryInsIB->bindParam('id',$idIb);
                $qryInsIB->bindParam('criador',$criador);
                $qryInsIB->bindParam('idpai',$idIbMaster);
                $qryInsIB->bindParam('tipo',$idTibs[$campo]);
                $qryInsIB->bindParam('valor',$valor);
                $qryInsIB->execute();
            }
        }
        //new dBug($rs);

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
        die('funcionou');
    } catch (PDOException $e) {
        $dbh->rollBack();
        parseJson(true, $e->getMessage(), $e->getTraceAsString());
    } catch (exception $e) {
        $dbh->rollBack();
        parseJson(true, $e->getMessage(), $e->getTraceAsString());
    }

    $qry = $dbh->query($sql);
    for ($i = 0; $i < $qry->columnCount(); $i++) {
        $col = $qry->getColumnMeta($i);
        $columns[] = $col['name'];
    }

    //print_r($columns);

    //$qry = $dbh->prepare($sql);
    //$qry->bindParam(1,$tabela);
    //$qry->execute();
    //$rs = $qry->fetchAll(PDO::FETCH_ASSOC);

    //new dBug($rs);