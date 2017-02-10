<?php

require_once "importacaoDeDados.php";

$objGrupo                   =   new Grupo();
$objItemBiblioteca          =   new ItemBiblioteca();
$objTpItemBiblioteca        =   new TpItemBiblioteca();
$objItemBibliotecaMetadata  =   new ItemBibliotecaMetadata();
$objRlGI                    =   new RlGrupoItem();

$grupo = null;
if (isset($SERVICO['metadata']['ws_grupo'])){
    $grupos = $objGrupo->getGrupoByMetanome($SERVICO['metadata']['ws_grupo']);
    if(!empty($grupos)){
        $grupo = $grupos['id'];
    } else {
        echo "Grupo destino n�o encontrado. Favor verificar metadata.";
        die();
    }
} else {
    $grupo = $identity->grupo['id'];
}

$arrCampos              =   array();
$arrCampos['id']        =   $SERVICO['metadata']['ws_arqcampo'];
$item = $objTpItemBiblioteca->getTpItemBibliotecaBy( $arrCampos );
$arrCamposMaster              =   array();
$arrCamposMaster['id']        =   $item[0]['id_tib_pai'];
$itemmaster = $objTpItemBiblioteca->getTpItemBibliotecaBy($arrCamposMaster);

$id_ib      =   UUID::v4();
$id_ib_master = UUID::v4();

$arquivo    =   array();
$arquivo[$id_ib . '_enclosure' ]    = $_FILES['file'];

$caminhoDoArquivo   =   localUpload( $arquivo, $_SESSION['TIME']['ID'], $grupo);

chmod('upload_dir/'.$caminhoDoArquivo,0777);
$nFeXml     = simplexml_load_file( 'upload_dir/'.$caminhoDoArquivo );
$arrayNfe   = json_decode(json_encode($nFeXml), TRUE);

if(isset($arrayNfe['NFe']['infNFe'])){
    $arrayNfe['infNFe'] = $arrayNfe['NFe']['infNFe'];
}

$ibnota = $objItemBiblioteca->getByValorETIB($arrayNfe['infNFe']['@attributes']['Id'], $objTpItemBiblioteca->getIdTibByMetanome('IdinfNFe'));

if( $ibnota ){
    foreach ($ibnota as $itemnota){
        $iteminfnfe = current($objItemBiblioteca->getById($itemnota['id_ib_pai']));
        $lstpub = $objRlGI->getPublicacao($iteminfnfe['id_ib_pai'],$_SESSION['TIME']['ID']);
        if(count($lstpub)) {
            die('nota já importada');
        }
    }
} elseif ( !isset($arrayNfe['infNFe']['@attributes']['Id']) ){
    die('este arquivo n�o � uma nota valida');
}

DatabaseConnection::getInstance()->getConnection()->beginTransaction();

try {
    if($arrayNfe['infNFe']['@attributes']['versao'] == '3.10'){
        //NFe uber
        $id_uber = UUID::v4();
        $objItemBiblioteca->criarItem($id_uber, '', $objTpItemBiblioteca->getIdTibByMetanome('NFe'));
        //infNFe Master
        $id_master = UUID::v4();
        $objItemBiblioteca->criarItem($id_master, '', $objTpItemBiblioteca->getIdTibByMetanome('infNFe'),$id_uber);

        foreach ( $arrayNfe['infNFe'] as $informacoes => $conteudo ){
            switch ( $informacoes ){
                case '@attributes':
                    foreach ($conteudo as $chave_attributes => $valor_attributes){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_attributes, $objTpItemBiblioteca->getIdTibByMetanome($chave_attributes.'infNFe'),$id_master);
                    }
                    break;
                case 'ide':
                    //ide master
                    $id_ide = UUID::v4();
                    $objItemBiblioteca->criarItem($id_ide, '', $objTpItemBiblioteca->getIdTibByMetanome('ide'),$id_master);
                    if (isset($conteudo['NFRef'])){
                        //NFRef master
                        $id_NFRef = UUID::v4();
                        $objItemBiblioteca->criarItem($id_NFRef, '', $objTpItemBiblioteca->getIdTibByMetanome('NFRef'),$id_ide);
                        foreach ($conteudo['NFRef'] as $chave_NFRef => $valor_NFRef){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_NFRef, $objTpItemBiblioteca->getIdTibByMetanome($chave_NFRef.'NFRef'),$id_NFRef);
                            }
                        unset($conteudo['NFRef']);
                    }
                    foreach ($conteudo as $chave_ide => $valor_ide){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_ide, $objTpItemBiblioteca->getIdTibByMetanome($chave_ide.'ide'),$id_ide);
                    }
                    break;
                case 'infSuplem':
                    //compra master
                    $id_infSuplem = UUID::v4();
                    $objItemBiblioteca->criarItem($id_infSuplem, '', $objTpItemBiblioteca->getIdTibByMetanome('infSuplem'),$id_master);
                    if (isset($conteudo['qrCode'])){$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['qrCode'], $objTpItemBiblioteca->getIdTibByMetanome('qrCode'),$id_infSuplem);}
                    break;
                case 'compra':
                    //compra master
                    $id_compra = UUID::v4();
                    $objItemBiblioteca->criarItem($id_compra, '', $objTpItemBiblioteca->getIdTibByMetanome('compra'),$id_master);
                    foreach ($conteudo as $chave_compra => $valor_compra){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_compra, $objTpItemBiblioteca->getIdTibByMetanome($chave_compra),$id_compra);
                    }
                    break;
                case 'exporta':
                    //exporta master
                    $id_exporta = UUID::v4();
                    $objItemBiblioteca->criarItem($id_exporta, '', $objTpItemBiblioteca->getIdTibByMetanome('exporta'),$id_master);
                    foreach ($conteudo as $chave_exporta => $valor_exporta){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_exporta, $objTpItemBiblioteca->getIdTibByMetanome($chave_exporta),$id_exporta);
                    }
                    break;
                case 'cana':
                    //cana master
                    $id_cana = UUID::v4();
                    $objItemBiblioteca->criarItem($id_cana, '', $objTpItemBiblioteca->getIdTibByMetanome('cana'),$id_master);
                    if ($conteudo['deduc']){
                        //endere�o do Dest master
                        $id_deduc = UUID::v4();
                        $objItemBiblioteca->criarItem($id_deduc, '', $objTpItemBiblioteca->getIdTibByMetanome('deduc'),$id_cana);
                        foreach ( $conteudo['deduc'] as $chave_deduc => $valor_deduc){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_deduc, $objTpItemBiblioteca->getIdTibByMetanome($chave_deduc.'deduccana'),$id_deduc);
                        }
                        unset($conteudo['deduc']);
                    }
                    if ($conteudo['forDia']){
                        $id_forDia = UUID::v4();
                        $objItemBiblioteca->criarItem($id_forDia, '', $objTpItemBiblioteca->getIdTibByMetanome('forDia'),$id_cana);
                        foreach ( $conteudo['forDia'] as $chave_forDia => $valor_forDia){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_forDia, $objTpItemBiblioteca->getIdTibByMetanome($chave_forDia.'forDiacana'),$id_forDia);
                        }
                        unset($conteudo['forDia']);
                    }
                    foreach ($conteudo as $chave_cana => $valor_cana){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_cana, $objTpItemBiblioteca->getIdTibByMetanome($chave_cana),$id_cana);
                    }
                    break;
                case 'dest':
                    //dest master
                    $id_dest = UUID::v4();
                    $objItemBiblioteca->criarItem($id_dest, '', $objTpItemBiblioteca->getIdTibByMetanome('dest'),$id_master);
                    if ($conteudo['enderDest']){
                        //endere�o do Dest master
                        $id_enderDest = UUID::v4();
                        $objItemBiblioteca->criarItem($id_enderDest, '', $objTpItemBiblioteca->getIdTibByMetanome('enderDest'),$id_dest);
                        foreach ( $conteudo['enderDest'] as $chave_enderDest => $valor_enderDest){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_enderDest, $objTpItemBiblioteca->getIdTibByMetanome($chave_enderDest.'dest'),$id_enderDest);
                        }
                        unset($conteudo['enderDest']);
                    }
                    foreach ($conteudo as $chave_dest => $valor_dest){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_dest, $objTpItemBiblioteca->getIdTibByMetanome($chave_dest.'dest'),$id_dest);
                    }

                    break;

                    /*
                     * revisar essa parte do codigo... eu adicionei o
                     * if (is_array($produto)){
                                switch (key($produto)) {

                    antes apenas existia os foreach (prod, imposto e med)...

                    fiz os testes com o arquivo 53150918188449000190550010000000691020401314-procNfe.xml da AGECOM
                     */
                case 'det':
                    //det master
                    $id_det = UUID::v4();
                    $objItemBiblioteca->criarItem($id_det, '', $objTpItemBiblioteca->getIdTibByMetanome('det'),$id_master);
                    foreach($conteudo as $itemProd) {
                        foreach ($itemProd['prod'] as $chave_prod => $valor_prod){
                            //prod master
                            $id_produto = UUID::v4();
                            $objItemBiblioteca->criarItem($id_produto, '', $objTpItemBiblioteca->getIdTibByMetanome('prod'),$id_det);
                            if (is_array($valor_prod)){
                                foreach ( $valor_prod as $tipo_produto => $valor_tipo_produto){
                                    switch ($tipo_produto){
                                        case 'comb':
                                            //criar comb master
                                            $id_combustivel = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_combustivel, '', $objTpItemBiblioteca->getIdTibByMetanome('comb'),$id_produto);
                                            if ($valor_tipo_produto['Encerrante']){
                                                //Encerrante master
                                                $id_encerrante = UUID::v4();
                                                $objItemBiblioteca->criarItem($id_encerrante, '', $objTpItemBiblioteca->getIdTibByMetanome('Encerrante'),$id_combustivel);
                                                foreach ($valor_tipo_produto['Encerrante'] as $chave_encerrante => $valor_encerrante){
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_encerrante, $objTpItemBiblioteca->getIdTibByMetanome($chave_encerrante),$id_encerrante);
                                                }
                                                unset($valor_tipo_produto['Encerrante']);
                                            }
                                            if ($valor_tipo_produto['CIDE']){
                                                //CIDE master
                                                $id_cide = UUID::v4();
                                                $objItemBiblioteca->criarItem($id_cide, '', $objTpItemBiblioteca->getIdTibByMetanome('CIDE'),$id_combustivel);
                                                foreach ($valor_tipo_produto['CIDE'] as $chave_cide => $valor_cide){
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_cide, $objTpItemBiblioteca->getIdTibByMetanome($chave_cide),$id_cide);
                                                }
                                                unset($valor_tipo_produto['CIDE']);
                                            }
                                            foreach ($valor_tipo_produto as $chave_v1 => $valor_v1){
                                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_v1, $objTpItemBiblioteca->getIdTibByMetanome($chave_v1),$id_combustivel);
                                            }
                                            break;
                                        case 'arma':
                                            //arma master
                                            $id_arma = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_arma, '', $objTpItemBiblioteca->getIdTibByMetanome('arma'),$id_produto);
                                            foreach ($valor_tipo_produto['armaItem'] as $chave_arma => $valor_arma){
                                                if ( $chave_arma != 'nSerie'){
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_arma, $objTpItemBiblioteca->getIdTibByMetanome($chave_arma),$id_arma);
                                                }else{
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_arma, $objTpItemBiblioteca->getIdTibByMetanome($chave_arma.'arma'),$id_arma);
                                                }
                                            }
                                            break;
                                        case 'veicProd':
                                            //veiculo master
                                            $id_veiculo = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_veiculo, '', $objTpItemBiblioteca->getIdTibByMetanome('veicProd'),$id_produto);
                                            foreach ($valor_tipo_produto as $chave_veiculo => $valor_veiculo){
                                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_veiculo, $objTpItemBiblioteca->getIdTibByMetanome($chave_veiculo.'veicProd'),$id_veiculo);
                                            }
                                            break;
                                        case 'detExport':
                                            //detExport master
                                            $id_detExport = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_detExport, '', $objTpItemBiblioteca->getIdTibByMetanome('detExport'),$id_produto);
                                            foreach ($valor_tipo_produto['exportInd'] as $chave_exportInd => $valor_exportInd){
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_exportInd, $objTpItemBiblioteca->getIdTibByMetanome($chave_exportInd),$id_detExport);
                                            }
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_tipo_produto['nDraw'], $objTpItemBiblioteca->getIdTibByMetanome('nDraw'),$id_detExport);
                                            break;
                                        case 'detDI';
                                            //detDI master
                                            $id_detDI = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_detDI, '', $objTpItemBiblioteca->getIdTibByMetanome('detDI'),$id_produto);
                                            if (isset($valor_tipo_produto['detAdicoes'])){
                                                //detAdicoes master
                                                $id_detAdicoes = UUID::v4();
                                                $objItemBiblioteca->criarItem($id_detAdicoes, '', $objTpItemBiblioteca->getIdTibByMetanome('detAdicoes'),$id_detDI);
                                                foreach ($valor_tipo_produto['detAdicoes'] as $chave_detAdicoes => $valor_detAdicoes){
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_detAdicoes, $objTpItemBiblioteca->getIdTibByMetanome($chave_detAdicoes),$id_detAdicoes);
                                                }
                                                unset($valor_tipo_produto['detAdicoes']);
                                            }
                                            foreach ($valor_tipo_produto as $chave_detDI => $valor_detDI){
                                                if ( $chave_detDI != 'CNPJ'){
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_detDI, $objTpItemBiblioteca->getIdTibByMetanome($chave_detDI),$id_detDI);
                                                }else{
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_detDI, $objTpItemBiblioteca->getIdTibByMetanome($chave_detDI.'adq'),$id_detDI);
                                                }
                                            }
                                            break;
                                        case 'NVEs':
                                            //NVEs master
                                            $id_NVEs = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_NVEs, '', $objTpItemBiblioteca->getIdTibByMetanome('NVEs'),$id_produto);
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_tipo_produto['cNVE'], $objTpItemBiblioteca->getIdTibByMetanome('cNVE'),$id_NVEs);
                                            break;
                                    }
                                }
                            } else {
                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_prod, $objTpItemBiblioteca->getIdTibByMetanome($chave_prod),$id_produto);
                            }
                        }

                        foreach ($itemProd['imposto'] as $chave_imposto => $imposto){
                            //imposto  master
                            $id_imposto = UUID::v4();
                            $objItemBiblioteca->criarItem($id_imposto, '', $objTpItemBiblioteca->getIdTibByMetanome('imposto'),$id_det);
                            switch ( $chave_imposto ){
                                case 'ICMS':
                                    //criar ICMS master
                                    $id_icms = UUID::v4();
                                    $objItemBiblioteca->criarItem($id_icms, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS'),$id_imposto);
                                    //ICMSSN900
                                    if (isset($imposto['ICMSSN900'])){
                                        //master
                                        $id_ICMSSN900 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMSSN900, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSSN900'),$id_icms);
                                        foreach ($imposto['ICMSSN900'] as $chave_ICMSSN900 => $valor_ICMSSN900 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSSN900, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSSN900.'ICMSSN900'),$id_ICMSSN900);
                                        }
                                    }
                                    //ICMSSN500
                                    if (isset($imposto['ICMSSN500'])){
                                        //master
                                        $id_ICMSSN500 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMSSN500, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSSN500'),$id_icms);
                                        foreach ($imposto['ICMSSN500'] as $chave_ICMSSN500 => $valor_ICMSSN500 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSSN500, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSSN500.'ICMSSN500'),$id_ICMSSN500);
                                        }
                                    }
                                    //ICMSSN202
                                    if (isset($imposto['ICMSSN202'])){
                                        //master
                                        $id_ICMSSN202 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMSSN202, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSSN202'),$id_icms);
                                        foreach ($imposto['ICMSSN202'] as $chave_ICMSSN202 => $valor_ICMSSN202 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSSN202, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSSN202.'ICMSSN202'),$id_ICMSSN202);
                                        }
                                    }
                                    //ICMSSN201
                                    if (isset($imposto['ICMSSN201'])){
                                        //master
                                        $id_ICMSSN201 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMSSN201, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSSN201'),$id_icms);
                                        foreach ($imposto['ICMSSN201'] as $chave_ICMSSN201 => $valor_ICMSSN201 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSSN201, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSSN201.'ICMSSN201'),$id_ICMSSN201);
                                        }
                                    }
                                    //ICMSSN102
                                    if (isset($imposto['ICMSSN102'])){
                                        //master
                                        $id_ICMSSN102 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMSSN102, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSSN102'),$id_icms);
                                        foreach ($imposto['ICMSSN102'] as $chave_ICMSSN102 => $valor_ICMSSN102 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSSN102, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSSN102.'ICMSSN102'),$id_ICMSSN102);
                                        }
                                    }
                                    //ICMSSN101
                                    if (isset($imposto['ICMSSN101'])){
                                        //master
                                        $id_ICMSSN101 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMSSN101, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSSN101'),$id_icms);
                                        foreach ($imposto['ICMSSN101'] as $chave_ICMSSN101 => $valor_ICMSSN101 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSSN101, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSSN101.'ICMSSN101'),$id_ICMSSN101);
                                        }
                                    }
                                    //ICMSST
                                    if (isset($imposto['ICMSST'])){
                                        //master
                                        $id_ICMSST = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMSST, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSST'),$id_icms);
                                        foreach ($imposto['ICMSST'] as $chave_ICMSST => $valor_ICMSST ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSST, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSST.'ICMSST'),$id_ICMSST);
                                        }
                                    }
                                    //ICMSPart
                                    if (isset($imposto['ICMSPart'])){
                                        //master
                                        $id_ICMSPart = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMSPart, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSPart'),$id_icms);
                                        foreach ($imposto['ICMSPart'] as $chave_ICMSPart => $valor_ICMSPart ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSPart, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSPart.'ICMSPart'),$id_ICMSPart);
                                        }
                                    }
                                    //ICMS90
                                    if (isset($imposto['ICMS90'])){
                                        //master
                                        $id_ICMS90 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMS90, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS90'),$id_icms);
                                        foreach ($imposto['ICMS90'] as $chave_ICMS90 => $valor_ICMS90 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS90, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS90.'ICMS90'),$id_ICMS90);
                                        }
                                    }
                                    //ICMS70
                                    if (isset($imposto['ICMS90'])){
                                        //master
                                        $id_ICMS90 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMS90, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS90'),$id_icms);
                                        foreach ($imposto['ICMS90'] as $chave_ICMS90 => $valor_ICMS90 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS90, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS90.'ICMS90'),$id_ICMS90);
                                        }
                                    }
                                    //ICMS60
                                    if (isset($imposto['ICMS60'])){
                                        //master
                                        $id_ICMS60 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMS60, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS60'),$id_icms);
                                        foreach ($imposto['ICMS60'] as $chave_ICMS60 => $valor_ICMS60 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS60, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS60.'ICMS60'),$id_ICMS60);
                                        }
                                    }
                                    //ICMS51
                                    if (isset($imposto['ICMS51'])){
                                        //master
                                        $id_ICMS51 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMS51, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS51'),$id_icms);
                                        foreach ($imposto['ICMS51'] as $chave_ICMS51 => $valor_ICMS51 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS51, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS51.'ICMS51'),$id_ICMS51);
                                        }
                                    }
                                    //ICMS40
                                    if (isset($imposto['ICMS40'])){
                                        //master
                                        $id_ICMS40 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMS40, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS40'),$id_icms);
                                        foreach ($imposto['ICMS40'] as $chave_ICMS40 => $valor_ICMS40 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS40, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS40.'ICMS40'),$id_ICMS40);
                                        }
                                    }

                                    //ICMS30
                                    if (isset($imposto['ICMS30'])){
                                        //master
                                        $id_ICMS30 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMS30, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS30'),$id_icms);
                                        foreach ($imposto['ICMS30'] as $chave_ICMS30 => $valor_ICMS30 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS30, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS30.'ICMS30'),$id_ICMS30);
                                        }
                                    }
                                    //ICMS20
                                    if (isset($imposto['ICMS20'])){
                                        //master
                                        $id_ICMS20 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMS20, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS20'),$id_icms);
                                        foreach ($imposto['ICMS20'] as $chave_ICMS20 => $valor_ICMS20 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS20, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS20.'ICMS20'),$id_ICMS20);
                                        }
                                    }
                                    //ICMS10
                                    if (isset($imposto['ICMS10'])){
                                        //master
                                        $id_ICMS10 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMS10, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS10'),$id_icms);
                                        foreach ($imposto['ICMS10'] as $chave_ICMS10 => $valor_ICMS10 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS10, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS10.'ICMS10'),$id_ICMS10);
                                        }
                                    }
                                    //ICMS00
                                    if (isset($imposto['ICMS00'])){
                                        //master
                                        $id_ICMS00 = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ICMS00, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS00'),$id_icms);
                                        foreach ($imposto['ICMS00'] as $chave_ICMS00 => $valor_ICMS00 ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS00, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS00.'ICMS00'),$id_ICMS00);
                                        }
                                    }
                                    break;
                                case'IPI':
                                    //criar IPI master
                                    $id_ipi = UUID::v4();
                                    $objItemBiblioteca->criarItem($id_ipi, '', $objTpItemBiblioteca->getIdTibByMetanome('IPI'),$id_imposto);
                                    if (isset($imposto['IPINT'])){
                                        //IPINT master
                                        $id_ipi_nt = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ipi_nt, '', $objTpItemBiblioteca->getIdTibByMetanome('IPINT'),$id_ipi);
                                        foreach ($imposto['IPINT'] as $chave_ipint => $valor_ipint ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_ipint, $objTpItemBiblioteca->getIdTibByMetanome($chave_ipint.'IPINT'),$id_ipi_nt);
                                        }
                                        unset($imposto['IPINT']);
                                    }
                                    if (isset($imposto['IPITrib'])){
                                        //IPITrib MASTER
                                        $id_ipi_trib = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_ipi_trib, '', $objTpItemBiblioteca->getIdTibByMetanome('IPITrib'),$id_ipi);
                                        foreach ($imposto['IPITrib'] as $tributo_ipi => $valor_tributo_ipi){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_tributo_ipi, $objTpItemBiblioteca->getIdTibByMetanome($tributo_ipi.'IPITrib'),$id_ipi_trib);
                                        }
                                        unset($imposto['IPITrib']);
                                    }
                                    foreach ($imposto as $chave_ipi => $valor_ipi){
                                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_ipi, $objTpItemBiblioteca->getIdTibByMetanome($chave_ipi),$id_ipi);
                                    }
                                    break;
                                case 'vTotTrib':
                                    $objItemBiblioteca->criarItem(UUID::v4(), $imposto, $objTpItemBiblioteca->getIdTibByMetanome('vTotTrib'),$id_imposto);
                                    break;
                                case 'ISSQN':
                                    //criar ISSQN master
                                    $id_issqn = UUID::v4();
                                    $objItemBiblioteca->criarItem($id_issqn, '', $objTpItemBiblioteca->getIdTibByMetanome('ISSQN'),$id_imposto);
                                    foreach ($imposto as $chave_issqn => $valor_issqn){
                                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_issqn, $objTpItemBiblioteca->getIdTibByMetanome($chave_issqn.'ISSQN'),$id_issqn);
                                    }
                                    break;
                                case 'COFINSST':
                                    //criar COFINSST master
                                    $id_cofinsst = UUID::v4();
                                    $objItemBiblioteca->criarItem($id_cofinsst, '', $objTpItemBiblioteca->getIdTibByMetanome('COFINSST'),$id_imposto);
                                    foreach ($imposto as $chave_cofinsst => $valor_cofinsst){
                                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_cofinsst, $objTpItemBiblioteca->getIdTibByMetanome($chave_cofinsst.'cofinsST'),$id_cofinsst);
                                    }
                                    break;
                                case 'COFINS':
                                    //COFINS master
                                    $id_cofins = UUID::v4();
                                    $objItemBiblioteca->criarItem($id_cofins, '', $objTpItemBiblioteca->getIdTibByMetanome('COFINS'),$id_imposto);
                                    if (isset($imposto['COFINSOutr'])){
                                        //COFINSOutr master
                                        $id_COFINSOutr = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_COFINSOutr, '', $objTpItemBiblioteca->getIdTibByMetanome('COFINSOutr'),$id_cofins);
                                        foreach ($imposto['COFINSOutr'] as $chave_COFINSOutr => $valor_COFINSOutr ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_COFINSOutr, $objTpItemBiblioteca->getIdTibByMetanome($chave_COFINSOutr.'COFINSOutr'),$id_COFINSOutr);
                                        }
                                        unset($imposto['COFINSOutr']);
                                    }
                                    if (isset($imposto['COFINSNT'])){
                                        //COFINSNT master
                                        $id_COFINSNT = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_COFINSNT, '', $objTpItemBiblioteca->getIdTibByMetanome('COFINSNT'),$id_cofins);
                                        foreach ($imposto['COFINSNT'] as $chave_COFINSNT => $valor_COFINSNT ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_COFINSNT, $objTpItemBiblioteca->getIdTibByMetanome($chave_COFINSNT.'COFINSNT'),$id_COFINSNT);
                                        }
                                        unset($imposto['COFINSNT']);
                                    }
                                    if (isset($imposto['COFINSQtde'])){
                                        //COFINSQtde master
                                        $id_COFINSQtde = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_COFINSQtde, '', $objTpItemBiblioteca->getIdTibByMetanome('COFINSQtde'),$id_cofins);
                                        foreach ($imposto['COFINSQtde'] as $chave_COFINSQtde => $valor_COFINSQtde ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_COFINSQtde, $objTpItemBiblioteca->getIdTibByMetanome($chave_COFINSQtde.'COFINSQtde'),$id_COFINSQtde);
                                        }
                                        unset($imposto['COFINSQtde']);
                                    }
                                    if (isset($imposto['COFINSAliq'])){
                                        //COFINSAliq master
                                        $id_COFINSAliq = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_COFINSAliq, '', $objTpItemBiblioteca->getIdTibByMetanome('COFINSAliq'),$id_cofins);
                                        foreach ($imposto['COFINSAliq'] as $chave_COFINSAliq => $valor_COFINSAliq ){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_COFINSAliq, $objTpItemBiblioteca->getIdTibByMetanome($chave_COFINSAliq.'COFINSAliq'),$id_COFINSAliq);
                                        }
                                        unset($imposto['COFINSAliq']);
                                    }
                                    break;
                                case 'PISST':
                                    //criar PISST master
                                    $id_pisst = UUID::v4();
                                    $objItemBiblioteca->criarItem($id_pisst, '', $objTpItemBiblioteca->getIdTibByMetanome('PISST'),$id_imposto);
                                    foreach ($imposto as $chave_pisst => $valor_pisst){
                                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_pisst, $objTpItemBiblioteca->getIdTibByMetanome($chave_pisst.'PISST'),$id_pisst);
                                    }
                                case 'PIS':
                                    //criar PIS master
                                    $id_pis = UUID::v4();
                                    $objItemBiblioteca->criarItem($id_pis, '', $objTpItemBiblioteca->getIdTibByMetanome('PIS'),$id_imposto);
                                    if(isset($imposto['PISQtde'])){
                                        //PISQtde master
                                        $id_PISQtde = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_PISQtde, '', $objTpItemBiblioteca->getIdTibByMetanome('PISQtde'),$id_pis);
                                        foreach ($imposto['PISQtde'] as $chave_PISQtde => $valor_PISQtde){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_PISQtde, $objTpItemBiblioteca->getIdTibByMetanome($chave_PISQtde.'PISQtde'),$id_PISQtde);
                                        }
                                        unset($imposto['PISQtde']);
                                    }
                                    if(isset($imposto['PISAliq'])){
                                        //PISAliq master
                                        $id_PISAliq = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_PISAliq, '', $objTpItemBiblioteca->getIdTibByMetanome('PISAliq'),$id_pis);
                                        foreach ($imposto['PISAliq'] as $chave_PISAliq => $valor_PISAliq){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_PISAliq, $objTpItemBiblioteca->getIdTibByMetanome($chave_PISAliq.'PISAliq'),$id_PISAliq);
                                        }
                                        unset($imposto['PISAliq']);
                                    }
                                    if(isset($imposto['PISOutr'])){
                                        //PISOutr master
                                        $id_PISOutr = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_PISOutr, '', $objTpItemBiblioteca->getIdTibByMetanome('PISOutr'),$id_pis);
                                        foreach ($imposto['PISOutr'] as $chave_PISOutr => $valor_PISOutr){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_PISOutr, $objTpItemBiblioteca->getIdTibByMetanome($chave_PISOutr.'PISOutr'),$id_PISOutr);
                                        }
                                        unset($imposto['PISOutr']);
                                    }
                                    if(isset($imposto['PISNT'])){
                                        //PISNT master
                                        $id_PISNT = UUID::v4();
                                        $objItemBiblioteca->criarItem($id_PISNT, '', $objTpItemBiblioteca->getIdTibByMetanome('PISNT'),$id_pis);
                                        foreach ($imposto['PISNT'] as $chave_PISNT => $valor_PISNT){
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_PISNT, $objTpItemBiblioteca->getIdTibByMetanome($chave_PISNT.'PISNT'),$id_PISNT);
                                        }
                                        unset($imposto['PISNT']);
                                    }
                                    break;
                                case 'II':
                                    //criar ii master
                                    $id_ii = UUID::v4();
                                    $objItemBiblioteca->criarItem($id_ii, '', $objTpItemBiblioteca->getIdTibByMetanome('II'),$id_imposto);
                                    foreach ($imposto as $chave_ii => $valor_ii){
                                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_ii, $objTpItemBiblioteca->getIdTibByMetanome($chave_ii.'II'),$id_ii);
                                    }
                                    break;
                            }
                        }

                        if(isset($conteudo['med'])){
                            foreach ($conteudo['med'] as $produto_med){
                                $id_med = UUID::v4();
                                $objItemBiblioteca->criarItem($id_med, '', $objTpItemBiblioteca->getIdTibByMetanome('med'),$id_det);
                                    foreach ($produto_med as $medItem => $med_valor){
                                        $objItemBiblioteca->criarItem(UUID::v4(), $med_valor, $objTpItemBiblioteca->getIdTibByMetanome($medItem),$id_med);
                                }
                            }
                        }
                    }
                    

                    break;
                case 'total':
                    //total master
                    $id_total = UUID::v4();
                    $objItemBiblioteca->criarItem($id_total, '', $objTpItemBiblioteca->getIdTibByMetanome('total'),$id_master);
                    foreach ($conteudo as $chave_total => $valor_total){
                        switch ($chave_total) {
                            case 'retTrib';
                                //retTrib master
                                $id_retTrib = UUID::v4();
                                $objItemBiblioteca->criarItem($id_retTrib, '', $objTpItemBiblioteca->getIdTibByMetanome('retTrib'),$id_total);
                                foreach ($valor_total as $chave_retTrib => $valor_retTrib ){
                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_retTrib, $objTpItemBiblioteca->getIdTibByMetanome($chave_retTrib.'retTrib'),$id_retTrib);
                                }
                                break;
                            case 'ISSQNtot':
                                //ISSQNtot master
                                $id_ISSQNtot = UUID::v4();
                                $objItemBiblioteca->criarItem($id_ISSQNtot, '', $objTpItemBiblioteca->getIdTibByMetanome('ISSQNtot'),$id_total);
                                foreach ($valor_total as $chave_ISSQNtot => $valor_ISSQNtot) {
                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ISSQNtot, $objTpItemBiblioteca->getIdTibByMetanome($chave_ISSQNtot.'ISSQNtot'),$id_ISSQNtot);
                                }
                                break;
                            case 'ICMSTot';
                                //ICMStot master
                                $id_ICMStot = UUID::v4();
                                $objItemBiblioteca->criarItem($id_ICMStot, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMStot'),$id_total);
                                foreach ($valor_total as $chave_ICMStot => $valor_ICMStot) {
                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMStot, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMStot.'ICMStot'),$id_ICMStot);
                                }
                                break;
                            }
                        }
                    break;
                case 'transp':
                    //transp master
                    $id_transp = UUID::v4();
                    $objItemBiblioteca->criarItem($id_transp, '', $objTpItemBiblioteca->getIdTibByMetanome('transp'),$id_master);
                    //vol  master
                    if ( isset($conteudo['vol'])){
                        $id_vol = UUID::v4();
                        $objItemBiblioteca->criarItem($id_vol, '', $objTpItemBiblioteca->getIdTibByMetanome('vol'),$id_transp);
                        //lacres master
                        if ( isset($conteudo['vol']['lacres'])){
                            $id_lacres = UUID::v4();
                            $objItemBiblioteca->criarItem($id_lacres, '', $objTpItemBiblioteca->getIdTibByMetanome('lacres'),$id_vol);
                            foreach ($conteudo['vol']['lacres'] as $chave_lacre => $valor_lacre){
                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_lacre, $objTpItemBiblioteca->getIdTibByMetanome($chave_lacre),$id_lacres);
                            }
                            unset($conteudo['vol']['lacres']);
                        }
                        foreach ( $conteudo['vol'] as $chave_vol => $valor_vol){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_vol, $objTpItemBiblioteca->getIdTibByMetanome($chave_vol.'voltransp'),$id_vol);
                        }
                    }
                    if ( isset($conteudo['reboque'])){
                        //reboque master
                        $id_reboque = UUID::v4();
                        $objItemBiblioteca->criarItem($id_reboque, '', $objTpItemBiblioteca->getIdTibByMetanome('reboque'),$id_transp);
                        foreach ( $conteudo['reboque'] as $chave_reboque => $valor_reboque){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_reboque, $objTpItemBiblioteca->getIdTibByMetanome($chave_reboque.'rebtransp'),$id_reboque);
                        }
                    }
                    if ( isset($conteudo['veicTransp'])){
                        //veicTransp master
                        $id_veicTransp = UUID::v4();
                        $objItemBiblioteca->criarItem($id_veicTransp, '', $objTpItemBiblioteca->getIdTibByMetanome('veicTransp'),$id_transp);
                        foreach ($conteudo['veicTransp'] as $chave_veicTransp => $valor_veicTransp){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_veicTransp, $objTpItemBiblioteca->getIdTibByMetanome($chave_veicTransp.'veictransp'),$id_veicTransp);
                        }
                    }
                    if ( isset($conteudo['retTransp'])){
                        //retTransp master
                        $id_retTransp = UUID::v4();
                        $objItemBiblioteca->criarItem($id_retTransp, '', $objTpItemBiblioteca->getIdTibByMetanome('retTransp'),$id_transp);
                        foreach ($conteudo['retTransp'] as $chave_retTransp => $valor_retTransp){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_retTransp, $objTpItemBiblioteca->getIdTibByMetanome($chave_retTransp.'retTransp'),$id_retTransp);
                        }
                        unset($conteudo['retTransp']);
                    }
                    if ( isset($conteudo['transporta'])){
                        //transporta master
                        $id_transporta = UUID::v4();
                        $objItemBiblioteca->criarItem($id_transporta, '', $objTpItemBiblioteca->getIdTibByMetanome('transporta'),$id_transp);
                        foreach ($conteudo['transporta'] as $chave_transporta => $valor_transporta){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_transporta, $objTpItemBiblioteca->getIdTibByMetanome($chave_transporta.'transp'),$id_transporta);
                        }
                        unset($conteudo['transporta']);
                    }
                    //avulsos
                    //$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['vagao'], $objTpItemBiblioteca->getIdTibByMetanome('vagao'),$id_transp);
                    //$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['balsa'], $objTpItemBiblioteca->getIdTibByMetanome('balsa'),$id_transp);
                    $objItemBiblioteca->criarItem(UUID::v4(), $conteudo['modFrete'], $objTpItemBiblioteca->getIdTibByMetanome('modFrete'),$id_transp);
                    break;
                case 'infAdic':
                    //infAdic master
                    $id_infAdic = UUID::v4();
                    $objItemBiblioteca->criarItem($id_infAdic, '', $objTpItemBiblioteca->getIdTibByMetanome('infAdic'),$id_master);
                    //avulsos
                    if (isset($conteudo['infCpl'])){$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['infCpl'], $objTpItemBiblioteca->getIdTibByMetanome('infCpl'),$id_infAdic);}
                    if (isset($conteudo['infAdFisco'])){$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['infAdFisco'], $objTpItemBiblioteca->getIdTibByMetanome('infAdFisco'),$id_infAdic);}
                    if (isset($conteudo['obsFisco'])){
                        //obsFisco  master
                        $id_obsFisco = UUID::v4();
                        $objItemBiblioteca->criarItem($id_obsFisco, '', $objTpItemBiblioteca->getIdTibByMetanome('obsFisco'),$id_infAdic);
                        foreach ($conteudo['obsFisco'] as $chave_obsFisco => $valor_obsFisco ){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_obsFisco, $objTpItemBiblioteca->getIdTibByMetanome($chave_obsFisco.'obsFisco'),$id_obsFisco);
                        }
                        unset($conteudo['obsFisco']);
                    }
                    if (isset($conteudo['procRef'])){
                        //procRef  master
                        $id_procRef = UUID::v4();
                        $objItemBiblioteca->criarItem($id_procRef, '', $objTpItemBiblioteca->getIdTibByMetanome('procRef'),$id_infAdic);
                        foreach ($conteudo['procRef'] as $chave_procRef => $valor_procRef){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_procRef, $objTpItemBiblioteca->getIdTibByMetanome($chave_procRef),$id_procRef);
                        }
                        unset($conteudo['procRef']);
                    }
                    if (isset($conteudo['obsCont'])){
                        //obsCont  master
                        $id_obsCont = UUID::v4();
                        $objItemBiblioteca->criarItem($id_obsCont, '', $objTpItemBiblioteca->getIdTibByMetanome('obsCont'),$id_infAdic);
                        foreach ($conteudo['obsCont'] as $chave_obsCont => $valor_obsCont){
                            $tibnome = '';
                            if($chave_obsCont == '@attributes') {
                                $tibnome = 'obsCont';
                            } else {
                                $tibnome = $chave_obsCont.'obsCont';
                            }
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_obsCont, $objTpItemBiblioteca->getIdTibByMetanome($tibnome),$id_obsCont);
                        }
                        unset($conteudo['obsCont']);
                    }
                    break;
                case 'entrega':
                    //entrega master
                    $id_entrega = UUID::v4();
                    $objItemBiblioteca->criarItem($id_entrega, '', $objTpItemBiblioteca->getIdTibByMetanome('entrega'),$id_master);
                    foreach ($conteudo as $chave_entrega => $valor_entrega){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_entrega, $objTpItemBiblioteca->getIdTibByMetanome($chave_entrega.'entr'),$id_entrega);
                    }
                    break;
                case 'retirada':
                    //entrega master
                    $id_retirada = UUID::v4();
                    $objItemBiblioteca->criarItem($id_retirada, '', $objTpItemBiblioteca->getIdTibByMetanome('retirada'),$id_master);
                    foreach ($conteudo as $chave_retirada => $valor_retirada){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_retirada, $objTpItemBiblioteca->getIdTibByMetanome($chave_retirada.'ret'),$id_retirada);
                    }
                    break;
                case 'avulsa':
                    //avulsa master
                    $id_avulsa = UUID::v4();
                    $objItemBiblioteca->criarItem($id_avulsa, '', $objTpItemBiblioteca->getIdTibByMetanome('avulsa'),$id_master);
                    foreach ($conteudo as $chave_avulsa => $valor_avulsa){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_avulsa, $objTpItemBiblioteca->getIdTibByMetanome($chave_avulsa.'avulsa'),$id_avulsa);
                    }
                    break;
                case 'emit':
                    //emitente master
                    $id_emit = UUID::v4();
                    $objItemBiblioteca->criarItem($id_emit, '', $objTpItemBiblioteca->getIdTibByMetanome('emit'),$id_master);
                    if (isset($conteudo['enderEmit'])){
                        //endere�o do emitente master
                        $id_enderEmit = UUID::v4();
                        $objItemBiblioteca->criarItem($id_enderEmit, '', $objTpItemBiblioteca->getIdTibByMetanome('enderEmit'),$id_emit);
                        foreach ( $conteudo['enderEmit'] as $chave_enderEmit => $valor_enderEmit){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_enderEmit, $objTpItemBiblioteca->getIdTibByMetanome($chave_enderEmit.'emit'),$id_enderEmit);
                        }
                        unset($conteudo['enderEmit']);
                    }
                    foreach ($conteudo as $chave_emit => $valor_emit){
                        if ( $chave_emit != 'CPF_emit' || $chave_emit != 'CNPJ_emit'){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_emit, $objTpItemBiblioteca->getIdTibByMetanome($chave_emit.'emit'),$id_emit);
                        }else{
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_emit, $objTpItemBiblioteca->getIdTibByMetanome($chave_emit),$id_emit);
                        }
                    }
                    break;
                case 'pag':
                    //pag  master
                    $id_pag = UUID::v4();
                    $objItemBiblioteca->criarItem($id_pag, '', $objTpItemBiblioteca->getIdTibByMetanome('pag'),$id_master);
                    //avulsos
                    if (isset($conteudo['vPag'])){$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['vPag'], $objTpItemBiblioteca->getIdTibByMetanome('vPag'),$id_pag);}
                    if (isset($conteudo['tPag'])){$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['tPag'], $objTpItemBiblioteca->getIdTibByMetanome('tPag'),$id_pag);}
                    if (isset($conteudo['card'])){
                        //card master
                        $id_card = UUID::v4();
                        $objItemBiblioteca->criarItem($id_card, '', $objTpItemBiblioteca->getIdTibByMetanome('card'),$id_pag);
                        foreach ( $conteudo['card'] as $chave_card => $valor_card){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_card, $objTpItemBiblioteca->getIdTibByMetanome($chave_card.'card'),$id_card);
                        }
                    }
                    break;
                case 'cobr':
                    //cobr master
                    $id_cobr = UUID::v4();
                    $objItemBiblioteca->criarItem($id_cobr, '', $objTpItemBiblioteca->getIdTibByMetanome('cobr'),$id_master);
                    if (isset($conteudo['dup'])){
                        //dup master
                        $id_dup = UUID::v4();
                        $objItemBiblioteca->criarItem($id_dup, '', $objTpItemBiblioteca->getIdTibByMetanome('dup'),$id_cobr);
                        foreach ($conteudo['dup'] as $chave_dup => $valor_dup){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_dup, $objTpItemBiblioteca->getIdTibByMetanome($chave_dup),$id_dup);
                        }
                    }
                    if (isset($conteudo['fat'])){
                        //fat master
                        $id_fat = UUID::v4();
                        $objItemBiblioteca->criarItem($id_fat, '', $objTpItemBiblioteca->getIdTibByMetanome('fat'),$id_cobr);
                        foreach ($conteudo['fat'] as $chave_fat => $valor_fat){
                            if ($chave_fat != 'vDesc'){
                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_fat, $objTpItemBiblioteca->getIdTibByMetanome($chave_fat),$id_fat);
                            }else{
                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_fat, $objTpItemBiblioteca->getIdTibByMetanome($chave_fat.'cob'),$id_fat);
                            }
                        }
                    }
                    break;
            }
        }
    } elseif ($arrayNfe['infNFe']['@attributes']['versao'] == '2.00'){
        //NFe uber
        $id_uber = UUID::v4();
        $objItemBiblioteca->criarItem($id_uber, '', $objTpItemBiblioteca->getIdTibByMetanome('NFe'));
        //infNFe Master
        $id_master = UUID::v4();
        $objItemBiblioteca->criarItem($id_master, '', $objTpItemBiblioteca->getIdTibByMetanome('infNFe'),$id_uber);

        foreach ( $arrayNfe['infNFe'] as $informacoes => $conteudo ){
            switch ( $informacoes ){
                case '@attributes':
                    foreach ($conteudo as $chave_attributes => $valor_attributes){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_attributes, $objTpItemBiblioteca->getIdTibByMetanome($chave_attributes.'infNFe'),$id_master);
                    }
                    break;
                case 'ide':
                    //ide master
                    $id_ide = UUID::v4();
                    $objItemBiblioteca->criarItem($id_ide, '', $objTpItemBiblioteca->getIdTibByMetanome('ide'),$id_master);
                    if (isset($conteudo['NFRef'])){
                        //NFRef master
                        $id_NFRef = UUID::v4();
                        $objItemBiblioteca->criarItem($id_NFRef, '', $objTpItemBiblioteca->getIdTibByMetanome('NFRef'),$id_ide);
                        foreach ($conteudo['NFRef'] as $chave_NFRef => $valor_NFRef){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_NFRef, $objTpItemBiblioteca->getIdTibByMetanome($chave_NFRef.'NFRef'),$id_NFRef);
                            }
                        unset($conteudo['NFRef']);
                    }
                    foreach ($conteudo as $chave_ide => $valor_ide){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_ide, $objTpItemBiblioteca->getIdTibByMetanome($chave_ide.'ide'),$id_ide);
                    }
                    break;
                case 'infSuplem':
                    //compra master
                    $id_infSuplem = UUID::v4();
                    $objItemBiblioteca->criarItem($id_infSuplem, '', $objTpItemBiblioteca->getIdTibByMetanome('infSuplem'),$id_master);
                    if (isset($conteudo['qrCode'])){$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['qrCode'], $objTpItemBiblioteca->getIdTibByMetanome('qrCode'),$id_infSuplem);}
                    break;
                case 'compra':
                    //compra master
                    $id_compra = UUID::v4();
                    $objItemBiblioteca->criarItem($id_compra, '', $objTpItemBiblioteca->getIdTibByMetanome('compra'),$id_master);
                    foreach ($conteudo as $chave_compra => $valor_compra){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_compra, $objTpItemBiblioteca->getIdTibByMetanome($chave_compra),$id_compra);
                    }
                    break;
                case 'exporta':
                    //exporta master
                    $id_exporta = UUID::v4();
                    $objItemBiblioteca->criarItem($id_exporta, '', $objTpItemBiblioteca->getIdTibByMetanome('exporta'),$id_master);
                    foreach ($conteudo as $chave_exporta => $valor_exporta){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_exporta, $objTpItemBiblioteca->getIdTibByMetanome($chave_exporta),$id_exporta);
                    }
                    break;
                case 'cana':
                    //cana master
                    $id_cana = UUID::v4();
                    $objItemBiblioteca->criarItem($id_cana, '', $objTpItemBiblioteca->getIdTibByMetanome('cana'),$id_master);
                    if ($conteudo['deduc']){
                        //endere�o do Dest master
                        $id_deduc = UUID::v4();
                        $objItemBiblioteca->criarItem($id_deduc, '', $objTpItemBiblioteca->getIdTibByMetanome('deduc'),$id_cana);
                        foreach ( $conteudo['deduc'] as $chave_deduc => $valor_deduc){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_deduc, $objTpItemBiblioteca->getIdTibByMetanome($chave_deduc.'deduccana'),$id_deduc);
                        }
                        unset($conteudo['deduc']);
                    }
                    if ($conteudo['forDia']){
                        $id_forDia = UUID::v4();
                        $objItemBiblioteca->criarItem($id_forDia, '', $objTpItemBiblioteca->getIdTibByMetanome('forDia'),$id_cana);
                        foreach ( $conteudo['forDia'] as $chave_forDia => $valor_forDia){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_forDia, $objTpItemBiblioteca->getIdTibByMetanome($chave_forDia.'ForDiacana'),$id_forDia);
                        }
                        unset($conteudo['forDia']);
                    }
                    foreach ($conteudo as $chave_cana => $valor_cana){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_cana, $objTpItemBiblioteca->getIdTibByMetanome($chave_cana),$id_cana);
                    }
                    break;
                case 'dest':
                    //dest master
                    $id_dest = UUID::v4();
                    $objItemBiblioteca->criarItem($id_dest, '', $objTpItemBiblioteca->getIdTibByMetanome('dest'),$id_master);
                    if ($conteudo['enderDest']){
                        //endere�o do Dest master
                        $id_enderDest = UUID::v4();
                        $objItemBiblioteca->criarItem($id_enderDest, '', $objTpItemBiblioteca->getIdTibByMetanome('enderDest'),$id_dest);
                        foreach ( $conteudo['enderDest'] as $chave_enderDest => $valor_enderDest){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_enderDest, $objTpItemBiblioteca->getIdTibByMetanome($chave_enderDest.'dest'),$id_enderDest);
                        }
                        unset($conteudo['enderDest']);
                    }
                    foreach ($conteudo as $chave_dest => $valor_dest){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_dest, $objTpItemBiblioteca->getIdTibByMetanome($chave_dest.'dest'),$id_dest);
                    }

                    break;

                    /*
                     * revisar essa parte do codigo... eu adicionei o
                     * if (is_array($produto)){
                                switch (key($produto)) {

                    antes apenas existia os foreach (prod, imposto e med)...

                    fiz os testes com o arquivo 53150918188449000190550010000000691020401314-procNfe.xml da AGECOM
                     */
                case 'det':
                    //det master
                    $id_det = UUID::v4();
                    $objItemBiblioteca->criarItem($id_det, '', $objTpItemBiblioteca->getIdTibByMetanome('det'),$id_master);
                    foreach ($conteudo as $conteudo_do_produto){
                        foreach ($conteudo_do_produto as $chave_prod => $valor_prod){
                            //prod master
                            $id_produto = UUID::v4();
                            $objItemBiblioteca->criarItem($id_produto, '', $objTpItemBiblioteca->getIdTibByMetanome('prod'),$id_det);
                            if (is_array($valor_prod)){
                                foreach ( $valor_prod as $tipo_produto => $valor_tipo_produto){
                                    switch ($chave_prod){
                                        case 'comb':
                                            //criar comb master
                                            $id_combustivel = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_combustivel, '', $objTpItemBiblioteca->getIdTibByMetanome('comb'),$id_produto);
                                            if ($valor_tipo_produto['Encerrante']){
                                                //Encerrante master
                                                $id_encerrante = UUID::v4();
                                                $objItemBiblioteca->criarItem($id_encerrante, '', $objTpItemBiblioteca->getIdTibByMetanome('Encerrante'),$id_combustivel);
                                                foreach ($valor_tipo_produto['Encerrante'] as $chave_encerrante => $valor_encerrante){
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_encerrante, $objTpItemBiblioteca->getIdTibByMetanome($chave_encerrante),$id_encerrante);
                                                }
                                                unset($valor_tipo_produto['Encerrante']);
                                            }
                                            if ($valor_tipo_produto['CIDE']){
                                                //CIDE master
                                                $id_cide = UUID::v4();
                                                $objItemBiblioteca->criarItem($id_cide, '', $objTpItemBiblioteca->getIdTibByMetanome('CIDE'),$id_combustivel);
                                                foreach ($valor_tipo_produto['CIDE'] as $chave_cide => $valor_cide){
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_cide, $objTpItemBiblioteca->getIdTibByMetanome($chave_cide),$id_cide);
                                                }
                                                unset($valor_tipo_produto['CIDE']);
                                            }
                                            foreach ($valor_tipo_produto as $chave_v1 => $valor_v1){
                                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_v1, $objTpItemBiblioteca->getIdTibByMetanome($chave_v1),$id_combustivel);
                                            }
                                            break;
                                        case 'arma':
                                            //arma master
                                            $id_arma = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_arma, '', $objTpItemBiblioteca->getIdTibByMetanome('arma'),$id_produto);
                                            foreach ($valor_tipo_produto['armaItem'] as $chave_arma => $valor_arma){
                                                if ( $chave_arma != 'nSerie'){
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_arma, $objTpItemBiblioteca->getIdTibByMetanome($chave_arma),$id_arma);
                                                }else{
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_arma, $objTpItemBiblioteca->getIdTibByMetanome($chave_arma.'arma'),$id_arma);
                                                }
                                            }
                                            break;
                                        case 'veicProd':
                                            //veiculo master
                                            $id_veiculo = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_veiculo, '', $objTpItemBiblioteca->getIdTibByMetanome('veicProd'),$id_produto);
                                            foreach ($valor_tipo_produto as $chave_veiculo => $valor_veiculo){
                                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_veiculo, $objTpItemBiblioteca->getIdTibByMetanome($chave_veiculo.'veicProd'),$id_veiculo);
                                            }
                                            break;
                                        case 'detExport':
                                            //detExport master
                                            $id_detExport = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_detExport, '', $objTpItemBiblioteca->getIdTibByMetanome('detExport'),$id_produto);
                                            foreach ($valor_tipo_produto['exportInd'] as $chave_exportInd => $valor_exportInd){
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_exportInd, $objTpItemBiblioteca->getIdTibByMetanome($chave_exportInd),$id_detExport);
                                            }
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_tipo_produto['nDraw'], $objTpItemBiblioteca->getIdTibByMetanome('nDraw'),$id_detExport);
                                            break;
                                        case 'detDI';
                                            //detDI master
                                            $id_detDI = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_detDI, '', $objTpItemBiblioteca->getIdTibByMetanome('detDI'),$id_produto);
                                            if (isset($valor_tipo_produto['detAdicoes'])){
                                                //detAdicoes master
                                                $id_detAdicoes = UUID::v4();
                                                $objItemBiblioteca->criarItem($id_detAdicoes, '', $objTpItemBiblioteca->getIdTibByMetanome('detAdicoes'),$id_detDI);
                                                foreach ($valor_tipo_produto['detAdicoes'] as $chave_detAdicoes => $valor_detAdicoes){
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_detAdicoes, $objTpItemBiblioteca->getIdTibByMetanome($chave_detAdicoes),$id_detAdicoes);
                                                }
                                                unset($valor_tipo_produto['detAdicoes']);
                                            }
                                            foreach ($valor_tipo_produto as $chave_detDI => $valor_detDI){
                                                if ( $chave_detDI != 'CNPJ'){
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_detDI, $objTpItemBiblioteca->getIdTibByMetanome($chave_detDI),$id_detDI);
                                                }else{
                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_detDI, $objTpItemBiblioteca->getIdTibByMetanome($chave_detDI.'adq'),$id_detDI);
                                                }
                                            }
                                            break;
                                        case 'NVEs':
                                            //NVEs master
                                            $id_NVEs = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_NVEs, '', $objTpItemBiblioteca->getIdTibByMetanome('NVEs'),$id_produto);
                                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_tipo_produto['cNVE'], $objTpItemBiblioteca->getIdTibByMetanome('cNVE'),$id_NVEs);
                                            break;
                                        case 'prod':
                                            if (!is_array($valor_tipo_produto)) {
                                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_tipo_produto, $objTpItemBiblioteca->getIdTibByMetanome($tipo_produto),$id_produto);
                                            }
                                            break;
                                        case 'imposto':
                                            $id_imposto = UUID::v4();
                                            $objItemBiblioteca->criarItem($id_imposto, '', $objTpItemBiblioteca->getIdTibByMetanome('imposto'),$id_det);
                                            if (is_array($conteudo['imposto'])){
                                                foreach ( $conteudo['imposto'] as $chave_imposto => $imposto){
                                                    switch ( $chave_imposto ){
                                                        case 'ICMS':
                                                            //criar ICMS master
                                                            $id_icms = UUID::v4();
                                                            $objItemBiblioteca->criarItem($id_icms, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS'),$id_imposto);
                                                            //ICMSSN900
                                                            if (isset($imposto['ICMSSN900'])){
                                                                //master
                                                                $id_ICMSSN900 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMSSN900, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSSN900'),$id_icms);
                                                                foreach ($imposto['ICMSSN900'] as $chave_ICMSSN900 => $valor_ICMSSN900 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSSN900, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSSN900.'ICMSSN900'),$id_ICMSSN900);
                                                                }
                                                            }
                                                            //ICMSSN500
                                                            if (isset($imposto['ICMSSN500'])){
                                                                //master
                                                                $id_ICMSSN500 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMSSN500, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSSN500'),$id_icms);
                                                                foreach ($imposto['ICMSSN500'] as $chave_ICMSSN500 => $valor_ICMSSN500 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSSN500, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSSN500.'ICMSSN500'),$id_ICMSSN500);
                                                                }
                                                            }
                                                            //ICMSSN202
                                                            if (isset($imposto['ICMSSN202'])){
                                                                //master
                                                                $id_ICMSSN202 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMSSN202, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSSN202'),$id_icms);
                                                                foreach ($imposto['ICMSSN202'] as $chave_ICMSSN202 => $valor_ICMSSN202 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSSN202, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSSN202.'ICMSSN202'),$id_ICMSSN202);
                                                                }
                                                            }
                                                            //ICMSSN201
                                                            if (isset($imposto['ICMSSN201'])){
                                                                //master
                                                                $id_ICMSSN201 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMSSN201, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSSN201'),$id_icms);
                                                                foreach ($imposto['ICMSSN201'] as $chave_ICMSSN201 => $valor_ICMSSN201 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSSN201, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSSN201.'ICMSSN201'),$id_ICMSSN201);
                                                                }
                                                            }
                                                            //ICMSSN102
                                                            if (isset($imposto['ICMSSN102'])){
                                                                //master
                                                                $id_ICMSSN102 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMSSN102, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSSN102'),$id_icms);
                                                                foreach ($imposto['ICMSSN102'] as $chave_ICMSSN102 => $valor_ICMSSN102 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSSN102, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSSN102.'ICMSSN102'),$id_ICMSSN102);
                                                                }
                                                            }
                                                            //ICMSSN101
                                                            if (isset($imposto['ICMSSN101'])){
                                                                //master
                                                                $id_ICMSSN101 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMSSN101, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSSN101'),$id_icms);
                                                                foreach ($imposto['ICMSSN101'] as $chave_ICMSSN101 => $valor_ICMSSN101 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSSN101, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSSN101.'ICMSSN101'),$id_ICMSSN101);
                                                                }
                                                            }
                                                            //ICMSST
                                                            if (isset($imposto['ICMSST'])){
                                                                //master
                                                                $id_ICMSST = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMSST, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSST'),$id_icms);
                                                                foreach ($imposto['ICMSST'] as $chave_ICMSST => $valor_ICMSST ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSST, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSST.'ICMSST'),$id_ICMSST);
                                                                }
                                                            }
                                                            //ICMSPart
                                                            if (isset($imposto['ICMSPart'])){
                                                                //master
                                                                $id_ICMSPart = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMSPart, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMSPart'),$id_icms);
                                                                foreach ($imposto['ICMSPart'] as $chave_ICMSPart => $valor_ICMSPart ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMSPart, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMSPart.'ICMSPart'),$id_ICMSPart);
                                                                }
                                                            }
                                                            //ICMS90
                                                            if (isset($imposto['ICMS90'])){
                                                                //master
                                                                $id_ICMS90 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMS90, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS90'),$id_icms);
                                                                foreach ($imposto['ICMS90'] as $chave_ICMS90 => $valor_ICMS90 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS90, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS90.'ICMS90'),$id_ICMS90);
                                                                }
                                                            }
                                                            //ICMS70
                                                            if (isset($imposto['ICMS90'])){
                                                                //master
                                                                $id_ICMS90 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMS90, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS90'),$id_icms);
                                                                foreach ($imposto['ICMS90'] as $chave_ICMS90 => $valor_ICMS90 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS90, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS90.'ICMS90'),$id_ICMS90);
                                                                }
                                                            }
                                                            //ICMS60
                                                            if (isset($imposto['ICMS60'])){
                                                                //master
                                                                $id_ICMS60 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMS60, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS60'),$id_icms);
                                                                foreach ($imposto['ICMS60'] as $chave_ICMS60 => $valor_ICMS60 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS60, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS60.'ICMS60'),$id_ICMS60);
                                                                }
                                                            }
                                                            //ICMS51
                                                            if (isset($imposto['ICMS51'])){
                                                                //master
                                                                $id_ICMS51 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMS51, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS51'),$id_icms);
                                                                foreach ($imposto['ICMS51'] as $chave_ICMS51 => $valor_ICMS51 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS51, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS51.'ICMS51'),$id_ICMS51);
                                                                }
                                                            }
                                                            //ICMS40
                                                            if (isset($imposto['ICMS40'])){
                                                                //master
                                                                $id_ICMS40 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMS40, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS40'),$id_icms);
                                                                foreach ($imposto['ICMS40'] as $chave_ICMS40 => $valor_ICMS40 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS40, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS40.'ICMS40'),$id_ICMS40);
                                                                }
                                                            }

                                                            //ICMS30
                                                            if (isset($imposto['ICMS30'])){
                                                                //master
                                                                $id_ICMS30 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMS30, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS30'),$id_icms);
                                                                foreach ($imposto['ICMS30'] as $chave_ICMS30 => $valor_ICMS30 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS30, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS30.'ICMS30'),$id_ICMS30);
                                                                }
                                                            }
                                                            //ICMS20
                                                            if (isset($imposto['ICMS20'])){
                                                                //master
                                                                $id_ICMS20 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMS20, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS20'),$id_icms);
                                                                foreach ($imposto['ICMS20'] as $chave_ICMS20 => $valor_ICMS20 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS20, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS20.'ICMS20'),$id_ICMS20);
                                                                }
                                                            }
                                                            //ICMS10
                                                            if (isset($imposto['ICMS10'])){
                                                                //master
                                                                $id_ICMS10 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMS10, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS10'),$id_icms);
                                                                foreach ($imposto['ICMS10'] as $chave_ICMS10 => $valor_ICMS10 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS10, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS10.'ICMS10'),$id_ICMS10);
                                                                }
                                                            }
                                                            //ICMS00
                                                            if (isset($imposto['ICMS00'])){
                                                                //master
                                                                $id_ICMS00 = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ICMS00, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMS00'),$id_icms);
                                                                foreach ($imposto['ICMS00'] as $chave_ICMS00 => $valor_ICMS00 ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMS00, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMS00.'ICMS00'),$id_ICMS00);
                                                                }
                                                            }
                                                            break;
                                                        case'IPI':
                                                            //criar IPI master
                                                            $id_ipi = UUID::v4();
                                                            $objItemBiblioteca->criarItem($id_ipi, '', $objTpItemBiblioteca->getIdTibByMetanome('IPI'),$id_imposto);
                                                            if (isset($imposto['IPINT'])){
                                                                //IPINT master
                                                                $id_ipi_nt = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ipi_nt, '', $objTpItemBiblioteca->getIdTibByMetanome('IPINT'),$id_ipi);
                                                                foreach ($imposto['IPINT'] as $chave_ipint => $valor_ipint ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ipint, $objTpItemBiblioteca->getIdTibByMetanome($chave_ipint.'IPINT'),$id_ipi_nt);
                                                                }
                                                                unset($imposto['IPINT']);
                                                            }
                                                            if (isset($imposto['IPITrib'])){
                                                                //IPITrib MASTER
                                                                $id_ipi_trib = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_ipi_trib, '', $objTpItemBiblioteca->getIdTibByMetanome('IPITrib'),$id_ipi);
                                                                foreach ($imposto['IPITrib'] as $tributo_ipi => $valor_tributo_ipi){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_tributo_ipi, $objTpItemBiblioteca->getIdTibByMetanome($tributo_ipi.'IPITrib'),$id_ipi_trib);
                                                                }
                                                                unset($imposto['IPITrib']);
                                                            }
                                                            foreach ($imposto as $chave_ipi => $valor_ipi){
                                                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_ipi, $objTpItemBiblioteca->getIdTibByMetanome($chave_ipi),$id_ipi);
                                                            }
                                                            break;
                                                        case 'vTotTrib':
                                                            $objItemBiblioteca->criarItem(UUID::v4(), $imposto, $objTpItemBiblioteca->getIdTibByMetanome('vTotTrib'),$id_imposto);
                                                            break;
                                                        case 'ISSQN':
                                                            //criar ISSQN master
                                                            $id_issqn = UUID::v4();
                                                            $objItemBiblioteca->criarItem($id_issqn, '', $objTpItemBiblioteca->getIdTibByMetanome('ISSQN'),$id_imposto);
                                                            foreach ($imposto as $chave_issqn => $valor_issqn){
                                                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_issqn, $objTpItemBiblioteca->getIdTibByMetanome($chave_issqn.'ISSQN'),$id_issqn);
                                                            }
                                                            break;
                                                        case 'COFINSST':
                                                            //criar COFINSST master
                                                            $id_cofinsst = UUID::v4();
                                                            $objItemBiblioteca->criarItem($id_cofinsst, '', $objTpItemBiblioteca->getIdTibByMetanome('COFINSST'),$id_imposto);
                                                            foreach ($imposto as $chave_cofinsst => $valor_cofinsst){
                                                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_cofinsst, $objTpItemBiblioteca->getIdTibByMetanome($chave_cofinsst.'cofinsST'),$id_cofinsst);
                                                            }
                                                            break;
                                                        case 'COFINS':
                                                            //COFINS master
                                                            $id_cofins = UUID::v4();
                                                            $objItemBiblioteca->criarItem($id_cofins, '', $objTpItemBiblioteca->getIdTibByMetanome('COFINS'),$id_imposto);
                                                            if (isset($imposto['COFINSOutr'])){
                                                                //COFINSOutr master
                                                                $id_COFINSOutr = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_COFINSOutr, '', $objTpItemBiblioteca->getIdTibByMetanome('COFINSOutr'),$id_cofins);
                                                                foreach ($imposto['COFINSOutr'] as $chave_COFINSOutr => $valor_COFINSOutr ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_COFINSOutr, $objTpItemBiblioteca->getIdTibByMetanome($chave_COFINSOutr.'COFINSOutr'),$id_COFINSOutr);
                                                                }
                                                                unset($imposto['COFINSOutr']);
                                                            }
                                                            if (isset($imposto['COFINSNT'])){
                                                                //COFINSNT master
                                                                $id_COFINSNT = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_COFINSNT, '', $objTpItemBiblioteca->getIdTibByMetanome('COFINSNT'),$id_cofins);
                                                                foreach ($imposto['COFINSNT'] as $chave_COFINSNT => $valor_COFINSNT ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_COFINSNT, $objTpItemBiblioteca->getIdTibByMetanome($chave_COFINSNT.'COFINSNT'),$id_COFINSNT);
                                                                }
                                                                unset($imposto['COFINSNT']);
                                                            }
                                                            if (isset($imposto['COFINSQtde'])){
                                                                //COFINSQtde master
                                                                $id_COFINSQtde = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_COFINSQtde, '', $objTpItemBiblioteca->getIdTibByMetanome('COFINSQtde'),$id_cofins);
                                                                foreach ($imposto['COFINSQtde'] as $chave_COFINSQtde => $valor_COFINSQtde ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_COFINSQtde, $objTpItemBiblioteca->getIdTibByMetanome($chave_COFINSQtde.'COFINSQtde'),$id_COFINSQtde);
                                                                }
                                                                unset($imposto['COFINSQtde']);
                                                            }
                                                            if (isset($imposto['COFINSAliq'])){
                                                                //COFINSAliq master
                                                                $id_COFINSAliq = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_COFINSAliq, '', $objTpItemBiblioteca->getIdTibByMetanome('COFINSAliq'),$id_cofins);
                                                                foreach ($imposto['COFINSAliq'] as $chave_COFINSAliq => $valor_COFINSAliq ){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_COFINSAliq, $objTpItemBiblioteca->getIdTibByMetanome($chave_COFINSAliq.'COFINSAliq'),$id_COFINSAliq);
                                                                }
                                                                unset($imposto['COFINSAliq']);
                                                            }
                                                            break;
                                                        case 'PISST':
                                                            //criar PISST master
                                                            $id_pisst = UUID::v4();
                                                            $objItemBiblioteca->criarItem($id_pisst, '', $objTpItemBiblioteca->getIdTibByMetanome('PISST'),$id_imposto);
                                                            foreach ($imposto as $chave_pisst => $valor_pisst){
                                                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_pisst, $objTpItemBiblioteca->getIdTibByMetanome($chave_pisst.'PISST'),$id_pisst);
                                                            }
                                                        case 'PIS':
                                                            //criar PIS master
                                                            $id_pis = UUID::v4();
                                                            $objItemBiblioteca->criarItem($id_pis, '', $objTpItemBiblioteca->getIdTibByMetanome('PIS'),$id_imposto);
                                                            if(isset($imposto['PISQtde'])){
                                                                //PISQtde master
                                                                $id_PISQtde = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_PISQtde, '', $objTpItemBiblioteca->getIdTibByMetanome('PISQtde'),$id_pis);
                                                                foreach ($imposto['PISQtde'] as $chave_PISQtde => $valor_PISQtde){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_PISQtde, $objTpItemBiblioteca->getIdTibByMetanome($chave_PISQtde.'PISQtde'),$id_PISQtde);
                                                                }
                                                                unset($imposto['PISQtde']);
                                                            }
                                                            if(isset($imposto['PISAliq'])){
                                                                //PISAliq master
                                                                $id_PISAliq = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_PISAliq, '', $objTpItemBiblioteca->getIdTibByMetanome('PISAliq'),$id_pis);
                                                                foreach ($imposto['PISAliq'] as $chave_PISAliq => $valor_PISAliq){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_PISAliq, $objTpItemBiblioteca->getIdTibByMetanome($chave_PISAliq.'PISAliq'),$id_PISAliq);
                                                                }
                                                                unset($imposto['PISAliq']);
                                                            }
                                                            if(isset($imposto['PISOutr'])){
                                                                //PISOutr master
                                                                $id_PISOutr = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_PISOutr, '', $objTpItemBiblioteca->getIdTibByMetanome('PISOutr'),$id_pis);
                                                                foreach ($imposto['PISOutr'] as $chave_PISOutr => $valor_PISOutr){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_PISOutr, $objTpItemBiblioteca->getIdTibByMetanome($chave_PISOutr.'PISOutr'),$id_PISOutr);
                                                                }
                                                                unset($imposto['PISOutr']);
                                                            }
                                                            if(isset($imposto['PISNT'])){
                                                                //PISNT master
                                                                $id_PISNT = UUID::v4();
                                                                $objItemBiblioteca->criarItem($id_PISNT, '', $objTpItemBiblioteca->getIdTibByMetanome('PISNT'),$id_pis);
                                                                foreach ($imposto['PISNT'] as $chave_PISNT => $valor_PISNT){
                                                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_PISNT, $objTpItemBiblioteca->getIdTibByMetanome($chave_PISNT.'PISNT'),$id_PISNT);
                                                                }
                                                                unset($imposto['PISNT']);
                                                            }
                                                            break;
                                                        case 'II':
                                                            //criar ii master
                                                            $id_ii = UUID::v4();
                                                            $objItemBiblioteca->criarItem($id_ii, '', $objTpItemBiblioteca->getIdTibByMetanome('II'),$id_imposto);
                                                            foreach ($imposto as $chave_ii => $valor_ii){
                                                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_ii, $objTpItemBiblioteca->getIdTibByMetanome($chave_ii.'II'),$id_ii);
                                                            }
                                                            break;
                                                        }
                                                    }
                                                }
                                            break;
                                    }
                                }
                            } else {
                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_prod, $objTpItemBiblioteca->getIdTibByMetanome($chave_prod),$id_produto);
                            }
                        }
                    }
                    if(isset($conteudo['med'])){
                        foreach ($conteudo['med'] as $produto_med){
                            $id_med = UUID::v4();
                            $objItemBiblioteca->criarItem($id_med, '', $objTpItemBiblioteca->getIdTibByMetanome('med'),$id_det);
                                foreach ($produto_med as $medItem => $med_valor){
                                    $objItemBiblioteca->criarItem(UUID::v4(), $med_valor, $objTpItemBiblioteca->getIdTibByMetanome($medItem),$id_med);
                            }
                        }
                    }

                    break;
                case 'total':
                    //total master
                    $id_total = UUID::v4();
                    $objItemBiblioteca->criarItem($id_total, '', $objTpItemBiblioteca->getIdTibByMetanome('total'),$id_master);
                    foreach ($conteudo as $chave_total => $valor_total){
                        switch ($chave_total) {
                            case 'retTrib';
                                //retTrib master
                                $id_retTrib = UUID::v4();
                                $objItemBiblioteca->criarItem($id_retTrib, '', $objTpItemBiblioteca->getIdTibByMetanome('retTrib'),$id_total);
                                foreach ($valor_total as $chave_retTrib => $valor_retTrib ){
                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_retTrib, $objTpItemBiblioteca->getIdTibByMetanome($chave_retTrib.'retTrib'),$id_retTrib);
                                }
                                break;
                            case 'ISSQNtot':
                                //ISSQNtot master
                                $id_ISSQNtot = UUID::v4();
                                $objItemBiblioteca->criarItem($id_ISSQNtot, '', $objTpItemBiblioteca->getIdTibByMetanome('ISSQNtot'),$id_total);
                                foreach ($valor_total as $chave_ISSQNtot => $valor_ISSQNtot) {
                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ISSQNtot, $objTpItemBiblioteca->getIdTibByMetanome($chave_ISSQNtot.'ISSQNtot'),$id_ISSQNtot);
                                }
                                break;
                            case 'ICMSTot';
                                //ICMStot master
                                $id_ICMStot = UUID::v4();
                                $objItemBiblioteca->criarItem($id_ICMStot, '', $objTpItemBiblioteca->getIdTibByMetanome('ICMStot'),$id_total);
                                foreach ($valor_total as $chave_ICMStot => $valor_ICMStot) {
                                    $objItemBiblioteca->criarItem(UUID::v4(), $valor_ICMStot, $objTpItemBiblioteca->getIdTibByMetanome($chave_ICMStot.'ICMStot'),$id_ICMStot);
                                }
                                break;
                            }
                        }
                    break;
                case 'transp':
                    //transp master
                    $id_transp = UUID::v4();
                    $objItemBiblioteca->criarItem($id_transp, '', $objTpItemBiblioteca->getIdTibByMetanome('transp'),$id_master);
                    //vol  master
                    if ( isset($conteudo['vol'])){
                        $id_vol = UUID::v4();
                        $objItemBiblioteca->criarItem($id_vol, '', $objTpItemBiblioteca->getIdTibByMetanome('vol'),$id_transp);
                        //lacres master
                        if ( isset($conteudo['vol']['lacres'])){
                            $id_lacres = UUID::v4();
                            $objItemBiblioteca->criarItem($id_lacres, '', $objTpItemBiblioteca->getIdTibByMetanome('lacres'),$id_vol);
                            foreach ($conteudo['vol']['lacres'] as $chave_lacre => $valor_lacre){
                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_lacre, $objTpItemBiblioteca->getIdTibByMetanome($chave_lacre),$id_lacres);
                            }
                            unset($conteudo['vol']['lacres']);
                        }
                        foreach ( $conteudo['vol'] as $chave_vol => $valor_vol){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_vol, $objTpItemBiblioteca->getIdTibByMetanome($chave_vol.'voltransp'),$id_vol);
                        }
                    }
                    if ( isset($conteudo['reboque'])){
                        //reboque master
                        $id_reboque = UUID::v4();
                        $objItemBiblioteca->criarItem($id_reboque, '', $objTpItemBiblioteca->getIdTibByMetanome('reboque'),$id_transp);
                        foreach ( $conteudo['reboque'] as $chave_reboque => $valor_reboque){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_reboque, $objTpItemBiblioteca->getIdTibByMetanome($chave_reboque.'rebtransp'),$id_reboque);
                        }
                    }
                    if ( isset($conteudo['veicTransp'])){
                        //veicTransp master
                        $id_veicTransp = UUID::v4();
                        $objItemBiblioteca->criarItem($id_veicTransp, '', $objTpItemBiblioteca->getIdTibByMetanome('veicTransp'),$id_transp);
                        foreach ($conteudo['veicTransp'] as $chave_veicTransp => $valor_veicTransp){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_veicTransp, $objTpItemBiblioteca->getIdTibByMetanome($chave_veicTransp.'veictransp'),$id_veicTransp);
                        }
                    }
                    if ( isset($conteudo['retTransp'])){
                        //retTransp master
                        $id_retTransp = UUID::v4();
                        $objItemBiblioteca->criarItem($id_retTransp, '', $objTpItemBiblioteca->getIdTibByMetanome('retTransp'),$id_transp);
                        foreach ($conteudo['retTransp'] as $chave_retTransp => $valor_retTransp){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_retTransp, $objTpItemBiblioteca->getIdTibByMetanome($chave_retTransp.'retTransp'),$id_retTransp);
                        }
                        unset($conteudo['retTransp']);
                    }
                    if ( isset($conteudo['transporta'])){
                        //transporta master
                        $id_transporta = UUID::v4();
                        $objItemBiblioteca->criarItem($id_transporta, '', $objTpItemBiblioteca->getIdTibByMetanome('transporta'),$id_transp);
                        foreach ($conteudo['transporta'] as $chave_transporta => $valor_transporta){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_transporta, $objTpItemBiblioteca->getIdTibByMetanome($chave_transporta.'transp'),$id_transporta);
                        }
                        unset($conteudo['transporta']);
                    }
                    //avulsos
                    //$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['vagao'], $objTpItemBiblioteca->getIdTibByMetanome('vagao'),$id_transp);
                    //$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['balsa'], $objTpItemBiblioteca->getIdTibByMetanome('balsa'),$id_transp);
                    $objItemBiblioteca->criarItem(UUID::v4(), $conteudo['modFrete'], $objTpItemBiblioteca->getIdTibByMetanome('modFrete'),$id_transp);
                    break;
                case 'infAdic':
                    //infAdic master
                    $id_infAdic = UUID::v4();
                    $objItemBiblioteca->criarItem($id_infAdic, '', $objTpItemBiblioteca->getIdTibByMetanome('infAdic'),$id_master);
                    //avulsos
                    if (isset($conteudo['infCpl'])){$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['infCpl'], $objTpItemBiblioteca->getIdTibByMetanome('infCpl'),$id_infAdic);}
                    if (isset($conteudo['infAdFisco'])){$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['infAdFisco'], $objTpItemBiblioteca->getIdTibByMetanome('infAdFisco'),$id_infAdic);}
                    if (isset($conteudo['obsFisco'])){
                        //obsFisco  master
                        $id_obsFisco = UUID::v4();
                        $objItemBiblioteca->criarItem($id_obsFisco, '', $objTpItemBiblioteca->getIdTibByMetanome('obsFisco'),$id_infAdic);
                        foreach ($conteudo['obsFisco'] as $chave_obsFisco => $valor_obsFisco ){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_obsFisco, $objTpItemBiblioteca->getIdTibByMetanome($chave_obsFisco.'obsFisco'),$id_obsFisco);
                        }
                        unset($conteudo['obsFisco']);
                    }
                    if (isset($conteudo['procRef'])){
                        //procRef  master
                        $id_procRef = UUID::v4();
                        $objItemBiblioteca->criarItem($id_procRef, '', $objTpItemBiblioteca->getIdTibByMetanome('procRef'),$id_infAdic);
                        foreach ($conteudo['procRef'] as $chave_procRef => $valor_procRef){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_procRef, $objTpItemBiblioteca->getIdTibByMetanome($chave_procRef),$id_procRef);
                        }
                        unset($conteudo['procRef']);
                    }
                    if (isset($conteudo['obsCont'])){
                        //obsCont  master
                        $id_obsCont = UUID::v4();
                        $objItemBiblioteca->criarItem($id_obsCont, '', $objTpItemBiblioteca->getIdTibByMetanome('obsCont'),$id_infAdic);
                        foreach ($conteudo['obsCont'] as $chave_obsCont => $valor_obsCont){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_obsCont, $objTpItemBiblioteca->getIdTibByMetanome($chave_obsCont.'obsCont'),$id_obsCont);
                        }
                    unset($conteudo['obsCont']);
                    }
                    break;
                case 'entrega':
                    //entrega master
                    $id_entrega = UUID::v4();
                    $objItemBiblioteca->criarItem($id_entrega, '', $objTpItemBiblioteca->getIdTibByMetanome('entrega'),$id_master);
                    foreach ($conteudo as $chave_entrega => $valor_entrega){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_entrega, $objTpItemBiblioteca->getIdTibByMetanome($chave_entrega.'entr'),$id_entrega);
                    }
                    break;
                case 'retirada':
                    //entrega master
                    $id_retirada = UUID::v4();
                    $objItemBiblioteca->criarItem($id_retirada, '', $objTpItemBiblioteca->getIdTibByMetanome('retirada'),$id_master);
                    foreach ($conteudo as $chave_retirada => $valor_retirada){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_retirada, $objTpItemBiblioteca->getIdTibByMetanome($chave_retirada.'ret'),$id_retirada);
                    }
                    break;
                case 'avulsa':
                    //avulsa master
                    $id_avulsa = UUID::v4();
                    $objItemBiblioteca->criarItem($id_avulsa, '', $objTpItemBiblioteca->getIdTibByMetanome('avulsa'),$id_master);
                    foreach ($conteudo as $chave_avulsa => $valor_avulsa){
                        $objItemBiblioteca->criarItem(UUID::v4(), $valor_avulsa, $objTpItemBiblioteca->getIdTibByMetanome($chave_avulsa.'avulsa'),$id_avulsa);
                    }
                    break;
                case 'emit':
                    //emitente master
                    $id_emit = UUID::v4();
                    $objItemBiblioteca->criarItem($id_emit, '', $objTpItemBiblioteca->getIdTibByMetanome('emit'),$id_master);
                    if (isset($conteudo['enderEmit'])){
                        //endere�o do emitente master
                        $id_enderEmit = UUID::v4();
                        $objItemBiblioteca->criarItem($id_enderEmit, '', $objTpItemBiblioteca->getIdTibByMetanome('enderEmit'),$id_emit);
                        foreach ( $conteudo['enderEmit'] as $chave_enderEmit => $valor_enderEmit){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_enderEmit, $objTpItemBiblioteca->getIdTibByMetanome($chave_enderEmit.'emit'),$id_enderEmit);
                        }
                        unset($conteudo['enderEmit']);
                    }
                    foreach ($conteudo as $chave_emit => $valor_emit){
                        if ( $chave_emit != 'CPF_emit' || $chave_emit != 'CNPJ_emit'){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_emit, $objTpItemBiblioteca->getIdTibByMetanome($chave_emit.'emit'),$id_emit);
                        }else{
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_emit, $objTpItemBiblioteca->getIdTibByMetanome($chave_emit),$id_emit);
                        }
                    }
                    break;
                case 'pag':
                    //pag  master
                    $id_pag = UUID::v4();
                    $objItemBiblioteca->criarItem($id_pag, '', $objTpItemBiblioteca->getIdTibByMetanome('pag'),$id_master);
                    //avulsos
                    if (isset($conteudo['vPag'])){$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['vPag'], $objTpItemBiblioteca->getIdTibByMetanome('vPag'),$id_pag);}
                    if (isset($conteudo['tPag'])){$objItemBiblioteca->criarItem(UUID::v4(), $conteudo['tPag'], $objTpItemBiblioteca->getIdTibByMetanome('tPag'),$id_pag);}
                    if (isset($conteudo['card'])){
                        //card master
                        $id_card = UUID::v4();
                        $objItemBiblioteca->criarItem($id_card, '', $objTpItemBiblioteca->getIdTibByMetanome('card'),$id_pag);
                        foreach ( $conteudo['card'] as $chave_card => $valor_card){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_card, $objTpItemBiblioteca->getIdTibByMetanome($chave_card.'card'),$id_card);
                        }
                    }
                    break;
                case 'cobr':
                    //cobr master
                    $id_cobr = UUID::v4();
                    $objItemBiblioteca->criarItem($id_cobr, '', $objTpItemBiblioteca->getIdTibByMetanome('cobr'),$id_master);
                    if (isset($conteudo['dup'])){
                        //dup master
                        $id_dup = UUID::v4();
                        $objItemBiblioteca->criarItem($id_dup, '', $objTpItemBiblioteca->getIdTibByMetanome('dup'),$id_cobr);
                        foreach ($conteudo['dup'] as $chave_dup => $valor_dup){
                            $objItemBiblioteca->criarItem(UUID::v4(), $valor_dup, $objTpItemBiblioteca->getIdTibByMetanome($chave_dup),$id_dup);
                        }
                    }
                    if (isset($conteudo['fat'])){
                        //fat master
                        $id_fat = UUID::v4();
                        $objItemBiblioteca->criarItem($id_fat, '', $objTpItemBiblioteca->getIdTibByMetanome('fat'),$id_cobr);
                        foreach ($conteudo['fat'] as $chave_fat => $valor_fat){
                            if ($chave_fat != 'vDesc'){
                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_fat, $objTpItemBiblioteca->getIdTibByMetanome($chave_fat),$id_fat);
                            }else{
                                $objItemBiblioteca->criarItem(UUID::v4(), $valor_fat, $objTpItemBiblioteca->getIdTibByMetanome($chave_fat.'cob'),$id_fat);
                            }
                        }
                    }
                break;
            }
        }

        $objRlGI->criaRlGrupoItem (UUID::v4(), $_SESSION['TIME']['ID'], $id_uber);

        DatabaseConnection::getInstance()->getConnection()->commit();
    } else {
        die( 'vers�o de nota n�o suportada pelo sistema!');
    }
} catch (PDOException $e) {
    DatabaseConnection::getInstance()->getConnection()->rollBack();
    die('Erro de PDO - ' . $e);
} catch (Exception $e) {
    DatabaseConnection::getInstance()->getConnection()->rollBack();
    die('Erro não PDO - ' . $e);
}

if(isset($SERVICO['metadata']['ws_url'])){
    $sucesso = getUrlContent($SERVICO['metadata']['ws_url'] . $id_ib_master);
}

//exit('foi?');