<?php
$pathRastro   = new Rastro();
$rastro	= $pathRastro->getPath($SERVICO['id']);

if(isset($SERVICO['ws_conversacao'])){
	if(!isset($_SESSION[$SERVICO['ws_conversacao']])) {
		 $_SESSION[$SERVICO['ws_conversacao']] = array();
	}
} else {
	echo "Conversação não encontrada. Este formulário demanda configuração de conversação";
	die();
}

if(isset($_REQUEST['id']) && $_REQUEST['id'] != "") {
	$_SESSION[$SERVICO['ws_conversacao']]['id_pessoa'] = $_REQUEST['id'];
	$idPessoa = $_REQUEST['id'];
} else if(isset($_SESSION[$SERVICO['ws_conversacao']]['id_pessoa'])) {
	$idPessoa = $_SESSION[$SERVICO['ws_conversacao']]['id_pessoa'];
} else {
	echo 'Pessoa não encontrada';
}

//			join rl_grupo_informacao rgi on (inf.id = rgi.id_info)

$qryVinc = $dbh->prepare("select rvp.id as id, inf.valor as nome, cls.nome as classificacao
			from tb_informacao inf join tp_informacao tinf on (inf.id_tinfo = tinf.id)
			join rl_vinculo_pessoa rvp on (inf.id_pessoa = rvp.id_pessoa)
			join tb_classificacao cls on (rvp.id_classificacao = cls.id)
			where rvp.id_grupo = :idTime and tinf.metanome in ('NOMEPESSOA','RAZAOSOCIAL')
			and rvp.id_vinculado = :idVinc order by valor");
$qryVinc->bindParam('idTime',$identity->time['id']);
$qryVinc->bindParam('idVinc',$idPessoa);
$qryVinc->execute();
$data = $qryVinc->fetchAll(PDO::FETCH_ASSOC);
$header = array();
$header[0]['campo'] = 'nome';
$header[0]['label'] = 'Nome / Razão Social';
$header[1]['campo'] = 'classificacao';
$header[1]['label'] = 'Vínculo';
//new dBug($data);
$twig->addGlobal('servico', $SERVICO);
$twig->addGlobal('rastro', $rastro);
echo $twig->render('paginator.html.twig'/*'index.html.twig'*/, array('data' => $data, 'header' => $header, 'req_id' => $idPessoa));
