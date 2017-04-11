<?php

    $qryDel = $dbh->prepare("delete from rl_vinculo_pessoa where id = :id");
    $qryDel->bindParam(':id',$_REQUEST['id']);
    $qryDel->execute();

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

