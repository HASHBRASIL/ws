<?php

    require_once("Random.php");
    set_time_limit(0);

    $dbh2 = new PDO( 'pgsql:host=www.hash.ws;port=5432;dbname=glmdf;user=hash;password=fYQEuSUTEBhWrMPA' );
    $dbh2->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

    try {
        $dbh->beginTransaction();
        $lstPes = $dbh2->prepare('select p.nom_pessoa, pf.* from tb_pessoa p join tb_pessoa_fisica pf on (p.seq_pessoa = pf.seq_pessoa) order by p.nom_pessoa');
        $lstEnd = $dbh2->prepare('select * from tb_endereco where seq_pessoa = :idpes');
        $lstHist = $dbh2->prepare('select * from tb_situacao_irmao where seq_pessoa_irmao = :idpes');
        $lstLj = $dbh2->prepare('select * from tb_loja_irmao where seq_pf_irmao = :idpes');
        $qryCodLoja = $dbh2->prepare('select cod_numero from tb_pessoa_juridica where seq_pessoa = :idloja');
        $lstPes->execute();
        $arrPes = $lstPes->fetchAll(PDO::FETCH_ASSOC);
        //new dBug($arrPes[0]);
        //echo count($arrPes);
        foreach ($arrPes as $reg) {
            echo $reg['nom_pessoa'] . ' - ' . $reg['seq_pessoa'] . ' - ' . date('H:i:s', time()) . ' - ';
            //echo count($reg);
            $idPes = pessoa($dbh,$reg['nom_pessoa']);
            usuario($dbh,$idPes,$reg['num_matricula'],$reg['num_cpf']);
            vinculopes($dbh,$idPes);
            pes_meta($dbh,$idPes,'a1a4ac1f-ccac-47d0-bbe5-72858fcef376',$reg['nom_pessoa'],null);
            $idSexo = getIbByValor($dbh,'92fd0a17-1ed9-490a-87f1-03a0bbb59cbc',$reg['seq_apoio_sexo']);
            pes_meta($dbh,$idPes,'fc9cfc8b-2413-4218-8b7c-9cdb58ef81d9',$idSexo,null);
            $idEstCiv = getIbByValor($dbh,'5bd09c57-afc3-4cf3-d4a0-cc455038f2e4',$reg['seq_apoio_est_civil']);
            pes_meta($dbh,$idPes,'886a3de5-a7c7-49e6-8eb4-c31cca68e662',$idEstCiv,null);
            $idTipSng = getIbByValor($dbh,'f0a226dc-22b9-483a-fcc0-a9a3ff740087',$reg['seq_apoio_tip_sanguineo']);
            pes_meta($dbh,$idPes,'c0c4d4fa-0f19-4203-ba72-0c290c46782a',$idTipSng,null);
            $idRgOrg = getIbByValor($dbh,'4477a7ac-4d14-4a2a-a54f-305c393249ca',$reg['seq_apoio_orgao_rg']);
            pes_meta($dbh,$idPes,'ef19e92f-bd5d-4f60-9cf6-89ddaca76d85',$idRgOrg,null);
            $idInst = getIbByValor($dbh,'9d86aa5b-a2bc-4861-a122-753fa5e8f222',$reg['seq_apoio_instrucao']);
            pes_meta($dbh,$idPes,'c711e150-54df-4538-8dfd-13fa45616000',$idInst,null);
            $idEsp = getIbByValor($dbh,'cde21a2a-9322-497d-bb88-4d5aa3d41797',$reg['seq_apoio_especializacao']);
            pes_meta($dbh,$idPes,'5a4d1734-cf50-45e6-afce-9c3a7d2cb12a',$idEsp,null);
            pes_meta($dbh,$idPes,'fbe5140e-86b2-4e1a-b7f7-596cb29c7956',$reg['cod_mun_naturalidade'],null);
            pes_meta($dbh,$idPes,'bdc2bdfa-d795-45d3-89e9-a67967f76153',$reg['cod_nacionalidade'],null);
            pes_meta($dbh,$idPes,'94bee911-44e6-481b-838a-d175d4a723d7',$reg['cod_uf_orgao_rg'],null);
            pes_meta($dbh,$idPes,'9a2acb1a-8714-11e5-af63-feff819cdc9f',$reg['dat_nascimento'],null);
            pes_meta($dbh,$idPes,'2dd90018-e083-476d-949a-44e9fded5433',$reg['dat_falecimento'],null);
            pes_meta($dbh,$idPes,'489a477e-b2b9-46d8-9b2a-965922d91aaf',$reg['dat_casamento'],null);
            pes_meta($dbh,$idPes,'c16bc08d-e5ec-455b-887f-9201504475fd',$reg['num_cpf'],null);
            pes_meta($dbh,$idPes,'913bd5e2-8ec7-46cc-a947-727da845b470',$reg['num_rg'],null);
            pes_meta($dbh,$idPes,'45767bd5-2fd4-4dc5-aa26-815675d86a00',$reg['des_apelido'],null);
            pes_meta($dbh,$idPes,'3421eba0-e5b9-4b28-b73f-bab23c6f57c5',$reg['nom_pai'],null);
            pes_meta($dbh,$idPes,'ac1bc40b-013b-4b15-ab26-9f7726b0dbb0',$reg['nom_mae'],null);
            echo 'I';
            // pes_meta($dbh,$idPes,'',$reg['nom_foto'],null);
            // //DADOS MACONICOS
            $idGrau = getIbByValor($dbh,'e8f2d870-fc7b-451c-8c77-73d967f0259f',$reg['seq_apoio_grau_irmao']);
            pes_meta($dbh,$idPes,'1b22da98-94a9-4c5b-ac72-bc4e53fd743d',$idGrau,null);
            $idSituacao = getIbByValor($dbh,'472e6d0c-d673-47f3-9d7a-10ce499210cd',$reg['seq_apoio_situacao']);
            pes_meta($dbh,$idPes,'d207d98b-b567-45e7-b2fd-e245e926908e',$idSituacao,null);
            pes_meta($dbh,$idPes,'5b771ddc-9e03-458b-9cef-d3be72fb2153',$reg['seq_apoio_contribuicao'],null);
            pes_meta($dbh,$idPes,'f5254941-49d6-4e12-ad1f-e95c60f92c75',$reg['num_matricula'],null);
            echo 'M';
            // //DADOS ENDERECO
            $lstEnd->bindParam(':idpes',$reg['seq_pessoa']);
            $lstEnd->execute();
            $arrEnd = $lstEnd->fetchAll(PDO::FETCH_ASSOC);
            echo count($arrEnd);
            foreach($arrEnd as $end) {
                $idEnd = pes_meta($dbh,$idPes,'f7158235-7a06-475e-9de4-b336496146e5',null,null);
                pes_meta($dbh,$idPes,'e814db1b-23b5-4eb9-b008-81cfec7d95c1',$end['cod_municipio'],$idEnd);
                pes_meta($dbh,$idPes,'cd55171e-43bc-4186-9498-4d7fc318e153',$end['des_endereco'],$idEnd);
                pes_meta($dbh,$idPes,'c367965c-8700-11e5-af63-feff819cdc9f',$end['des_bairro'],$idEnd);
                pes_meta($dbh,$idPes,'71d4cfed-75de-4e5b-aed6-84ea3002ac2f',$end['num_cep'],$idEnd);
                $idTel = pes_meta($dbh,$idPes,'983b3d1d-9cd2-4257-a1b0-93745eceb552',null,null);
                pes_meta($dbh,$idPes,'870e0bc0-3dbf-4ae4-b2e7-16f410c5f365',$end['num_telefone'],$idTel);
                $idCel = pes_meta($dbh,$idPes,'ba6660ca-7bb2-4bdc-b03f-97dbea36015e',null,null);
                pes_meta($dbh,$idPes,'cd811270-bdb0-4b4e-ac9b-0fe6232c0cd6',$end['num_celular'],$idCel);
                pes_meta($dbh,$idPes,'6fcdfc42-5866-4169-8f19-85c1bdaf257b',$end['des_email'],null);
                pes_meta($dbh,$idPes,'8856ea33-41b8-4a8f-be8c-1e79e18d984c',$end['des_site'],null);    
            }
            echo 'E';

            $lstHist->bindParam(':idpes',$reg['seq_pessoa']);
            $lstHist->execute();
            $arrHist = $lstHist->fetchAll(PDO::FETCH_ASSOC);
            echo count($arrHist);
            foreach($arrHist as $hist) {
                $idHist = pes_meta($dbh,$idPes,'c4c25ab7-160b-49fa-8eea-5842d3746cc0',null,null);
                pes_meta($dbh,$idPes,'efb5c170-89de-4ab9-b7a6-b9629fc1e303',$hist['dat_situacao_irmao'],$idHist);
                pes_meta($dbh,$idPes,'c1f87873-4000-4f5d-8583-ad00cfc9c399',$hist['seq_pessoa_loja'],$idHist);
                $idSituacao = getIbByValor($dbh,'472e6d0c-d673-47f3-9d7a-10ce499210cd',$hist['seq_apoio_situacao_irmao']);
                pes_meta($dbh,$idPes,'a7919e1f-02d8-4414-8a9d-3e6f5f227006',$idSituacao,null);
            }
            echo 'S';
            $lstLj->bindParam(':idpes',$reg['seq_pessoa']);
            $lstLj->execute();
            $arrLj = $lstLj->fetchAll(PDO::FETCH_ASSOC);
            echo count($arrLj);
            foreach($arrLj as $lj) {
                // $idHist = pes_meta($dbh,$idPes,'c4c25ab7-160b-49fa-8eea-5842d3746cc0',null,null);
                if($lj['seq_pj_loja']){
                    pes_meta($dbh,$idPes,'0e9af6bc-2a09-4d46-afd7-4ca25961a66d',$lj['seq_pj_loja'],null);
                    $qryCodLoja->bindParam(':idloja',$lj['seq_pj_loja']);
                    $qryCodLoja->execute();
                    $arrCodLoja=$qryCodLoja->fetchAll(PDO::FETCH_ASSOC);
                    $arrCodLoja = current($arrCodLoja);
                    vinculaGrupoByLoja($dbh,$arrCodLoja['cod_numero'],$idPes);
                }
                if($lj['seq_pj_loja_principal']){
                    pes_meta($dbh,$idPes,'f15e9932-911f-4309-9320-c8e136b920aa',$lj['seq_pj_loja_principal'],null);
                    $qryCodLoja->bindParam(':idloja',$lj['seq_pj_loja_principal']);
                    $qryCodLoja->execute();
                    $arrCodLoja=$qryCodLoja->fetchAll(PDO::FETCH_ASSOC);
                    $arrCodLoja = current($arrCodLoja);
                    vinculaGrupoByLoja($dbh,$arrCodLoja['cod_numero'],$idPes);
                }                
                // pes_meta($dbh,$idPes,'c1f87873-4000-4f5d-8583-ad00cfc9c399',$hist['seq_pessoa_loja'],$idHist);
                // $idSituacao = getIbByValor($dbh,'472e6d0c-d673-47f3-9d7a-10ce499210cd',$hist['seq_apoio_situacao_irmao']);
                // pes_meta($dbh,$idPes,'a7919e1f-02d8-4414-8a9d-3e6f5f227006',$idSituacao,null);
            }
            echo '<br />';
        }
        $dbh->commit();
        //$dbh->rollBack();
    } catch (PDOException $e) {
        $dbh->rollBack();
        parseJson(true, $e->getMessage(), $e->getTraceAsString());
    } catch (exception $e) {
        $dbh->rollBack();
        parseJson(true, $e->getMessage(), $e->getTraceAsString());
    }


    function pessoa($dbh,$nome){
        $idPessoa = UUID::v4();
        $stmt = $dbh->prepare("insert into tb_pessoa (id,nome) values (:id,:nome)");
        $stmt->bindParam(':id',$idPessoa);
        $stmt->bindParam(':nome',$nome);
        $stmt->execute();
        return $idPessoa;
    }

    function pes_meta($dbh,$pessoa,$campo,$valor,$pai = null) {
        $glmdf = 'b3c35a24-b142-4704-971d-0437e04f940d';
        $idInfo = UUID::v4();
        $stmt = $dbh->prepare('insert into tb_informacao (id,id_pessoa,id_tinfo,valor,id_pai) values (:id,:pessoa,:campo,:valor,:pai)');
        $stmt->bindParam(':id',$idInfo);
        $stmt->bindParam(':pessoa',$pessoa);
        $stmt->bindParam(':campo',$campo);
        $stmt->bindParam(':valor',$valor);
        $stmt->bindParam(':pai',$idPai);
        $stmt->execute();

        $stmt2 = $dbh->prepare('insert into rl_grupo_informacao (id,id_grupo,id_pessoa,id_info) values (uuid_generate_v4(),:grupo,:pessoa,:info)');
        $stmt2->bindParam(':grupo',$glmdf);
        $stmt2->bindParam(':pessoa',$pessoa);
        $stmt2->bindParam(':info',$idInfo);
        $stmt2->execute();
        return $idInfo;
    }

    function getIbByValor($dbh,$campo,$valor){
        $stmt = $dbh->prepare('select id_ib_pai from tb_itembiblioteca ib where id_tib = :campo and valor = :valor');
        $stmt->bindParam(':campo',$campo);
        $stmt->bindParam(':valor',$valor);
        $stmt->execute();
        $rsValor = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $ret = null;
        if(!count($rsValor)){
            //echo "Problema ao carregar " . $campo . " com o valor " . $valor;
        } else {
            $linhaValor = current($rsValor);
            $ret = $linhaValor['id_ib_pai'];
        }
        
        return $ret;
    }

    function grupo($dbh,$nome,$pai,$criador,$meta) {
        $idgrp = UUID::v4();
        $stmt = $dbh->prepare('insert into tb_grupo (id,metanome,nome,publico,id_criador,id_pai,descricao) values (:id,:meta,:nome,TRUE,:criador,:pai,:desc)');
        $stmt->bindParam(':id',$idgrp);
        $stmt->bindParam(':meta',$meta);
        $stmt->bindParam(':nome',$nome);
        $stmt->bindParam(':criador',$criador);
        $stmt->bindParam(':pai',$pai);
        $stmt->bindParam(':desc',$nome);
        $stmt->execute();
        return $idgrp;
    }

    function grupoMetadata($dbh,$grupo,$meta,$valor) {
        $idmtd = UUID::v4();
        $stmt = $dbh->prepare('insert into tb_grupo_metadata (id,id_grupo,metanome,valor) values (:id,:idgrp,:meta,:valor)');
        $stmt->bindParam(':id',$idmtd);
        $stmt->bindParam(':idgrp',$grupo);
        $stmt->bindParam(':meta',$meta);
        $stmt->bindParam(':valor',$valor);
        $stmt->execute();
        return $idmtd;
    }

    function ib($dbh,$pessoa,$tib,$valor,$grupo,$pai = null) {
        $qryInsCracha = $dbh->prepare("insert into tb_itembiblioteca (id,id_criador,id_ib_pai,id_tib,valor) values (:id,:pessoa,:pai,:tib,:valor)");
        $id = UUID::v4();
        $qryInsCracha->bindParam(":id",$id);
        $qryInsCracha->bindParam(":pessoa",$pessoa);
        $qryInsCracha->bindParam(":pai",$pai);
        $qryInsCracha->bindParam(":tib",$tib);
        $qryInsCracha->bindParam(":valor",$valor);
        $qryInsCracha->execute();

        $idrgi = UUID::v4();
        $qryRlCracha = $dbh->prepare("insert into rl_grupo_item (id,id_grupo,id_item) values (:id,:grupo,:item)");
        $qryRlCracha->bindParam(':id',$idrgi);
        $qryRlCracha->bindParam(':grupo',$grupo);
        $qryRlCracha->bindParam(':item',$id);
        $qryRlCracha->execute();

        return $id;
    }

    function vinculaGrupoByLoja($dbh,$loja,$pessoa) {
        $qryInfo = $dbh->prepare("select * from tb_informacao where id_tinfo = '34251140-2fea-44bb-a408-05e26f36cd0a' and valor = :loja");
        $qryInfo->bindParam(':loja',$loja);
        $qryInfo->execute();
        $arrInfo = $qryInfo->fetchAll(PDO::FETCH_ASSOC);
        if(count($arrInfo)){
            $arrInfo = current($arrInfo);
            $qryTime = $dbh->prepare('select * from tb_grupo where id_representacao = :idtime');
            $qryTime->bindParam(':idtime',$arrInfo['id_pessoa']);
            $qryTime->execute();
            $arrTime = $qryTime->fetchAll(PDO::FETCH_ASSOC);
            if(count($arrTime)) {
                $arrTime = current($arrTime);
                $qryGrp = $dbh->prepare("select * from tb_grupo where id_pai = :idtime and metanome = 'GERAL'");
                $qryGrp->bindParam(":idtime",$arrTime['id']);
                $qryGrp->execute();
                $arrGrp = $qryGrp->fetchAll(PDO::FETCH_ASSOC);
                $arrGrp = current($arrGrp);
                $insGrp = $dbh->prepare("insert into rl_grupo_pessoa (id,id_grupo,id_pessoa,nomehash,permissao,dt_inicio) values (uuid_generate_v4(),:idgrupo,:idpessoa,'geral','R',(select current_timestamp)) ");
                $insGrp->bindParam(":idgrupo",$arrGrp['id']);
                $insGrp->bindParam(":idpessoa",$pessoa);
                $insGrp->execute();
            }
        }
    }

    function usuario($dbh,$pessoa,$nomeusuario,$pwd) {
        $sal = Random::random_str(16);
        $senha = hash_pbkdf2('sha1', $pwd, $sal,10000,40);
        $insUsr = $dbh->prepare("insert into tb_usuario (id,nomeusuario,salt,password_encrypted) values (:id,:nome,:sal,:senha)");
        $insUsr->bindParam(":id",$pessoa);
        $insUsr->bindParam(":nome",$nomeusuario);
        $insUsr->bindParam(":sal",$sal);
        $insUsr->bindParam(":senha",$senha);
        $insUsr->execute();
    }

    function vinculopes($dbh,$pessoa){
        $insCls = $dbh->prepare("insert into rl_vinculo_pessoa (id,id_classificacao,id_pessoa,id_grupo) values (uuid_generate_v4(),:idcls,:idpes,:idgrp)");
        $classif = '844a1de5-6281-41b4-83ef-d3d6e252e745';
        $pf = '890b2fb2-8cee-44d9-b01f-a31f29406284';
        $glmdf = 'b3c35a24-b142-4704-971d-0437e04f940d';
        $insCls->bindParam(":idcls",$classif);
        $insCls->bindParam(":idpes",$pessoa);
        $insCls->bindParam(":idgrp",$glmdf);
        $insCls->execute();
        $insCls->bindParam(":idcls",$pf);
        $insCls->bindParam(":idpes",$pessoa);
        $insCls->bindParam(":idgrp",$glmdf);
        $insCls->execute();
    }
    