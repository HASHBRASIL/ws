<?php

    $dbh2 = new PDO( 'pgsql:host=www.hash.ws;port=5432;dbname=glmdf;user=hash;password=fYQEuSUTEBhWrMPA' );
    $dbh2->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

    try {
        $dbh->beginTransaction();

        $qryTabs = $dbh2->prepare("select * from tb_pessoa_juridica where seq_pessoa > 1");
        $qryTabs->execute();
        
        $rsTabs=$qryTabs->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rsTabs as $linha) {
            echo $linha['nom_fantasia'] . ' ';
            $idPes = pessoa($dbh,$linha['nom_fantasia']);
            echo 'P';
            pes_meta($dbh,$idPes,'9d7a9c7a-794b-4e46-bab9-eff9e2fc946d',$linha['nom_fantasia']);
            pes_meta($dbh,$idPes,'ee3fb955-bd73-4d17-9342-4b38d1dec475',$linha['num_cnpj']);
            pes_meta($dbh,$idPes,'34251140-2fea-44bb-a408-05e26f36cd0a',$linha['cod_numero']);
            pes_meta($dbh,$idPes,'1b30a486-50e1-440f-a7fe-e05b69b21b6a',$linha['dat_aprovacao']);
            pes_meta($dbh,$idPes,'440fff52-77dd-4039-92ed-4efe38658bca',$linha['dat_fundacao']);
            $dia = getIbByValor($dbh,'ab772c3a-061b-4785-d16b-e54d91bd6e25',$linha['seq_apoio_dia']);
            pes_meta($dbh,$idPes,'ced29a4f-bdc8-4c46-8caa-de030cc281d1',$dia);
            $folga = getIbByValor($dbh,'f13a2649-04bc-416c-d0a4-4678c8e8c271',$linha['seq_apoio_folga']);
            pes_meta($dbh,$idPes,'2ddb849b-55a9-4ed6-9979-278f29330708',$folga);
            pes_meta($dbh,$idPes,'bfd1bc63-0a06-4220-9722-68603e84faba',$linha['des_hora']);
            pes_meta($dbh,$idPes,'f8475f26-e7b9-4575-9934-51a2de528236',$linha['des_observacao']);
            $rito = getIbByValor($dbh,'8dc8f1df-4a09-45d6-9c47-e1b00bfa078e',$linha['seq_apoio_rito']);
            pes_meta($dbh,$idPes,'d779f658-b8f5-43cb-a1c0-27d92a161056',$rito);
            pes_meta($dbh,$idPes,'8c7a82e6-2ed9-4847-84d2-3668a20d10c3',$linha['nom_sigla']);
            echo 'I';
            $idTime = novoTime($dbh,$linha['nom_fantasia'],$linha['nom_fantasia'],$idPes);
            $site = grupo($dbh,'geral',$idTime,$idPes,'GERAL');
            site($dbh,$idTime,$idPes,$linha['nom_fantasia'],$linha['cod_numero']);
            echo 'S';
            cracha($dbh,$dbh2,'b3c35a24-b142-4704-971d-0437e04f940d',$site,$idPes, $linha['seq_pessoa'],$idTime);
            echo 'C';
            plesk(13,trim($linha['cod_numero']));
            echo 'D<br />';
        }

        //$dbh->commit();
        $dbh->rollBack();
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

    function novoTime($dbh,$nome,$metanome,$repr){
        $glmdf = 'b3c35a24-b142-4704-971d-0437e04f940d';
        $idTime = UUID::v4();
        $stmt = $dbh->prepare('insert into tb_grupo (id,nome,metanome,publico,id_criador,id_pai,id_representacao,descricao) values (:id,:nome,:metanome,false,:criador,:pai,:representacao,:descricao) ');
        $stmt->bindParam(':id',$idTime);
        $stmt->bindParam(':nome',$nome);
        $stmt->bindParam(':metanome',$metanome);
        $stmt->bindParam(':criador',$repr);
        $stmt->bindParam(':pai',$glmdf);
        $stmt->bindParam(':representacao',$repr);
        $stmt->bindParam(':descricao',$nome);
        $stmt->execute();
        return $idTime;
    }

    function site($dbh,$time,$criador,$nome,$dns){
        $site = grupo($dbh,$nome,$time,$criador,'SITE');
        $perfil = grupo($dbh,'Loja Maçônica',$site,$criador,'perfil');
        $veneraveis = grupo($dbh,'Lista de Veneráveis',$perfil,$criador,'veneraveis');
        $maconaria = grupo($dbh,'Maçonaria',$perfil,$criador,'maconaria');
        $principios = grupo($dbh,'Princípios da Maçonaria',$perfil,$criador,'principios');
        $macom = grupo($dbh,'O Maçom',$perfil,$criador,'macom');
        $quemmacom = grupo($dbh,'Quem é o Maçom',$perfil,$criador,'quemmacom');
        $noticia = grupo($dbh,'Notícias',$site,$criador,'noticia');
        $agenda = grupo($dbh,'Agenda',$site,$criador,'agenda');
        //METADATA SITE
        grupoMetadata($dbh,$site,'cms_alias',$dns);
        grupoMetadata($dbh,$site,'cms_template','emacom');
        grupoMetadata($dbh,$site,'cms_area_menu','menuPrincipal');
        grupoMetadata($dbh,$site,'cms_area_coluna','itensBox');
        grupoMetadata($dbh,$site,'cms_area_rodape','rodapePrincipal');
        grupoMetadata($dbh,$site,'cms_area_topo','publicidadeTopo,itensDestaques,apresentacaoCracha');
        grupoMetadata($dbh,$site,'cms_visivel','true');
        grupoMetadata($dbh,$site,'cms_menulista','');
        grupoMetadata($dbh,$site,'cms_area_conteudo','itensColunas,agendaCalendario,midiaGaleria');
        grupoMetadata($dbh,$site,'cms_cracha','EMCCRACHALOJAGLMDF');
        grupoMetadata($dbh,$site,'cms_menuperfil',$perfil);
        //METADATA PERFIL
        grupoMetadata($dbh,$perfil,'cms_menutipo','detalhe');
        grupoMetadata($dbh,$perfil,'cms_menuacao','b2c53ea7-ad8a-4e27-b5b4-7d130411eaa3');
        grupoMetadata($dbh,$perfil,'cms_area_conteudo','itensDetalhe');
        grupoMetadata($dbh,$perfil,'cms_area_menu','menuPrincipal');
        grupoMetadata($dbh,$perfil,'cms_area_topo','');
        grupoMetadata($dbh,$perfil,'cms_area_coluna','itensBox');
        grupoMetadata($dbh,$perfil,'cms_area_rodape','rodapePrincipal');
        grupoMetadata($dbh,$perfil,'cms_visivel','true');
        //METADATA VENERAVEIS
        grupoMetadata($dbh,$veneraveis,'cms_menutipo','detalhe');
        grupoMetadata($dbh,$veneraveis,'cms_menuacao','b2c53ea7-ad8a-4e27-b5b4-7d130411eaa3');
        grupoMetadata($dbh,$veneraveis,'cms_area_conteudo','itensDetalhe');
        grupoMetadata($dbh,$veneraveis,'cms_area_menu','menuPrincipal');
        grupoMetadata($dbh,$veneraveis,'cms_area_topo','');
        grupoMetadata($dbh,$veneraveis,'cms_area_coluna','itensBox');
        grupoMetadata($dbh,$veneraveis,'cms_area_rodape','rodapePrincipal');
        grupoMetadata($dbh,$veneraveis,'cms_visivel','true');
        //METADATA MACONARIA
        grupoMetadata($dbh,$maconaria,'cms_menutipo','detalhe');
        grupoMetadata($dbh,$maconaria,'cms_menuacao','b2c53ea7-ad8a-4e27-b5b4-7d130411eaa3');
        grupoMetadata($dbh,$maconaria,'cms_area_conteudo','itensDetalhe');
        grupoMetadata($dbh,$maconaria,'cms_area_menu','menuPrincipal');
        grupoMetadata($dbh,$maconaria,'cms_area_topo','');
        grupoMetadata($dbh,$maconaria,'cms_area_coluna','itensBox');
        grupoMetadata($dbh,$maconaria,'cms_area_rodape','rodapePrincipal');
        grupoMetadata($dbh,$maconaria,'cms_visivel','true');
        //METADATA PRINCIPIOS
        grupoMetadata($dbh,$principios,'cms_menutipo','detalhe');
        grupoMetadata($dbh,$principios,'cms_menuacao','b2c53ea7-ad8a-4e27-b5b4-7d130411eaa3');
        grupoMetadata($dbh,$principios,'cms_area_conteudo','itensDetalhe');
        grupoMetadata($dbh,$principios,'cms_area_menu','menuPrincipal');
        grupoMetadata($dbh,$principios,'cms_area_topo','');
        grupoMetadata($dbh,$principios,'cms_area_coluna','itensBox');
        grupoMetadata($dbh,$principios,'cms_area_rodape','rodapePrincipal');
        grupoMetadata($dbh,$principios,'cms_visivel','true');
        //METADATA MACOM
        grupoMetadata($dbh,$macom,'cms_menutipo','detalhe');
        grupoMetadata($dbh,$macom,'cms_menuacao','b2c53ea7-ad8a-4e27-b5b4-7d130411eaa3');
        grupoMetadata($dbh,$macom,'cms_area_conteudo','itensDetalhe');
        grupoMetadata($dbh,$macom,'cms_area_menu','menuPrincipal');
        grupoMetadata($dbh,$macom,'cms_area_topo','');
        grupoMetadata($dbh,$macom,'cms_area_coluna','itensBox');
        grupoMetadata($dbh,$macom,'cms_area_rodape','rodapePrincipal');
        grupoMetadata($dbh,$macom,'cms_visivel','true');
        //METADATA QUEMMACOM
        grupoMetadata($dbh,$quemmacom,'cms_menutipo','detalhe');
        grupoMetadata($dbh,$quemmacom,'cms_menuacao','b2c53ea7-ad8a-4e27-b5b4-7d130411eaa3');
        grupoMetadata($dbh,$quemmacom,'cms_area_conteudo','itensDetalhe');
        grupoMetadata($dbh,$quemmacom,'cms_area_menu','menuPrincipal');
        grupoMetadata($dbh,$quemmacom,'cms_area_topo','');
        grupoMetadata($dbh,$quemmacom,'cms_area_coluna','itensBox');
        grupoMetadata($dbh,$quemmacom,'cms_area_rodape','rodapePrincipal');
        grupoMetadata($dbh,$quemmacom,'cms_visivel','true');
        //METADATA NOTICIA
        grupoMetadata($dbh,$noticia,'cms_menutipo','detalhe');
        grupoMetadata($dbh,$noticia,'cms_menuacao','b2c53ea7-ad8a-4e27-b5b4-7d130411eaa3');
        grupoMetadata($dbh,$noticia,'cms_area_conteudo','itensDetalhe');
        grupoMetadata($dbh,$noticia,'cms_area_menu','menuPrincipal');
        grupoMetadata($dbh,$noticia,'cms_area_topo','');
        grupoMetadata($dbh,$noticia,'cms_area_coluna','itensBox');
        grupoMetadata($dbh,$noticia,'cms_area_rodape','rodapePrincipal');
        grupoMetadata($dbh,$noticia,'cms_visivel','true');
        //METADATA AGENDA
        grupoMetadata($dbh,$agenda,'cms_menutipo','detalhe');
        grupoMetadata($dbh,$agenda,'cms_menuacao','b2c53ea7-ad8a-4e27-b5b4-7d130411eaa3');
        grupoMetadata($dbh,$agenda,'cms_area_conteudo','itensDetalhe');
        grupoMetadata($dbh,$agenda,'cms_area_menu','menuPrincipal');
        grupoMetadata($dbh,$agenda,'cms_area_topo','');
        grupoMetadata($dbh,$agenda,'cms_area_coluna','itensBox');
        grupoMetadata($dbh,$agenda,'cms_area_rodape','rodapePrincipal');
        grupoMetadata($dbh,$agenda,'cms_visivel','true');
        
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

    function plesk($dominio,$dns){
        // require_once('PleskApiClient.php');
        // $host = getenv('REMOTE_HOST');
        // $login = getenv('REMOTE_LOGIN') ?: 'admin';
        // $password = getenv('REMOTE_PASSWORD');
        // $client = new PleskApiClient($host);
        // $client->setCredentials($login, $password);
        //$request = '<packet><dns><add_rec><site-id>$dominio</site-id><type>A</type><host>$dns</host><value>54.94.136.66</value></add_rec></dns></packet>'; 
        echo "<packet><dns><add_rec><site-id>$dominio</site-id><type>A</type><host>$dns</host><value>54.94.136.66</value></add_rec></dns></packet>"; 
        // $response = $client->request($request);
        // echo $response;

    }

    function cracha($dbh,$dbh2,$glmdf,$site,$pessoa,$codloja,$grupo){
        $qryCracha = $dbh2->prepare("select * from tb_endereco where seq_pessoa = :loja");
        $qryVen = $dbh2->prepare("select pes.nom_pessoa from tb_pessoa pes join tb_cargo_irmao tci on (pes.seq_pessoa = tci.seq_pessoa_irmao) where tci.seq_pessoa_loja = :loja and tci.seq_cargo = 128 and tci.dat_fim_cargo is null");
        $qryMembros = $dbh2->prepare("select count(*) as cnt from tb_loja_irmao where ((seq_pj_loja_principal = :loja1) or (seq_pj_loja = :loja2))");
        $qryCracha->bindParam(':loja',$codloja);
        $qryCracha->execute();
        $rsCracha = $qryCracha->fetchAll(PDO::FETCH_ASSOC);
        $infoCracha = current($rsCracha);
        $endereco = $infoCracha['des_endereco'];
        $bairro = $infoCracha['des_bairro'];
        $cep = $infoCracha['num_cep'];
        $email = $infoCracha['des_email'];

        $telefone = $infoCracha['num_telefone'];
        $fax = $infoCracha['num_fax'];
        $qryVen->bindParam(':loja',$codloja);
        $qryVen->execute();
        $rsVnr = $qryVen->fetchAll(PDO::FETCH_ASSOC);
        $infoVen = current($rsVnr);
        $veneravel = $infoVen['nom_pessoa'];
        $qryMembros->bindParam(':loja1',$codloja);
        $qryMembros->bindParam(':loja2',$codloja);
        $qryMembros->execute();
        $rsMembros = $qryMembros->fetchAll(PDO::FETCH_ASSOC);
        $qtd = current($rsMembros)['cnt'];
        
        //CRACHA
        $idcracha = ib($dbh,$pessoa,'5a76356d-655a-4872-b837-556449417079',null,$grupo);
        ib($dbh,$pessoa,'78e211c0-de37-498b-aa2a-52a48aa72393',$veneravel,$grupo,$idcracha);
        ib($dbh,$pessoa,'ad98b288-8c96-4380-851e-7ad69d412a05',$endereco,$grupo,$idcracha);
        ib($dbh,$pessoa,'1d83dc3d-50d4-436e-9e25-a49287301855',$telefone,$grupo,$idcracha);
        ib($dbh,$pessoa,'ed45ed4e-73bf-4393-b805-b695e324c772',$fax,$grupo,$idcracha);
        ib($dbh,$pessoa,'df6af460-7a19-4633-ae52-a87c390a3f1a',$qtd . ' membros',$grupo,$idcracha);

        $idend = pes_meta($dbh,$pessoa,'f7158235-7a06-475e-9de4-b336496146e5',null);
        pes_meta($dbh,$pessoa,'cd55171e-43bc-4186-9498-4d7fc318e153',$endereco,$idend);
        pes_meta($dbh,$pessoa,'c367965c-8700-11e5-af63-feff819cdc9f',$bairro,$idend);
        pes_meta($dbh,$pessoa,'71d4cfed-75de-4e5b-aed6-84ea3002ac2f',$cep,$idend);
        $idtel = pes_meta($dbh,$pessoa,'71d4cfed-75de-4e5b-aed6-84ea3002ac2f',null);
        pes_meta($dbh,$pessoa,'983b3d1d-9cd2-4257-a1b0-93745eceb552',$telefone,$idtel);
        pes_meta($dbh,$pessoa,'81759d2c-1609-4ba9-a2fa-f6d6833569e9',$email);

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