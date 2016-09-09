<?php
/* REMOVENDO VINCULOS*/

if (!$_POST['id']) {
	// @todo erro!
	parseJson(true, 'Não existe pessoa cadastrada!');
} else {
	$uuidPessoa = $_POST['id'];
}

if (isset($SERVICO['id_grupo'])){
	$grupo = $SERVICO['id_grupo'];
} else {
	$grupo = $identity->time['id'];
}

require_once "UUID.php";

try {

	$dbh->beginTransaction();

	// prepare para delete de vinculos
	$queryDeleteVinculoPessoa = $dbh->prepare("
			delete from rl_vinculo_pessoa 
			where 
				id_pessoa = :id_pessoa and
				id_classificacao in ( 
					select id from tb_classificacao where metanome = ANY ( string_to_array( :ws_classificacao, ',') )
				) and
				id_grupo = :id_grupo");
	$queryDeleteVinculoPessoa->bindParam(':id_pessoa', $uuidPessoa);
	$queryDeleteVinculoPessoa->bindParam(':ws_classificacao' ,  $SERVICO['ws_classificacao']);
	$queryDeleteVinculoPessoa->bindParam(':id_grupo',  $grupo);
	$queryDeleteVinculoPessoa->execute();

	// prepare para delete de grupo informacao
	$queryDeleteGrupoInfo = $dbh->prepare("
			delete from rl_grupo_informacao 
			where 
				id_pessoa = :id_pessoa and
				id_grupo = :id_grupo");
	$queryDeleteGrupoInfo->bindParam(':id_pessoa', $uuidPessoa);
	$queryDeleteGrupoInfo->bindParam(':id_grupo',  $grupo);
	$queryDeleteGrupoInfo->execute();

	// resposta padrão para salvo com sucesso.
	$dbh->commit();
	//parseJson();
    if (isset($SERVICO['metadata']['ws_target']) && ($SERVICO['metadata']['ws_target'])) {
            $servico = new Servico();
            $servicoDestino = $servico->getServiceByMetanome($SERVICO['metadata']['ws_target']);
    } else {
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

/*  CODIGO DO SOL
    if (!$_POST['id']) {
        // @todo erro!
        parseJson(true, 'NÃ£o existe pessoa cadastrada!');
    } else {
        $uuidPessoa = $_POST['id'];
    }

    require_once "UUID.php";

    $data = $_POST;
    $tpInformacao = new TpInformacao();

    $campos = $tpInformacao->getTpInformacaoByPerfisByPessoaByGrupo($SERVICO['metadata']['ws_perfil'], $uuidPessoa, $_SESSION['TIME']['ID']);

    $arPerfil = explode(',', $SERVICO['metadata']['ws_perfil']);

    foreach($campos as $campo) {
        $arCampos[$campo['perfil']][] = $campo;
    }

    try {

        $dbh->beginTransaction();

        // prepare para delete
        $queryDeleteInformacao = $dbh->prepare("DELETE FROM rl_grupo_informacao where id_pessoa = :id_pessoa and id_grupo = :id_grupo and id = :id");

        foreach ($campos as $campo) {

            if ($campo['rl_grupo_informacao_id'] != $rlGrupoInformacaoId) {
                $queryDeleteInformacao->bindParam(':id_pessoa', $uuidPessoa);
                $queryDeleteInformacao->bindParam(':id_grupo', $_SESSION['TIME']['ID']);
                $queryDeleteInformacao->bindParam(':id', $campo['rl_grupo_informacao_id']);
                $queryDeleteInformacao->execute();
            }

            $rlGrupoInformacaoId = $campo['rl_grupo_informacao_id'];
        }

        // resposta padrÃ£o para salvo com sucesso.
        $dbh->commit();
        parseJson();
    } catch (PDOException $e) {
        // @todo verificar se vai ter algum tratamento especial para PDO.
        $dbh->rollBack();
        parseJson(true, $e->getMessage(), $e->getTraceAsString());
    } catch (exception $e) {
        $dbh->rollBack();
        parseJson(true, $e->getMessage(), $e->getTraceAsString());
    }
*/