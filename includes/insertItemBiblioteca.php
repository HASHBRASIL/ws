<?php 
 
    require_once "databaseconnect.php";
    require_once "UUID.php";

    try{
        $identificadores = array();

        $objGrupo       = new Grupo();

        if (isset($SERVICO['id_grupo'])){
            $grupo = $SERVICO['id_grupo'];
        } elseif (isset($SERVICO['metadata']['ws_grupo'])){
            $grupos = $objGrupo->getGruposByIDPaiByMetanome($identity->time['id'],$SERVICO['metadata']['ws_grupo']);
            if(!empty($grupos)){
                $grupo = current($grupos)['id'];
            } else {
                echo "Grupo destino n�o encontrado. Favor verificar metadata.";
                die();
            }
        } else {
            $grupo = $identity->grupo['id'];
        }
        
        $servico = $SERVICO['id_tib'];
        
        foreach ( array_keys( $_POST ) as $key => $ib)
        {
            $a = explode('_',$ib);
            if ( isset( $a[1])){
                $identificadores[$a[0]] = $a[1];
            }           
        }

        $dbh->beginTransaction();
        
        //criando master
        $queryTbItemMaster = $dbh->prepare("INSERT INTO tb_itembiblioteca ( id, id_criador, id_tib ) VALUES ( :id, :id_criador, :id_tib )");

        $tokenMaster = UUID::v4();
        $queryTbItemMaster->bindParam('id',         $tokenMaster);
        $queryTbItemMaster->bindParam('id_criador', $_SESSION['USUARIO']['ID']);
        $queryTbItemMaster->bindParam('id_tib',     $SERVICO['id_tib']);
        $queryTbItemMaster->execute();
        
        //logica dos checks marcados / desmarcados
        $templateItemBiblioteca =   new TpItemBiblioteca();
        $rowsetTemplate = $templateItemBiblioteca->getTemplateByIdTibPai($servico);
        
        $arrChecks  =   array();
        foreach ( $rowsetTemplate as $key => $template ){
            if ( $template['tipo'] == 'checkbox'){
                $arrChecks[$template['id']] =   $template;
            }           
        }
            
        // criando filhos
        foreach ( $identificadores as $id_tib => $nome)
        {
            if ( $nome ) {
                if ( $nome == 'boolean' ) {
                    
                    //removendo check marcado do controle
                    unset( $arrChecks[$id_tib]);

                    $queryTbItemBilbioteca = $dbh->prepare("INSERT INTO tb_itembiblioteca ( id, valor, id_criador, id_ib_pai, id_tib )
                                                                   VALUES ( :id, :valor, :criador, :id_pai, :id_tib )");
                        
                    $queryTbItemBilbioteca->bindValue('id',         UUID::v4());
                    $queryTbItemBilbioteca->bindValue('valor',      '1');
                    $queryTbItemBilbioteca->bindValue('criador',    $_SESSION['USUARIO']['ID']);
                    $queryTbItemBilbioteca->bindValue('id_pai',     $tokenMaster);
                    $queryTbItemBilbioteca->bindValue('id_tib',     $id_tib);
                    $queryTbItemBilbioteca->execute(); 
                }else{
                    $id = $id_tib . '_' . $nome;
                        
                    $queryTbItemBilbioteca = $dbh->prepare("INSERT INTO tb_itembiblioteca ( id, valor, id_criador, id_ib_pai, id_tib )
                                                                   VALUES ( :id, :valor, :criador, :id_pai, :id_tib )");
                    
                    $queryTbItemBilbioteca->bindValue('id',         UUID::v4());
                    $queryTbItemBilbioteca->bindValue('valor',      $_POST[$id]);
                    $queryTbItemBilbioteca->bindValue('criador',    $_SESSION['USUARIO']['ID']);
                    $queryTbItemBilbioteca->bindValue('id_pai',     $tokenMaster);
                    $queryTbItemBilbioteca->bindValue('id_tib',     $id_tib);
                    $queryTbItemBilbioteca->execute();
                        
                }
            }           
        }       
        
        // salvando checks não marcados
        foreach ( $arrChecks as $key => $check){
            $queryTbItemBilbioteca = $dbh->prepare("INSERT INTO tb_itembiblioteca ( id, valor, id_criador, id_ib_pai, id_tib )
                                                                   VALUES ( :id, :valor, :criador, :id_pai, :id_tib )");
                        
            $queryTbItemBilbioteca->bindValue('id',         UUID::v4());
            $queryTbItemBilbioteca->bindValue('valor',      '0');
            $queryTbItemBilbioteca->bindValue('criador',    $_SESSION['USUARIO']['ID']);
            $queryTbItemBilbioteca->bindValue('id_pai',     $tokenMaster);
            $queryTbItemBilbioteca->bindValue('id_tib',     $check['id']);
            $queryTbItemBilbioteca->execute();
        }
        
        //Crinado referencia do arquivo
        
        if ( isset( $_FILES ) ) {
            foreach ( $_FILES as $key => $file ){
                
                $arrFile         = array();
                $id_tib_img      = explode('_', $key);
                $arrFile[ $key ] = $file;
                $tokenFile       = UUID::v4();
                $arquivo         = array();
                $arquivo[$tokenFile . '_enclosure' ]    = $_FILES['file'];
                $img   =   localUpload( $arquivo, $identity->time['id'], $grupo);
                
                $queryTbItemBilbioteca = $dbh->prepare("INSERT INTO tb_itembiblioteca ( id, valor, id_criador, id_ib_pai, id_tib )
                                                               VALUES ( :id, :valor, :criador, :id_pai, :id_tib )");
                
                $queryTbItemBilbioteca->bindValue('id',         $tokenFile);
                $queryTbItemBilbioteca->bindValue('valor',      $img);
                $queryTbItemBilbioteca->bindValue('criador',    $_SESSION['USUARIO']['ID']);
                $queryTbItemBilbioteca->bindValue('id_pai',     $tokenMaster);
                $queryTbItemBilbioteca->bindValue('id_tib',     $id_tib_img[0]);
                $queryTbItemBilbioteca->execute();
                
                $objItemBibliotecaMetadata  =   new ItemBibliotecaMetadata();
                $objItemBibliotecaMetadata->criarItemBibliotecaMetadata( $file, $tokenFile);
            }
        }
        
        // vilculando se for id = null      
        if ( !isset($_POST['id']) or !empty($_POST['id'])){
            $queryTbRlVinculoItem   = $dbh->prepare("INSERT INTO rl_vinculo_item ( id, id_ib_principal, id_ib_vinculado )
                                                           VALUES ( :id, :principal, :vinculado )");
    
            $queryTbRlVinculoItem->bindValue('id',  UUID::v4());
            $queryTbRlVinculoItem->bindValue('principal',   $_POST['id']);
            $queryTbRlVinculoItem->bindValue('vinculado',   $tokenMaster);
            $queryTbRlVinculoItem->execute();
        }
        
        // vinculando ao grupo
        $queryTbRlVinculoGrupo  = $dbh->prepare("INSERT INTO rl_grupo_item ( id, id_grupo, id_item )
                                                           VALUES ( :id, :id_grupo, :id_item )");
            
        $queryTbRlVinculoGrupo->bindValue('id',         UUID::v4());
        $queryTbRlVinculoGrupo->bindValue('id_grupo',   $grupo);
        $queryTbRlVinculoGrupo->bindValue('id_item',    $tokenMaster);
        $queryTbRlVinculoGrupo->execute();
                
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
    }catch( Exception $e ){ 
        $dbh->rollBack();
        var_dump($e);
        echo "error";   
    };
