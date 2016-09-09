<?php
    session_start();

    require_once "includes/error.php";

    require_once "includes/functions.php";

    spl_autoload_register('hash_autoloader');

    require_once "includes/databaseconnect.php";
    require_once "includes/connect.php";
    require_once "includes/twig.php";

	switch ($_SERVER['HTTP_HOST']) {
    case "127.0.0.1":
        define('ROOT', 'http://127.0.0.1/hash-ws-php/');
        break;
   }

    if (!isset($_SESSION['USUARIO'])) {
        header('location: index.php');
    } else {
        if (isset($_POST['pwd'])) {
            if ($_POST['pwd'] === $_SESSION['USUARIO']['SENHA']) {
                unset($_SESSION['USUARIO']['SALSESSAO']);
                unset($_SESSION['USUARIO']['SENHA']);
            } else {
                header('location: index.php');
            }
        }

        $idCurrentServico = ( isset( $_GET['servico'] ) ) ? $_GET['servico'] : '';

        if (!$idCurrentServico) {
            $idCurrentServico = ( isset( $_REQUEST['servico'] ) ) ? $_REQUEST['servico'] : getIdServico('hash_dashboard', $dbh)[0]['id'];
        }

        # Fazer select na tb_servico e servico_metadata
        $queryServico              = $dbh->prepare("SELECT * FROM tb_servico WHERE id = :idServico ");
        $queryServicoMetadata      = $dbh->prepare("SELECT * FROM tb_servico_metadata WHERE id_servico = :idServico");
        $queryServicoMetadataFilho = $dbh->prepare("SELECT sv.*, svm.metanome as ws_metanome, svm.valor FROM tb_servico sv JOIN tb_servico_metadata svm ON (sv.id = svm.id_servico) WHERE sv.id_pai = :idServico");

        $queryServico->bindParam( ":idServico", $idCurrentServico );
        $queryServicoMetadata->bindParam( ":idServico", $idCurrentServico );
        $queryServicoMetadataFilho->bindParam(':idServico', $idCurrentServico);

        try {
            $HASH_SERVICO = array();
            $queryServico->execute();
            $queryServicoMetadata->execute();
            $queryServicoMetadataFilho->execute();

            $servico              = $queryServico->fetchAll(PDO::FETCH_ASSOC);
            $servicoMetadata      = $queryServicoMetadata->fetchAll(PDO::FETCH_ASSOC);
            $servicoMetadataFilho = $queryServicoMetadataFilho->fetchAll(PDO::FETCH_ASSOC);

            // Variáveis
            $HASH_SERVICO['id']        = $servico[0]['id'];
            $HASH_SERVICO['nome']      = $servico[0]['nome'];
            $HASH_SERVICO['descricao'] = $servico[0]['descricao'];
            $HASH_SERVICO['fluxo']     = $servico[0]['fluxo'];
            $HASH_SERVICO['metanome']  = $servico[0]['metanome'];
            $HASH_SERVICO['id_tib']    = $servico[0]['id_tib'];
            $HASH_SERVICO['ordem']     = $servico[0]['ordem'];
            $HASH_SERVICO['visivel']   = $servico[0]['visivel'];
            $HASH_SERVICO['id_pai']    = $servico[0]['id_pai'];

            foreach ($servicoMetadataFilho as $key => $value) {

                $HASH_SERVICO['filhos'][$value['id']]['id']         = $value['id'];
                $HASH_SERVICO['filhos'][$value['id']]['descricao']  = $value['descricao'];
                $HASH_SERVICO['filhos'][$value['id']]['fluxo']      = $value['fluxo'];
                $HASH_SERVICO['filhos'][$value['id']]['metanome']   = $value['metanome'];
                $HASH_SERVICO['filhos'][$value['id']]['nome']       = $value['nome'];
                $HASH_SERVICO['filhos'][$value['id']]['id_pai']     = $value['id_pai'];
                $HASH_SERVICO['filhos'][$value['id']]['id_tib']     = $value['id_tib'];
                $HASH_SERVICO['filhos'][$value['id']]['visivel']    = $value['visivel'];
                $HASH_SERVICO['filhos'][$value['id']]['ordem']      = $value['ordem'];
                $HASH_SERVICO['filhos'][$value['id']]['metadata'][$value['ws_metanome']] = $value['valor'];

                if ($value['ws_metanome'] == 'ws_comportamento' && $value['valor'] == 'tab') {
                    $HASH_SERVICO['tab'] = true;
                }

                if ( empty( $HASH_SERVICO['filhos'][$value['id']]['metadata']['ws_arquivo'] ) ) {
                    switch ( $HASH_SERVICO['filhos'][$value['id']]['fluxo'] ) {
                        case "editar":
                            $HASH_SERVICO['filhos'][$value['id']]['metadata']['ws_arquivo'] = "editDataMaster.php";
                        break;

                        case "criar":
                            $HASH_SERVICO['filhos'][$value['id']]['metadata']['ws_arquivo'] = "createDataMaster.php";
                        break;

                        default:
                            $HASH_SERVICO['filhos'][$value['id']]['metadata']['ws_arquivo'] = "master.php";
                    }
                }
            }

            foreach ($servicoMetadata as $key =>$value) {
                $HASH_SERVICO['metadata'][$value['metanome']] = $value['valor'];
            }

            if ( empty( $HASH_SERVICO['metadata']['ws_arquivo'] ) ) {
                switch ( $HASH_SERVICO['fluxo'] ) {
                    case "editar":
                        $HASH_SERVICO['metadata']['ws_arquivo'] = "editDataMaster.php";
                    break;

                    case "criar":
                        $HASH_SERVICO['metadata']['ws_arquivo'] = "createDataMaster.php";
                    break;

                    default:
                        $HASH_SERVICO['metadata']['ws_arquivo'] = "master.php";
                }
            }

        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        if ( isset($HASH_SERVICO['metadata']['ws_show']) && $HASH_SERVICO['metadata']['ws_show'] != 'reload') {
            $SERVICO = $HASH_SERVICO;
            include 'includes/'. $SERVICO['metadata']['ws_arquivo'];
        } else {
            include "includes/header.php";
            include "layout.php";
            include "includes/footer.php";
        }
    }
