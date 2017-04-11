<?php
class Auxiliar_RobotController extends App_Controller_Action
{
	private $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.76 Safari/537.36';
	private $cookifile = '';
	
 	public function init()
    {
        parent::init();
        $this->_helper->layout()->setLayout('novo_hash');
    }

    public function postDispatch()
    {
        $this->view->params = $this->getAllParams();
        $this->renderScript('twig.phtml');
        return parent::postDispatch();
    }
    
    public function request($url, array $post = array())
    {
    	$ch = curl_init();
    
    	curl_setopt($ch,CURLOPT_URL, $url);
    
    	curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
    	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    
    	if(!empty($post)){
    		$fields = count($post);
    		$post = http_build_query($post);
    		curl_setopt($ch, CURLOPT_POST, count($fields));
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    	}
    
    	curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookifile);
    	curl_setopt($ch, CURLOPT_COOKIEFILE,$this->cookifile);
    
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    	$a = curl_exec ($ch);
    	curl_close($ch);
    	return $a;
    }
    
    public function formAction()    
    {
    	$header = array();
    	$header[] = array('campo' => 'nome', 'label' => 'Nome');

    	$objGrupo = new Config_Model_Dao_Grupo();
    	$arrGrupos = $objGrupo->fetchAll('id_representacao is not null'); 
    	
    	$rsGroups = array();
    	$i = 0; 
    	foreach ($arrGrupos as $k => $v){
    		$rsGroups[$i]['id'] = $v->id;
    		$rsGroups[$i]['valor'] = $v->nome;
    		$i++;
    	}    	

    	$arrCampos = array();
    	$arrCampos['dados'][0] = array(
    			'id' 		=> 'numero',
    			'ordem' 	=> '0',
    			'obrigatorio' 	=> 'true',
    			'nome' 		=> 'Número do CNPJ',
    			'metanome'      => 'cnpj',
    			'tipo'		=> 'text',
    			'mascara' => '99.999.999/9999-99',
    			'metadatas'	=> array(    					
    					'ws_ordemLista'		=> '0',
    					'ws_style'		=> 'col-md-3',
    			)
    	);
    	$arrCampos['dados'][1] = array(
    			'id'            => 'data',
    			'ordem'         => '1',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Data Inicial',
    			'tipo'          => 'date',
    			'metanome'      => 'inicio',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-3'
    			)
    	);
    	$arrCampos['dados'][2] = array(
    			'id'            => 'data',
    			'ordem'         => '2',
    			'obrigatorio'   => 'true',
    			'nome'          => 'Data Final',
    			'tipo'          => 'date',
    			'metanome'      => 'fim',
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-3'
    			)
    	);
    	$arrCampos['dados'][3] = array(
    			'id'            => 'id',
    			'ordem'         => '3',
    			'obrigatorio'   => 'true',
    			'multiplo'      => false,
    			'nome'          => 'Grupo a ser vinculado',
    			'metanome'      => 'grupo',
    			'tipo'          => 'ref_itemBiblioteca',
    			'items'         => $rsGroups,
    			'metadatas'     => array(
    					'ws_ordemLista'         => '1',
    					'ws_style'              => 'col-md-3',
    					'ws_style_object'   => 'select2-skin'
    			)
    	);
    	$arrPerfis = array();
    	$arrPerfis[] = 'dados';
    	$this->view->file = 'form.html.twig';
    	$this->view->data = array('perfis' => $arrPerfis,'campos' => $arrCampos);
    } 
   
    public function varrerAction() {
    	if ( empty($_POST['id_grupo'] ) || empty( $_POST['numero_cnpj'] ) || empty($_POST['data_inicio']) || empty($_POST['data_fim']) ){
    		die('Todos os campos devem ser preenchidos.');
    	}
    	
    	$grupo = $_POST['id_grupo'];
    	$cnpj = $_POST['numero_cnpj'];
    	$cnpj = str_replace('-','',$cnpj);
    	$cnpj = str_replace('.','',$cnpj);
    	$cnpj = str_replace('/','',$cnpj);
    	$arrDtInicio = explode('/',$_POST['data_inicio']);
    	$arrDtFim = explode('/',$_POST['data_fim']);

    	$identity = Zend_Auth::getInstance()->getIdentity();
			
//		$curl = new Auxiliar_CurlController();
//    	$DetalhamentoDiarioDasDespesas = new DetalhamentoDiarioDasDespesas();
		$tib = new Config_Model_Dao_Tib();
		$ib = new Config_Model_Dao_ItemBiblioteca();
		$rlGrupoItem = new Config_Model_Dao_RlGrupoItem();
		
		$coTibMaster = $tib->fetchRow("metanome = 'ing_detalhamento_diario_das_despesas'")->id;
		
    	set_time_limit('36000');
    	
    	//$cnpj		=	'13380016000119';
    	$fase		=	'PAG';
    	$codigoOS	=	'TOD';
    	$dia_inicio = $arrDtInicio[0];
    	$dia_fim	= $arrDtFim[0];
    	$mes = $arrDtInicio[1];
    	$ano = $arrDtInicio[2];
    	$i	= 0;
    	$indice	=	array();

    	while ( $ano <= (int) $arrDtFim[2]){
    		if ($mes <= (int) $arrDtFim[1] ){
    			$controleDeData = checkdate($mes, $dia_fim, $ano);
    			while ($controleDeData == false){
    				$dia_fim--;
    				$controleDeData = checkdate($mes, $dia_fim, $ano);
    			}
    			sleep(10);
    			$a	=	$this->request('http://www.portaltransparencia.gov.br/despesasdiarias/resultado?consulta=rapida&periodoInicio='.$dia_inicio.'%2F'.$mes.'%2F'.$ano.'&periodoFim='.$dia_fim.'%2F'.$mes.'%2F'.$ano.'&fase='.$fase.'&codigoOS='.$codigoOS.'&codigoFavorecido='.$cnpj);
    			
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
//    	echo $inicio.' - '. microtime(TRUE);    	
    	$importados = 0;
    	foreach ( $indice as $despesa){
    		try {
	    		$dados = array();
	    		
				$dados['url_detalhamento'] = $despesa;
	    		sleep(10);
	    		
	    		$d	=	$this->request($despesa);
	
	    		preg_match_all('%class=\"impar\">.*?class=\"destaque\">(.*?)<\/span>%is', $d, $f);
	    		$f	= trim($f[1][0]);
	    		$dados['fase'] = utf8_encode($f);
	    		
	    		preg_match_all('%class=\"par\">.*?class=\"destaque\">(.*?)<\/span>%is', $d, $doc);
	    		$doc = trim($doc[1][0]);
	    		$dados['documento'] = utf8_encode($doc);
	    		
	    		preg_match_all('%class=\"par\">.*?class=\"rotulo\">.*class=\"destaque\">.*?class=\"rotulo\">.*?Documento:.*?<td>(.*?)<\/td>%is', $d, $tpdoc);
	    		$tpdoc = trim($tpdoc[1][0]);
	    		$dados['tipo_documento'] = $tpdoc;
	    		
	    		preg_match_all('%class=\"rotulo\">Data:<\/td>.*?>(.*?)<\/td>%is', $d, $dt);
	    		$dados['data'] = trim($dt[1][0]);
	    		
	    		preg_match_all('%class=\"rotulo\">Tipo de OB:.*?<td>(.*?)<\/td>%is', $d, $tpob);
	    		if (isset($tpob[1][0])){
	    			$tpob = trim($tpob[1][0]);
	    			$dados['tipo_ob'] = utf8_encode($tpob);
	    		}
	    		preg_match_all('%class=\"rotulo\">.*?Superior:.*?\"3\">(.*?)<\/td>%is', $d, $or_sup);
	    		$or_sup = trim($or_sup[1][0]);
	    		$dados['orgao_superior'] = utf8_encode($or_sup);
	    		
	    		preg_match_all('%class=\"rotulo\">.*?Vinculada:.*?\"3\">(.*?)<\/td>%is', $d, $vinc);
	    		$vinc = trim($vinc[1][0]);
	    		$dados['orgao_ou_entidade_vinculada'] = utf8_encode($vinc);
	    		
	    		preg_match_all('%class=\"rotulo\">.*?Emitente:.*?\"3\">(.*?)<\/td>%is', $d, $ugest);
	    		$ugest = trim($ugest[1][0]);
	    		$dados['unidade_gestora'] = utf8_encode($ugest);
	    		
	    		preg_match_all('%class=\"rotulo\">Gest.*?o:<\/td>.*?>(.*?)<\/td>%is', $d, $gest);
	    		$gest = trim($gest[1][0]);
	    		$dados['gestao'] = utf8_encode($gest);
	    		
	    		preg_match_all('%class=\"rotulo\">.*?Valor:.*?\"3\">(.*?)<\/td>%is', $d, $val);
	    		$val = trim($val[1][0]);
	    		$dados['valor'] = $val;
	    		
	    		preg_match_all('%class=\"rotulo\">Observa.*?o do Documento:<\/td>.*?\"3\">(.*?)<\/td>%is', $d, $obs_doc);
	    		$obs_doc = trim($obs_doc[1][0]);
	    		$dados['obs_documento'] = $obs_doc;
	    		
	    		preg_match_all('%class=\"rotulo\">.*?avorecido:.*?\"3\">(.*?)- %is', $d, $fave);
	    		$dados['favorecido'] = trim($fave[1][0]);    		
	    		
	    		preg_match_all('%class=\"rotulo\">.*?sp.*?cie de Empenho:.*?\"3\">(.*?)<\/td>%is', $d, $espec);
	    		if (isset($espec[1][0])){
	    			$espec = trim($espec[1][0]);
	    			$dados['especie'] = utf8_encode($espec);
	    		}
	    		
	    		preg_match_all('%class=\"rotulo\">.*?ipo de Empenho:.*?\"3\">(.*?)<\/td>%is', $d, $tpemp);
	    		if (isset($tpemp[1][0])){
	    			$tpemp = trim($tpemp[1][0]);
	    			$dados['tipo_empenho'] = utf8_encode($tpemp);
	    		}
				
	    		$dados['possiveisnumeroscontrato'] = $this->buscarPossiveisNumerosDeContrato($dados['obs_documento']);
	    		$dados['possiveisnumerosnfe'] = $this->buscarPossiveisNumerosDeNotasFiscais($dados['obs_documento']);
	    		
	    		if(is_null($ib->fetchRow("valor = '".$dados['documento']."' and id_tib = '".$idTib = $tib->fetchRow("id_tib_pai = '$coTibMaster' and metanome = 'documento'")->id."'"))){
	    			$idMasterIb = UUID::v4();
	    			
	    			$arrDados = array();
	    			$arrDados['id'] = $idMasterIb;
	    			$arrDados['dtype'] = 'TbItemBiblioteca';
	    			$arrDados['dt_criacao'] = date("Y-m-d h:i:s.B");
	    			$arrDados['id_criador'] = $identity->id;
	    			$arrDados['id_tib'] = $coTibMaster;
	    			$ib->insert($arrDados);
	    			
	    			foreach ($dados as $k => $v){
	    				$idTib = $tib->fetchRow("id_tib_pai = '$coTibMaster' and metanome = '$k'")->id;
	    				 
	    				$arrDados = array();
	    				$arrDados['id'] = UUID::v4();
	    				$arrDados['dtype'] = 'TbItemBiblioteca';
	    				$arrDados['dt_criacao'] = date("Y-m-d h:i:s.B");
	    				$arrDados['id_criador'] = $identity->id;
	    				$arrDados['valor'] = $v;
	    				$arrDados['id_ib_pai'] = $idMasterIb;
	    				$arrDados['id_tib'] = $idTib;
	    				$ib->insert($arrDados);
	    			}
	    			
	    			$arrDadosRl = array();
	    			$arrDadosRl['id'] = UUID::v4();
	    			$arrDadosRl['id_grupo'] = $grupo;
	    			$arrDadosRl['id_item'] = $idMasterIb;
	    			$rlGrupoItem->insert($arrDadosRl);
	    			
	    			$importados++;
	    		}
    		} 	catch(Zend_Exception $ex)	{
    			var_dump($dados);
    			echo '<Br>-----------<Br>';
    			var_dump($ex->getMessage());
    			exit;
    		}
    	}
    	die('Foram importados '.$importados.' registros.' );
    }
    /*
    public function testeAction() {
    	$ib = new Config_Model_Dao_ItemBiblioteca();
   		
    	$arrPrefix = array('NF','DANFE','FATURA','NOTA FISCAL','NOTAS FISCAIS','NOTA FISCAL ELETRONICA','NF-E','NR','nfs. nr.');
    	$arrOK = array();
    	
    	foreach ($ib->fetchAll("id_tib = '6a6b3655-5637-4e49-b745-6f3451745941'") as $k => $v ){
    		$srtInicial = $v->valor;
    		$srtInicial = preg_replace('%[0-9]{2}\/[0-9]{2}\/[0-9]{2,4}%', '', $srtInicial);
    		preg_match_all('%(.*?)R\$.*?[a-zA-Z]{1,}(.*?)\z%', $srtInicial, $fatia1);    			
    		if(isset($fatia1[1][0])){
    			$srtInicial = $fatia1[1][0].$fatia1[2][0];    			
    		}

    		foreach ( $arrPrefix as $prefixo ){    			
    			preg_match_all('%'.$prefixo.'(.*?)[a-zA-Z]{2,}%', $srtInicial, $fatia);
    			if (isset($fatia[1][0])){
    				$str = $fatia[1][0];
    			}    			
    		}
    		if ( !isset($str)){
	    		foreach ( $arrPrefix as $prefixo ){
	    			preg_match_all('%'.$prefixo.'(.*?)\z%', $srtInicial, $fatia);
	    			if (isset($fatia[1][0])){
	    				$str = $fatia[1][0];
	    			}    			
	    		}
    		}
    		
    		if ( !isset($str)){
    			$str = $srtInicial;
    		}
    		
    		preg_match_all('%([0-9]{2,})%', $str, $tpemp);
    		$notas = '';
    		if (isset($tpemp[1][0])){
    			if (count($tpemp[1])){
    				foreach ($tpemp[1] as $k_tpempS => $tpempS){
    					if ( (int)$tpempS != 0){
   							$notas .= $tpempS . '|';
    					}
    				}
    			}
    		}
    		$notas = substr($notas,0,-1);
    		$arrOK[$k] = $notas . '=STR>' . $str . '=STR_ORIGINAL>  '. $srtInicial;
    		$notas = '';
    		unset($str);
    		unset($srtInicial);
    	}x($arrOK);	
    }
    */
    public function buscarPossiveisNumerosDeNotasFiscais( $valor ){
    	$srtInicial = $valor;
    	$arrPrefix = array('NF','DANFE','FATURA','NOTA FISCAL','NOTAS FISCAIS','NOTA FISCAL ELETRONICA','NF-E','NR','nfs. nr.');
    	$srtInicial = preg_replace('%[0-9]{2}\/[0-9]{2}\/[0-9]{2,4}%', '', $srtInicial);
    	
    		preg_match_all('%(.*?)R\$.*?[a-zA-Z]{1,}(.*?)\z%', $srtInicial, $fatia1);    			
    		if(isset($fatia1[1][0])){
    			$srtInicial = $fatia1[1][0].$fatia1[2][0];    			
    		}

    		preg_match_all('%[^\/]([0-9]{2,}\/20..)%', $valor, $tpemp);
    		if (isset($tpemp[1][0])){
   				foreach ($tpemp[1] as $k_tpempS => $tpempS){
    				$srtInicial = str_replace($tpempS, '', $srtInicial);    							
    			}
    		}
    		
    		foreach ( $arrPrefix as $prefixo ){    			
    			preg_match_all('%'.$prefixo.'(.*?)[a-zA-Z]{2,}%', $srtInicial, $fatia);
    			if (isset($fatia[1][0])){
    				$str = $fatia[1][0];
    			}    			
    		}
    		if ( !isset($str)){
	    		foreach ( $arrPrefix as $prefixo ){
	    			preg_match_all('%'.$prefixo.'(.*?)\z%', $srtInicial, $fatia);
	    			if (isset($fatia[1][0])){
	    				$str = $fatia[1][0];
	    			}    			
	    		}
    		}
    		
    		if ( !isset($str)){
    			$str = $srtInicial;
    		}
    		
    		preg_match_all('%([0-9]{2,})%', $str, $tpemp);
    		$notas = '';
    		if (isset($tpemp[1][0])){
    			if (count($tpemp[1])){
    				foreach ($tpemp[1] as $k_tpempS => $tpempS){
    					if ( (int)$tpempS != 0){
   							$notas .= $tpempS . '|';
    					}
    				}
    			}
    		}
    		$notas = substr($notas,0,-1);
    	
    	return $notas;
    }
    
    public function buscarPossiveisNumerosDeContrato( $valor ){
    	$strPossiveisContratos = '';
    	preg_match_all('%[^\/]([0-9]{2,}\/20..)%', $valor, $tpemp);
    	if (isset($tpemp[1][0])){
    		$quanto = count($tpemp[1]);
    		if ($quanto > 1){
    			$quanto--;
    			foreach ($tpemp[1] as $k_tpempS => $tpempS){
    				$strPossiveisContratos .= $tpempS;
    				if( $k_tpempS < $quanto){
    					$strPossiveisContratos .= '|';
    				}
    			}
    		} else {
    			$strPossiveisContratos = $tpemp[1][0];
    		}
    	} else {
    		$strPossiveisContratos = null;
    	}
    	return $strPossiveisContratos;
    }
}