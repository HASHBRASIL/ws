<?php
if(isset($SERVICO['ws_conversacao'])){
        if(!isset($_SESSION[$SERVICO['ws_conversacao']])) {
                 $_SESSION[$SERVICO['ws_conversacao']] = array();
        }
} else {
        echo "Conversação não encontrada. Este formulário demanda configuração de conversação";
        die();
}

$pessoa = $_SESSION[$SERVICO['ws_conversacao']]['id_pessoa'];
//new dBug($pessoa);
$qryIns = $dbh->prepare("insert into rl_vinculo_pessoa
			(id,id_classificacao,id_pessoa,id_vinculado,id_grupo)
			values
			(uuid_generate_v4(),:idcls,:idpes,:idvinc,:idgrp)");

$qryIns->bindParam(':idcls',$_REQUEST['pessoa_CLASSIF']);
$qryIns->bindParam(':idpes',$_REQUEST['pessoa_PESSOA']);
$qryIns->bindParam(':idvinc',$pessoa);
$qryIns->bindParam(':idgrp',$identity->time['id']);

$qryIns->execute();

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

