<?php
$inicio	=	microtime(TRUE);

set_time_limit('36000');
$this->dbh = DatabaseConnection::getInstance()->getConnection();

$cnpj		=	'13380016000119';
$fase		=	'PAG';
$codigoOS	=	'TOD';
$dia_inicio = '01';
$dia_fim	= 31;
$mes = '01';
$ano = 2012;
$i	= 0; 	
$indice	=	array();

while ( $ano < 2016){
		if ($mes < 12 ){
			$controleDeData = checkdate($mes, $dia_fim, $ano);
			while ($controleDeData == false){
				$dia_fim--;
				$controleDeData = checkdate($mes, $dia_fim, $ano);
			}
			sleep(5);
			$a	=	curlRequest('http://www.portaltransparencia.gov.br/despesasdiarias/resultado?consulta=rapida&periodoInicio='.$dia_inicio.'%2F'.$mes.'%2F'.$ano.'&periodoFim='.$dia_fim.'%2F'.$mes.'%2F'.$ano.'&fase='.$fase.'&codigoOS='.$codigoOS.'&codigoFavorecido='.$cnpj);
			if( preg_match_all('/<a href=\"pagamento\?documento=(.*)\">/', $a, $links) ){
				unset($links[0]);
				foreach ( $links[1] as $iLink => $link){
					$indice[$i++]	=	'http://www.portaltransparencia.gov.br/despesasdiarias/pagamento?documento='.$link;
				}
			}
			$dia_fim	= 31;
			$mes++;
			if ( $mes < 10 ) {
				$mes = str_pad($mes, 2, "0", STR_PAD_LEFT);
			}
		} else {
			$mes = '01';
			$ano++;
		}

}
echo $inicio.' - '. microtime(TRUE);;

x($indice);

foreach ( $indice as $despesa){
	
	sleep(5);
	$d	=	curlRequest($despesa);
	preg_match_all('%class=\"impar\">.*?class=\"destaque\">(.*?)<\/span>%is', $d, $f);
	$fase = utf8_encode($f[1][0]);
	
	preg_match_all('%class=\"par\">.*?class=\"destaque\">(.*?)<\/span>%is', $d, $doc);
	$documento = utf8_encode($doc[1][0]);
	
	preg_match_all('%class=\"par\">.*?class=\"rotulo\">.*class=\"destaque\">.*?class=\"rotulo\">.*?Documento:.*?<td>(.*?)<\/td>%is', $d, $tpdoc);
	$tipo_documento = utf8_encode($tpdoc[1][0]);
	
	preg_match_all('%class=\"impar\">.*?class=\"rotulo\">.*?Data:.*?<td>(.*?)<\/td>%is', $d, $dt);
	$data = $dt[1][0];
	
	preg_match_all('%class=\"rotulo\">Tipo de OB:.*?<td>(.*?)<\/td>%is', $d, $tpob);
	if (isset($tpob[1][0])){
		$tipo_ob = utf8_encode($tpob[1][0]);
	}
	preg_match_all('%class=\"rotulo\">.*?Superior:.*?\"3\">(.*?)<\/td>%is', $d, $or_sup);
	$orgao_superior = utf8_encode($or_sup[1][0]);
	
	preg_match_all('%class=\"rotulo\">.*?Vinculada:.*?\"3\">(.*?)<\/td>%is', $d, $vinc);
	$orgao_ou_entidade_vinculada = utf8_encode($vinc[1][0]);
	
	preg_match_all('%class=\"rotulo\">.*?Emitente:.*?\"3\">(.*?)<\/td>%is', $d, $ugest);
	$unidade_gestora = utf8_encode($ugest[1][0]);
	
	preg_match_all('%class=\"rotulo\">.*?Gest.o:.*?\"3\">(.*?)<\/td>%is', $d, $gest);
	$gestao = utf8_encode($gest[1][0]);
	
	preg_match_all('%class=\"rotulo\">.*?Valor:.*?\"3\">(.*?)<\/td>%is', $d, $val);
	$valor = $val[1][0];
	
	preg_match_all('%class=\"rotulo\">.*?Observa..o do Documento:.*?\"3\">(.*?)<\/td>%is', $d, $obs_doc);
	$obs_documento = utf8_encode($obs_doc[1][0]);
	
	preg_match_all('%class=\"rotulo\">.*?Esp.cie de Empenho:.*?\"3\">(.*?)<\/td>%is', $d, $espec);
	if (isset($espec[1][0])){
		$especie = utf8_encode($espec[1][0]);
	}
	
	preg_match_all('%class=\"rotulo\">.*?Tipo de Empenho:.*?\"3\">(.*?)<\/td>%is', $d, $tpemp);
	if (isset($tpemp[1][0])){
		$tipo_empenho = utf8_encode($tpemp[1][0]);
	}
	
}