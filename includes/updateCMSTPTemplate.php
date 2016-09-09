<?php
    include_once "connect.php";

    // echo "<pre>";
    // var_dump($_POST);

    // echo "--------\n";
    // echo "ID's\n";
    // echo "--------\n";
    // echo "idtib: {$idTib}\n";
    // echo "id_tptemplate: {$idCMSTPTemplate}\n";
    // echo "--------\n\n\n";

    # Verifica se já esxiste um dado cadastrado com idTib e id_tptemplate passados via ajax
    $query             = $dbh->prepare( "SELECT * FROM cms_template WHERE id_tib = :idTib AND id_tptemplate = :idTPTemplate" );
    $updateCMSTemplate = $dbh->prepare( "UPDATE cms_template SET html = :html, id_tib = :idtib, id_tptemplate = :idtptemplate WHERE id = :id" );

    $dbh->beginTransaction();

    if( isset( $_POST['busca'] ) && ( $_POST['busca'] == 'vai' ) ){

        try {

            $idTib           = $_POST['idTib'];
            $idCMSTPTemplate = $_POST['idCMSTPTemplate'];

            # atribuição dos params da $query
            $query->bindParam(':idTib', $idTib);
            $query->bindParam(':idTPTemplate', $idCMSTPTemplate);
            $query->execute();
            $retorno = $query->fetch( PDO::FETCH_ASSOC );

            # Se passsar na validação armazena as variaves vindas do post
            $id           = $retorno['id'];
            $html         = $retorno['html'];
            $idtib        = $retorno['id_tib'];
            $idtptemplate = $retorno['id_tptemplate'];

            # echo $retorno['html'];
            # echo "<input type='hidden' name='cmstemplate' value='{$id}'>";

            $arr = array(
                'dadoParaEditor' => $retorno['html'],
                'idcmstemplate'  => $id
            );

            echo json_encode( $arr );

        } catch ( PDOException $e ) {

            // echo $e->getMessage();
            echo "error";

        }

    }

    if( isset( $_POST['update'] ) && ( $_POST['update'] == 'vai' ) ){
        // var_dump($_POST);
        try{

            # vai update
            $dadosDoEditor = urldecode( $_POST['dadosDoEditor'] );
            $dadosDoSelect = $_POST['dadosDoSelect'];
            $idTib         = $_POST['idTib'];
            $id            = $_POST['idcmstemplate'];

            // echo "-----------\n";
            // echo "Dados update: \n";
            // echo "-----------\n";
            // echo "ID: {$id}\n";
            // echo "HTML Novo: {$dadosDoEditor}\n";
            // echo "IDTIB: {$idTib}\n";
            // echo "IDTEMPLATE: {$dadosDoSelect}\n";
            // echo "-----------\n";

            # Atribuição dos params da updateCMSTemplate
            $updateCMSTemplate->bindParam(':html', $dadosDoEditor);
            $updateCMSTemplate->bindParam(':idtib', $idTib);
            $updateCMSTemplate->bindParam(':idtptemplate', $dadosDoSelect);
            $updateCMSTemplate->bindParam(':id', $id);
            $updateCMSTemplate->execute();

            $dbh->commit();

            echo "Dados salvos com sucesso.\n";

        } catch ( PDOException $e ){

            // echo $e->getMessage();
            $dbh->rollBack();
            echo "error";

        }
    }
?>
