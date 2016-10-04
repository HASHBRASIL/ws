<?php

class Content_RoboController extends App_Controller_Action_Abstract
{
    /**
     * @var Content_Model_Bo_Ib
     */
    protected $_bo;

    protected $tibsCandidato;
    protected $tibsCracha;

    const MSG_REMETENTE  = "REMETENTE";
    const MSG_CONVIDADA  = "CONVIDADA";
    const MSG_CANDIDATO  = "CRACHA";

    const CARGO_PREFEITO = "PREFEITO";
    const CARGO_VICEPREFEITO = "VICE-PREFEITO";

    public function init()
    {
        parent::init();
        $this->_bo = new Content_Model_Bo_ItemBiblioteca();
        $this->_translate = Zend_Registry::get('Zend_Translate');
    }

    private function download($url, $destination)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSLVERSION,3);

        $data = curl_exec ($ch);
        $error = curl_error($ch);

        curl_close ($ch);

        $file = fopen($destination, "w+");
        fputs($file, $data);
        fclose($file);
    }

    private function rrmdir($dir) {
      if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
          if ($object != "." && $object != "..") {
            if (is_dir($dir."/".$object))
              $this->rrmdir($dir."/".$object);
            else
              unlink($dir."/".$object);
          }
        }
        rmdir($dir);
      }
    }

public function importaCandidatosMunicipaisAction()
    {
        set_time_limit(0);

        $idTime = '15c94e3d-4ef5-4062-ad09-e9548bf22655'; // idTitaniumtech
        $id_grupo = '363a27b3-95e2-4e46-ea44-f4be5070dcb6'; //candidatosMunicipais
        $id_tib  = '624a424d-704b-4874-b26f-396146413048';
        $id_pessoa = '5dc2b0c6-0bce-4c5d-bdc0-eae0c73c9ac5';
        $filedir    = Zend_Registry::getInstance()->get('config')->get('filedir');
        $zipfile = $filedir->path.'consulta_cand_2016.zip';
        $fotoDir = $filedir->path.$idTime.'/'.$id_grupo.'/';
        //$fotoUrl = 'http://divulgacandcontas.tse.jus.br/divulga/rest/v1/candidatura/buscar/foto/';
        $fotoUrl = 'http://divulgacandcontas.tse.jus.br/divulga/rest/v1/candidatura/buscar/foto/2/';


        $idsTib = array(
                    'partidoSigla' => '44443979-6e56-4353-da67-646858687242',
                    'cargo' => '45385778-3165-4344-d442-6e785a4c7253',
                    'partido' => '446a684e-634e-4677-d662-353754656d46',
                    'partidoCod' => '5247635a-6539-4755-cf66-6d52364c7851',
                    'partidoNumero' => '4a383472-4a47-4148-f055-726a56425951',
                    'uf' => '4e313674-5538-4c4c-e857-624f6f316979',
                    'cidade' => '57743053-3859-4e70-d752-5965384b3048',
                    'cargoCod' => '63347033-626a-444a-f049-6d61365a6a54',
                    'situacao' => '33456455-4478-4a59-c546-4b5136613572',
                    'coligacaoNome' => '654e4f76-7957-4975-c455-72355242766c',
                    'nome' => '65384463-4f41-4635-d36b-48794e6e546c',
                    'ufCod' => '506d644a-3969-4362-ed67-67413569697a',
                    'id' => '4f4b6336-3657-4e65-f650-47675844446e',
                    'numero' => '70417642-3948-4861-e848-563464373647',
                    'cpf' => '524f5067-545a-4346-d050-365458656148',
                    'situacaoCod' => '4844756a-4331-4a6e-b364-485048615064',
                    'coligacaoPartidos' => '5959797a-7658-486d-c14d-444275647a46',
                    'nomeGuerra' => '66355269-5230-4f6e-b156-335134774d35',
                    'email' => '696c4275-495a-4948-d764-77756e4b7a76',
                    'imagem' => '446c4b44-4265-4333-b477-544377785835');

        $chaves = array(
            '5' => 'uf',
            '6' => 'ufCod',
            '7' => 'cidade',
            '8' => 'cargoCod',
            '9' => 'cargo',
            '10' => 'nome',
            '11' => 'id',
            '12' => 'numero',
            '13' => 'cpf',
            '14' => 'nomeGuerra',
            '15' => 'situacaoCod',
            '16' => 'situacao',
            '17' => 'partidoNumero',
            '18' => 'partidoSigla',
            '19' => 'partido',
            '20' => 'partidoCod',
            '22' => 'coligacaoPartidos',
            '23' => 'coligacaoNome',
            '45' => 'email'
        );
        $modelIb = new Content_Model_Bo_ItemBiblioteca();
        $dir = $filedir->path.'unziped/';
        if(!is_dir($fotoDir)){
            if(!is_dir($filedir->path.$idTime)){
                mkdir($filedir->path.$idTime, 0777);
            }
            mkdir($fotoDir, 0777);
        }
        if(!is_dir($dir)){ mkdir($dir, 0777); }

        $zip = new ZipArchive;

        if ($zip->open($zipfile) === TRUE) {
            $zip->extractTo($dir);
            $zip->close();

            $files = scandir($dir);

            //$registros = array_column($modelIb->getAllIbByTib('4f4b6336-3657-4e65-f650-47675844446e')->toArray(), 'valor');

            foreach($files as $file)
            {
                if(substr($file, -3) === 'txt'){
                    $conteudo = explode("\n", file_get_contents($dir.$file));

                    foreach($conteudo as $candidato)
                    {
                        $candidato = explode(';', $candidato);
                        $convertido = array();

                        foreach($chaves as $chave => $nome) { $convertido[$nome] = utf8_encode(preg_replace('/^"|"$/', '', $candidato[$chave]));}

                        //if(!in_array($convertido['id'], $registros)){
                            $convertido['imagem'] = $idTime.'/'.$id_grupo.'/'.$convertido['id'].'.jpg';

                            foreach($convertido as $chave => $nome) { $dadosInsert[$idsTib[$chave]] = $nome;}

                            $this->download($fotoUrl.$convertido['id'], $fotoDir.$convertido['id'].'.jpg');

                            $id_item = $modelIb->insere($id_tib, $id_pessoa, $dadosInsert);

                            if(empty($id_item)){ echo 'Erro ao relacionar '.print_r(error_get_last(), true); exit; }
                            $rel = $modelIb->addRelGrupoItem($id_grupo, $id_item);
                            if(!$rel){ echo 'Erro ao relacionar '.print_r(error_get_last(), true); exit; }
                        //}else{
                            if(!is_file($fotoDir.$convertido['id'].'.jpg')){
                                $this->download($fotoUrl.$convertido['id'], $fotoDir.$convertido['id'].'.jpg');
                            }else if(filesize($fotoDir.$convertido['id'].'.jpg') < 200){
                                $this->download($fotoUrl.$convertido['id'], $fotoDir.$convertido['id'].'.jpg');
                            }
                        //}
                    }
                }
            }

            $this->rrmdir($dir);
        } else {
            echo 'failed';
        }

        exit;
    }

    public function reexecutaProcessoAction()
    {
        $get = $this->getRequest()->getQuery();

        if(isset($get['nome'])){
            $nome       = preg_replace('[^0-9a-zA-Z_-]', '', $get['nome']);
            $filedir    = Zend_Registry::getInstance()->get('config')->get('filedir');
            $filename   = $filedir->path.'/log/processo'.$nome.'.txt';
            $content    = json_decode(file_get_contents($filename), true);
            $content['status'] = 'inicio';
            $register = fopen($filename, 'w+');
            fwrite($register, json_encode($content));
            fclose($register);

            call_user_func_array(array($this, 'addProcesso'), array($nome));
        }
        return true;
    }

    function shutDownProcesso($filename)
    {
        $last_error = error_get_last();
        $filedir  = Zend_Registry::getInstance()->get('config')->get('filedir');
        $dir      = $filedir->path.'/log/';

        $lastLog = fopen($dir.'LogProcessoFuncionamento.txt', 'a+');

        if(!empty($last_error) && ($last_error['type'] === E_ERROR || $last_error['type'] === E_USER_ERROR)){

            $data = json_decode(file_get_contents($filename), true);

            $chamadaFalha =  array_shift($data['chamadas']);

            $chamadaFalha['obs'] = 'Falhou : '.$last_error['message'];
            $data['resultados'][date('Y-m-d H:i:s')][] = $chamadaFalha;

            $register = fopen($filename, 'w+');
            fwrite($register, json_encode($data));
            fclose($register);

            fwrite($lastLog, date('Y-m-d H:i:s').' Erro Fatal : ('.$chamadaFalha['id'].') '.json_encode($last_error)." \n\r");
            fclose($lastLog);


            $getParams  = $this->getRequest()->getQuery();
            $getParams['direto'] = 0;
            $this->getRequest()->setQuery($getParams);
            call_user_func_array(array($this, 'addProcesso'), array());


        }else{
            fwrite($lastLog, date('Y-m-d H:i:s').' Término de execução : ('.$filename.')'." \n\r");
            fclose($lastLog);
        }
    }

    private function addProcesso($nomeArquivo = '_padrao')
    {
        $getParams  = $this->getRequest()->getQuery();
        $postParams = $this->getRequest()->getPost();
        $allParams  = $this->getRequest()->getParams();

        $filedir  = Zend_Registry::getInstance()->get('config')->get('filedir');
        $dir      = $filedir->path.'/log/';
        $filename = $dir.'processo'.$nomeArquivo.'.txt';

        $ActionName = str_replace(' ', '', ucwords(str_replace('-', ' ', $this->getRequest()->getActionName()))).'Action';
        $ActionName[0] = strtolower($ActionName[0]);

        if(!is_dir($filedir->path)){ mkdir($filedir->path, 0777); }
        if(!is_dir($dir)){ mkdir($dir, 0777); }

        if(isset($getParams['direto']) && $getParams['direto'] == 1){ return true; }

        register_shutdown_function(array($this, 'shutDownProcesso'), $filename);

        $register   = fopen($filename, 'a+');

        if($register === false){ echo 'Processo não pode ser criado'; exit; }

        fclose($register);
        $content    = file_get_contents($filename);

        if(empty($content)){
            $data = array( 'inicio' => date('Y-m-d H:i:s'), 'chamadas' => array(), 'resultados' => array(), 'status' => 'inicio');
        }else{
            $data = json_decode($content, true);
        }

        if($data['status'] == 'inicio'){

            $time               = 60;
            $novoId             = UUID::v4();
            $data['inicio']     = date('Y-m-d H:i:s');
            $data['status']     = 'rodando';
            $data['chamadas'][] = array(
                'id'         => $novoId,
                'action'     => $ActionName,
                'getParams'  => $getParams,
                'postParams' => $postParams
            );

            $register = fopen($filename, 'w+');
            fwrite($register, json_encode($data));
            fclose($register);

            while($chamada = array_shift($data['chamadas']))
            {
                set_time_limit($time * (count($data['chamadas']) + 1));

                $novoGet            = $chamada['getParams'];
                $novoGet['direto']  = 1;
                $this->getRequest()->setQuery($novoGet);
                $this->getRequest()->setPost($chamada['postParams']);

                if($chamada['action'] != 'reexecutaProcessoAction'){
                    $chamada['resultado'] = call_user_func_array(array($this, $chamada['action']), array());
                }

                $data['resultados'][date('Y-m-d H:i:s')][] = $chamada;

                $dataAtual = json_decode(file_get_contents($filename), true);

                foreach($dataAtual['chamadas'] as $pos => $value)
                {
                    if($dataAtual['chamadas'][$pos]['id'] == $chamada['id']){
                        break;
                    }
                }

                unset($dataAtual['chamadas'][ $pos ]);

                $data['chamadas'] = $dataAtual['chamadas'];

                $register = fopen($filename, 'w+');
                fwrite($register, json_encode($data));
                fclose($register);
            }

            $data['status'] = 'inicio';
            $hoje = date_create(date('Y-m-d 00:00:00'));
            $bkps = array();

            foreach($data['resultados'] as $chave => $resultado)
            {
                $date = date_create($chave);
                if($hoje->getTimestamp() > $date->getTimestamp()){
                    $resultado['data'] = $chave;
                    $bkps[substr($chave, 0, 10)][] = $resultado;
                    unset($data['resultados'][$chave]);
                }
            }

            foreach($bkps as $dia => $registrosDia)
            {
                $register = fopen($dir.$dia.'_Fila_'.$nomeArquivo.'.txt', 'a+');
                fwrite($register, json_encode($registrosDia));
                fclose($register);
            }

            $register = fopen($filename, 'w+');
            fwrite($register, json_encode($data));
            fclose($register);

        }else if($data['status'] === 'rodando'){

            $novoId = UUID::v4();
            $inserirNaFila = true;

            foreach($data['chamadas'] as $chamada)
            {
                if($chamada['action'] == $ActionName)
                {
                    $get = is_array($getParams) ? $getParams : array();

                    if(empty(array_diff($chamada['getParams'],$get)))
                    {
                        $post = is_array($postParams) ? $postParams : array();

                        if(empty(array_diff($postParams, $post)))
                        {
                            $inserirNaFila = false;
                        }
                    }
                }
            }

            if($inserirNaFila){
                $data['chamadas'][] = array(
                    'id'        => $novoId,
                    'action'    => $ActionName,
                    'getParams'  => $getParams,
                    'postParams' => $postParams
                );

                $register = fopen($filename, 'w+');
                fwrite($register, json_encode($data));
                fclose($register);
            }
        }
        exit;
    }

    public function importapessoaAction()
    {
        $this->addProcesso();

        ini_set("auto_detect_line_endings", true);
        set_time_limit(0);

        $tibBo = new Config_Model_Bo_Tib();
        $svcBo = new Config_Model_Bo_Servico();
        $tinfBo = new Config_Model_Bo_TipoInformacao();
        $rlPI = new Config_Model_Bo_RlPerfilInformacao();
        $infBo = new Config_Model_Bo_Informacao();
        $rgiBo = new Config_Model_Bo_RlGrupoInformacao();
        $rvpBo = new Config_Model_Bo_RlVinculoPessoa();
        $clsBo = new Config_Model_Bo_Classificacao();
        $pesBo = new Legacy_Model_Bo_Pessoa();

        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

        $ib = $this->getRequest()->getParam('ib');
        $time = $this->getRequest()->getParam('time');
        $classificacao = explode(',',$this->getRequest()->getParam('cls'));

        $master = current($this->_bo->getById($ib)->toArray());

        $tib = $tibBo->getFilhosById($master['id_tib'])->toArray();

        $tibFile = null;
        $tibStatus = null;
        $tibOcr = null;
        foreach($tib as $item) {
            if($item['metanome']=='enclosure') {
                $tibFile = $item;
            } else if($item['metanome']=='status') {
                $tibStatus = $item;
            } else if($item['metanome']=='ocr') {
                $tibOcr = $item;
            }
        }

        $ret = $this->_bo->getItemBibliotecaById($ib)->toArray();

        $arq = null;
        $ibstatus = null;
        $ibdesc = null;

        foreach($ret as $campo) {
            if($campo['id_tib']==$tibFile['id']) {
                $arq = $campo['valor'];
            } else if($campo['id_tib']==$tibStatus['id']) {
                $ibstatus = $campo['id'];
            } else if($campo['id_tib']==$tibOcr['id']) {
                $ibdesc = $campo['id'];
            }
        }

        $this->_bo->persiste($ibstatus,null,null,null,'EM PROCESSAMENTO');
        //$this->_bo->_dao->beginTransaction();
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();

        try {

            $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

            $caminho = $filedir->path . $arq;
            $row = 0;
            $tamanho = 0;
            $campos = array();
            $master = array();
            if (($handle = fopen($caminho, "r")) !== FALSE) {
                while (($data = fgetcsv($handle,0,';')) !== FALSE) {
                    if($row==0) {
                        $tamanho = count($data);
                        foreach($data as $linha=>$campo) {
                            if(!empty($campo)){
                                $campos[$linha] = current($tinfBo->getByMetanome(str_replace(array(1,2,3,),'',$campo)));
                                if($campos[$linha]==null) {
                                    $ori = $ret['original'];
                                    $msg = "Planilha $ori com não conformidades de campos. Campo $campo não encontrado. Favor revisar as colunas utilizadas.";
                                    Throw new Exception($msg);
                                }
                                $multiplo = $rlPI->getByInformacaoMultiplo($campos[$linha]['id']);
                                $campos[$linha]['multiplo'] = $multiplo != null;
                                $campos[$linha]['nomecampo'] = $campo;
                                if($campos[$linha]['id_pai']!= null) {
                                    $master[$campos[$linha]['id_pai']] = current($tinfBo->getById($campos[$linha]['id_pai']));
                                    $multiplo = $rlPI->getByInformacaoMultiplo($campos[$linha]['id_pai']);
                                    $master[$campos[$linha]['id_pai']]['multiplo'] = $multiplo != null;
                                }
                            }
                        }

                    } else {
                        if(count($data)==$tamanho) {
                            $multiplo = array();
                            $masterib = array();
                            $idPessoa = null;
                            $info = null;
                            foreach($data as $linha=>$valor) {
                                if(!empty($valor)){
                                    $valor = mb_convert_encoding($valor, 'UTF-8');
                                    echo $valor;
                                    if($linha==0) {
                                        $idPessoa = $pesBo->persiste(null,$valor,null);
                                        $info = $infBo->persiste(null,$campos[$linha]['id'],$idPessoa,null,$valor);
                                    } else {
                                        if($campos[$linha]['nomecampo']==$campos[$linha]['metanome']){
                                            if($campos[$linha]['id_pai']==null) {
                                                $info = $infBo->persiste(null,$campos[$linha]['id'],$idPessoa,null,$valor);
                                            } else {
                                                if(!isset($masterib[$campos[$linha]['id_pai']])) {
                                                    $masterib[$campos[$linha]['id_pai']] = $infBo->persiste(null,$campos[$linha]['id_pai'],$idPessoa,null,null);
                                                    $rgiBo->persiste(null,$time,$idPessoa,$masterib[$campos[$linha]['id_pai']]);
                                                }
                                                $info = $infBo->persiste(null,$campos[$linha]['id_pai'],$idPessoa,$masterib[$campos[$linha]['id_pai']],$valor);
                                            }
                                        } else {
                                            if($campos[$linha]['id_pai']==null) {
                                                $info = $infBo->persiste(null,$campos[$linha]['id'],$idPessoa,null,$valor);
                                            } else {
                                                if(!isset($masterib[$campos[$linha]['id_pai']][str_replace($campos[$linha]['metanome'],'',$campos[$linha]['nomecampo'])])) {
                                                    $masterib[$campos[$linha]['id_pai']][str_replace($campos[$linha]['metanome'],'',$campos[$linha]['nomecampo'])] = $infBo->persiste(null,$campos[$linha]['id_pai'],$idPessoa,null,null);
                                                    $rgiBo->persiste(null,$time,$idPessoa,$masterib[$campos[$linha]['id_pai']][str_replace($campos[$linha]['metanome'],'',$campos[$linha]['nomecampo'])]);
                                                }
                                                $info = $infBo->persiste(null,$campos[$linha]['id_pai'],$idPessoa,$masterib[$campos[$linha]['id_pai']][str_replace($campos[$linha]['metanome'],'',$campos[$linha]['nomecampo'])],$valor);
                                            }
                                        }
                                    }
                                }

                                $rgiBo->persiste(null,$time,$idPessoa,$info);
                            }
                            foreach($classificacao as $cls) {
                                $classe = current($clsBo->getByMetanome($cls)->toArray());
                                $rvpBo->persiste(null,$classe['id'],$idPessoa,null,$time,null,null);
                            }
                        } else {
                            $ori = $ret['original'];
                            $msg = "Planilha $ori com linhas de tamanho diferente. Importação interrompida. Favor revisar dados.";
                            Throw new Exception($msg);
                        }
                    }
                    // $num = count($data);
                    // echo "<p> $num fields in line $row: <br /></p>\n";
                    $row++;
                    // for ($c=0; $c < $num; $c++) {
                    //     echo $data[$c] . "<br />\n";
                    // }
                }
                fclose($handle);
            }

            $row--;
            //$this->_bo->_dao->rollBack();
            $this->_bo->persiste($ibstatus,$tibStatus['id'],null,$ib,'SUCESSO');
            $this->_bo->persiste($ibdesc,$tibOcr['id'],null,$ib,"Importação realizada com sucesso. $row pessoas importadas.");
            Zend_Db_Table::getDefaultAdapter()->commit();
            // $response = array(
            //     'success' => true,
            //     'msg' => $this->_translate->translate("Importação realizada com sucesso. $row pessoas importadas."),
            //     'data' => array('target' => array('servico' => $target))
            // );
            //$this->_helper->json($response);
        } catch (Exception $e) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->_bo->persiste($ibstatus,$tibStatus['id'],null,$ib,'ERRO');
            $this->_bo->persiste($ibdesc,$tibOcr['id'],null,$ib,$e->getMessage());
            // $response = array(
            //     'success' => false,
            //     'msg' => $this->_translate->translate(),
            //     'data' => array('target' => array('servico' => $target))
            // x($e->getMessage(),false);
            // x($e->getLine(),false);
            // x($e->getTrace());
            // );

            //$this->_helper->json($response);
        }
    }

    public function configuraWorkspaceAction()
    {
        $idPessoa = $this->getRequest()->getParam('codigo');
        $idTime = $this->getRequest()->getParam('time');
        $apenasInstala = false;
        $install = true;

        if (empty($idPessoa)) {
            $this->enviaMensagem([
                'assunto' => $this->_translate->translate('email_de_finalizacao_sem_pessoa'),
                'mensagem' => 'Requisição incompleta!<br />' . var_export($_SERVER, true)
            ]);
            exit();
        }

        if (!empty($idTime)) {
            $apenasInstala = true;
        }

        $emailPessoa = current((new Config_Model_Bo_Informacao)
            ->getInfoPessoaByMetanome(
                $idPessoa,
                Config_Model_Bo_TipoInformacao::META_EMAIL
            ))['valor'];

        $grupoBo = new Config_Model_Bo_Grupo();
        $metasBo = new Config_Model_Bo_GrupoMetadata();
        if (is_null($idTime)) {
            $idGrupoTime = $grupoBo->getTimeByCriador($idPessoa);
        } else {
            $idGrupoTime = current($grupoBo->getGrupoByRepresentacao($idTime))['id'];
        }
        $metas = $metasBo->listMeta($idGrupoTime)->toArray();
        foreach ($metas as $meta) {
            if ($meta['metanome'] == $metasBo::META_INSTALL) {
                $install = false;
            }
        }
        try {

            $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
            $opcoes = $config->getOption('filedir');
            $url = $opcoes['site'];

            $urlValidacao = $url . $this->view->url([
                'module' => 'config',
                'controller' => 'validacao',
                'action' => 'perfil',
                'codigo' => $idPessoa
            ]);

            // -- @todo Transformar em um help
            if ($install) {


                (new Legacy_Model_Bo_Pessoa())
                    ->salvaDeAcordoEInstalaModulos($idPessoa, $idTime, $apenasInstala);



        $destinatarios = [$emailPessoa];
        $assuntoEmail = 'email_de_finalizacao_instalacao';
        $conteudoEmail = <<<TEXT
<h1>Já terminamos de configurar o seu workspace!</h1>
Acesse agora e confira: {$urlValidacao}
TEXT;
            } else {
                $destinatarios = [$emailPessoa];
        $assuntoEmail = 'email_de_finalizacao_instalacao';
        $conteudoEmail = <<<TEXT
<h1>O seu workspace já esta configurado</h1>
Acesse agora e confira: {$urlValidacao}
TEXT;
            }
        } catch (Exception $ex) {
            $destinatarios = [];
            $assuntoEmail = 'email_de_finalizacao_exception';
            $conteudoEmail = <<<TEXT
Erro: {$ex->getMessage()}
<hr />
{$ex->getTraceAsString()}
TEXT;
        }

        $this->_helper->campanha(
            [
                'assunto' => $this->_translate->translate($assuntoEmail),
                'mensagem' => $conteudoEmail
            ],
            $destinatarios
        );

        exit();
    }

    public function processacampanhaAction() {

        //$this->addProcesso('ProcessaCampanha');
        $ib = $this->getRequest()->getParam('ib');

        $mdlIb = new Content_Model_Bo_ItemBiblioteca();
        $mdlTib = new Config_Model_Bo_Tib();
        $mdlPes = new Legacy_Model_Bo_Pessoa();
        $mdlGrp = new Config_Model_Bo_Grupo();
        $mdlInf = new Config_Model_Bo_Informacao();
        $mdlTinf = new Config_Model_Bo_TipoInformacao();

        $idtibmsg = current($mdlTib->getByMetanome('TPMSGCAMPANHA'))['id'];
        $idtibtpmsg = current($mdlTib->getByMetanome('TPTIPOMSG'))['id'];
        $pai = current($mdlIb->getById($ib)->toArray());
        $tpfilhos = $mdlTib->getFilhosById($pai['id_tib'])->toArray();
        $tibtpmsg = current($mdlTib->getByIdPaiByMetanome($idtibtpmsg,'nome'))['id'];

        $status = null;
        $ibstatus = null;
        $proc = null;
        $ibproc = null;
        $dtini = null;
        $ibdtini = null;
        $dtfim = null;
        $ibdtfim = null;
        $tpmsg = null;
        $msgsrv = null;
        $retirarTag = true;

        $idtibmsg   = current($mdlTib->getByMetanome('TPMSGCAMPANHA'))['id'];
        $idtibtpmsg = current($mdlTib->getByMetanome('TPTIPOMSG'))['id'];
        $pai        = current($mdlIb->getById($ib)->toArray());
        $tpfilhos   = $mdlTib->getFilhosById($pai['id_tib'])->toArray();
        $tibtpmsg   = current($mdlTib->getByIdPaiByMetanome($idtibtpmsg,'nome'))['id'];

        foreach($tpfilhos as $tp) {
            if($tp['metanome']=='status'){
                $status = $tp;
            } else if($tp['metanome']=='dtiniproc'){
                $dtini = $tp;
            } else if($tp['metanome']=='dtfimproc'){
                $dtfim = $tp;
            } else if($tp['metanome']=='preprocjson'){
                $proc = $tp;
            }
        }

        $campanha = $mdlIb->getItemBibliotecaById($ib)->toArray();

        foreach($campanha as $item) {
            if($item['id_tib']==$status['id']){
                $ibstatus = $item;
            } else if($item['id_tib']==$proc['id']){
                $ibproc = $item;
            } else if($item['id_tib']==$dtini['id']){
                $ibdtini = $item;
            } else if($item['id_tib']==$dtfim['id']){
                $ibdtfim = $item;
            }
        }

        $jsonProc = json_decode($ibproc['valor']);

        $time = $mdlGrp->getTimeByGrupo($jsonProc[0]->idgrupo);

        $grpmsg = current($mdlGrp->getGruposByIDPaiByMetanome($time,'MKTSOC'))['id'];

        $mdlIb->persiste($ibstatus['id'],null,null,null,'PREPARANDO MENSAGENS');
        $mdlIb->persiste($ibdtini['id'],null,null,null,date ('d-m-Y H:i:s'));
        foreach($jsonProc as $itmcmp) {

            $ibmsg = $mdlIb->getItemBibliotecaById($itmcmp->msg)->toArray();
            $tpmsg = null;
            $txtmsg = null;
            $titmsg = null;
            $imgmsg = null;
            $urlmsg = null;

            foreach($ibmsg as $cmpmsg) {
                $tpcmpmsg = current($mdlTib->getById($cmpmsg['id_tib']));
                if($tpcmpmsg['metanome']=='tipomsg'){
                    $infoMsg = current($mdlIb->getIbByPaiByTib($cmpmsg['valor'],$tibtpmsg)->toArray());
                    if ( $infoMsg['valor'] == 'E-mail' ) {
                        $retirarTag = false;
                    }
                    $tpmsg = $infoMsg['valor'];
                } else if($tpcmpmsg['metanome']=='textoserver'){
                    $txtmsg = $cmpmsg['valor'];
                } else if($tpcmpmsg['metanome']=='titulo'){
                    $titmsg = $cmpmsg['valor'];
                } else if($tpcmpmsg['metanome']=='imagem'){
                    $imgmsg = $cmpmsg['valor'];
                } else if($tpcmpmsg['metanome']=='url'){
                    $urlmsg = $cmpmsg['valor'];
                }
            }

            $remetente = (new Legacy_Model_Bo_Pessoa)->getByIdIgnoreTime($campanha[0]['id_criador']);
            $site = current((new Config_Model_Bo_Grupo)->getGruposByIDPaiByMetanome($time, 'SITE'));
            $cracha = current((new Config_Model_Bo_GrupoMetadata)->listMetaByMetanome($site['id'], Config_Model_Bo_GrupoMetadata::META_CRACHA)->toArray());
            $infoCracha = (new Content_Model_Bo_ItemBiblioteca)->getFilhosByIdPai($cracha['valor']);

            foreach($itmcmp->pessoas as $idPes) {
                $tinf = null;
                if($tpmsg=='E-mail') {
                    $tinf = current($mdlTinf->getByMetanome('EMAIL'));
                    $verifOptOut = current($mdlTinf->getByMetanome('OPTOUTEMAIL'));
                    $txtCanc = '<a href="http://www.hash.ws/config/optinout/cancelmail?p=' . $idPes . '&t=' . $time . '">Cancelar recebimento</a>';
                } else if($tpmsg=='SMS') {
                    $tinf = current($mdlTinf->getByMetanome('NUMCEL'));
                    $verifOptOut = current($mdlTinf->getByMetanome('OPTOUTSMS'));
                    $txtCanc = 'Responda "CANCELAR" se não quiser mais receber mensagens';
                }

                $lstInf = array();
                if($tinf) {
                    $lstInf = $mdlInf->getInfoPessoaByMetanome($idPes,$tinf['metanome']);
                    $lstOut = $mdlInf->getInfoPessoaByMetanome($idPes,$verifOptOut['metanome']);
                }
                if(!$lstOut){
                    $convidada = (new Legacy_Model_Bo_Pessoa)->getSimpleById($idPes);

                    $txtmsg = str_replace("%%" . self::MSG_CONVIDADA . "_nome%%", $convidada['nome'], $txtmsg);
                    $txtmsg = str_replace("%%" . self::MSG_REMETENTE . "_nome%%", $remetente['nome'], $txtmsg);
                    $txtmsg = str_replace("%%linkcancelamento%%", $txtCanc, $txtmsg);

                    foreach ($infoCracha as $item) {
                        $infoTib = current((new Config_Model_Bo_Tib)->getById($item['id_tib']));
                        if ($item['metanome'] == $infoTib['mentanome']) {
                            $txtmsg = str_replace("%%" . self::MSG_CANDIDATO . "_" . $infoTib['metanome'] . "%%", $item['valor'], $txtmsg);
                        }
                    }
                    foreach($lstInf as $itemInf) {
                        $arrIb = array();
                        $arrIb[current($mdlTib->getByIdPaiByMetanome($idtibmsg,'campanha'))['id']] = $ib;
                        $arrIb[current($mdlTib->getByIdPaiByMetanome($idtibmsg,'tipomsg'))['id']] = $tpmsg;
                        $arrIb[current($mdlTib->getByIdPaiByMetanome($idtibmsg,'pessoa'))['id']] = $idPes;
                        $arrIb[current($mdlTib->getByIdPaiByMetanome($idtibmsg,'dtcriacao'))['id']] = date ('d-m-Y H:i:s');
                        $arrIb[current($mdlTib->getByIdPaiByMetanome($idtibmsg,'dtiniproc'))['id']] = '';
                        $arrIb[current($mdlTib->getByIdPaiByMetanome($idtibmsg,'dtfimproc'))['id']] = '';
                        $arrIb[current($mdlTib->getByIdPaiByMetanome($idtibmsg,'status'))['id']] = 'NOVA';
                        $arrIb[current($mdlTib->getByIdPaiByMetanome($idtibmsg,'texto'))['id']] = ($retirarTag == true) ? strip_tags($txtmsg) : $txtmsg;
                        $arrIb[current($mdlTib->getByIdPaiByMetanome($idtibmsg,'titulo'))['id']] = $titmsg;
                        $arrIb[current($mdlTib->getByIdPaiByMetanome($idtibmsg,'imagem'))['id']]= $imgmsg;
                        $arrIb[current($mdlTib->getByIdPaiByMetanome($idtibmsg,'url'))['id']] = $urlmsg;
                        $arrIb[current($mdlTib->getByIdPaiByMetanome($idtibmsg,'infoenvio'))['id']] = $itemInf['valor'];
                        $idMsg = $mdlIb->insere($idtibmsg,$pai['id_criador'],$arrIb);
                        $mdlIb->addRelGrupoItem($grpmsg, $idMsg);
                    }
                }
            }
        }

        $mdlIb->persiste($ibstatus['id'],null,null,null,'AGUARDANDO ENVIO');
        $mdlIb->persiste($ibdtfim['id'],null,null,null,date ('d-m-Y H:i:s'));

        $smsserver = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('filedir')['site'];

        $url=$smsserver . "content/robo/enviacampanha?ib=". $ib;

		$myfile = fopen("c:/log_robo_proc.txt", "a");
                fwrite($myfile,$url . "\n");
                fclose($myfile);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        //curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        $res = curl_exec($ch);

        curl_close($ch);

        exit();

    }

    public function enviacampanhaAction() {

        $ib = $this->getRequest()->getParam('ib');

        $mdlIb = new Content_Model_Bo_ItemBiblioteca();
        $mdlTib = new Config_Model_Bo_Tib();
        $mdlInf = new Config_Model_Bo_Informacao();
        $idtibmsg = current($mdlTib->getByMetanome('TPMSGCAMPANHA'))['id'];
        $idcampmsg = current($mdlTib->getByIdPaiByMetanome($idtibmsg, 'campanha'))['id'];
        $idtipomsg = current($mdlTib->getByIdPaiByMetanome($idtibmsg, 'tipomsg'))['id'];
        $idpesmsg = current($mdlTib->getByIdPaiByMetanome($idtibmsg, 'pessoa'))['id'];
        $idmsgtxt = current($mdlTib->getByIdPaiByMetanome($idtibmsg, 'texto'))['id'];
        $idmsgtit = current($mdlTib->getByIdPaiByMetanome($idtibmsg, 'titulo'))['id'];
        $idmsgimg = current($mdlTib->getByIdPaiByMetanome($idtibmsg, 'imagem'))['id'];
        $idmsgurl = current($mdlTib->getByIdPaiByMetanome($idtibmsg, 'url'))['id'];
        $idmsginf = current($mdlTib->getByIdPaiByMetanome($idtibmsg, 'infoenvio'))['id'];
        $idtipotp = current($mdlTib->getByMetanome('TPTIPOMSG'))['id'];
        $idfiltp = current($mdlTib->getByIdPaiByMetanome($idtipotp, 'nome'))['id'];
        $ret = $mdlIb->getItemByTibByValor($idcampmsg,$ib)->toArray();
        $retmsg = array();

        foreach($ret as $msg) {
            $ibpai = current($mdlIb->getById($msg['id_ib_pai'])->toArray())['id'];
            $tipo = current($mdlIb->getIbByPaiByTib($ibpai,$idtipomsg)->toArray());
            $pessoa = current($mdlIb->getIbByPaiByTib($ibpai,$idpesmsg)->toArray());
            $texto = current($mdlIb->getIbByPaiByTib($ibpai,$idmsgtxt)->toArray());
            $titulo = current($mdlIb->getIbByPaiByTib($ibpai,$idmsgtit)->toArray());
            $imagem = current($mdlIb->getIbByPaiByTib($ibpai,$idmsgimg)->toArray());
            $link = current($mdlIb->getIbByPaiByTib($ibpai,$idmsgurl)->toArray());
            $info = current($mdlIb->getIbByPaiByTib($ibpai,$idmsginf)->toArray())['valor'];
            //$ibtipo = current($mdlIb->getItemBibliotecaById($tipo['valor'])->toArray())['valor'];
            //$ibfiltp = current($mdlIb->getIbByPaiByTib($tipo['valor'],$idfiltp)->toArray())['valor'];
            $ibfiltp = $tipo['valor'];
            $ibtexto = $texto['valor'];
            $ibtitulo = $titulo['valor'];
            if($ibfiltp=="SMS") {
                //$contatos = array();
                // $numtel = $mdlInf->getInfoPessoaByMetanome($pessoa['valor'], 'NUMCEL');
                // foreach($numtel as $tel) {
                //     $contatos[] = $tel['valor'];
                // }
                // $contatos = array_unique($contatos);
                // foreach($contatos as $contato){
                $this->enviaSms($info,'21#1',$ibtexto,$ibpai);
                //}
            } else if($ibfiltp=="E-mail") {
                // $contatos = array();
                // $emails = $mdlInf->getInfoPessoaByMetanome($pessoa['valor'], 'EMAIL');
                // foreach($emails as $email) {
                //     $contatos[] = $email['valor'];
                // }
                // $contatos = array_unique($contatos);
                $arrMsg = array('assunto' => $ibtitulo, 'mensagem'=>$ibtexto);

                if (!filter_var($info, FILTER_VALIDATE_EMAIL) === false) {
                    $this->enviaMensagem($arrMsg, array($info), $imagem['valor']);
                } else {
                    $this->enviaMensagem($arrMsg, array(), $imagem['valor']);
                }
            }
        }

        exit();
    }

    public function recebestatussmsAction() {
        $req = $this->getRequest()->getParams();

        if(trim(strtolower($req['message']))=='cancelar'){
            $mdlInf = new Config_Model_Bo_Informacao();
            $lstInf = $mdlInf->getInfoByMetanomeEValor('NUMCEL',str_replace("+55","",$req['number']));
            if(count($lstInf)>0){
                $pes = $lstInf[0]['id_pessoa'];
                $res = $mdlInf->addInformacao($pes, 'OPTOUTSMS', 'S');
                echo $this->_translate->translate('CANCELAMENTO_SMS');
            } else {
                $numero = str_replace("+55","",$req['number']);
            }

        }

        $arq = fopen("c:/smslog.txt",'a');
        $str = '';
        $data = file_get_contents("php://input");
        foreach($req as $chave => $valor) {
            $str = $str . $chave . ' -> ' . $valor . '; ';
        }
        if(strlen($data) > 0) {
            $str = $str . ' - DADO BRUTO: ' . $data;
        }
        $str = $str . "\r\n";
        fwrite($arq,$str);
        fclose($arq);

        exit();
    }


    protected function enviaMensagem(array $mensagem, array $destinatarios = [], $imagem = NULL)
    {
        $myfile = fopen(getcwd() . "/transacional/001/ativacao.html", "r");
        $txtorig = fread($myfile, filesize(getcwd() . "/transacional/001/ativacao.html"));
        fclose($myfile);
        $lstimg = array_diff(scandir(getcwd() . "/transacional/001/imagens"), array('.', '..'));
        $arrImg = array();
        foreach($lstimg as $img) {
            $arrImg[explode('.',$img)[0]] = getcwd() . "/transacional/001/imagens/" . $img;
        }

        if (empty($destinatarios)) {
            $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
            $opcoes = $config->getOption('company');
            $destinatarios = [$opcoes['emailSuporte']];

        }
        // $this->_helper->campanha(
        //     $mensagem,
        //     $destinatarios,
        //     $imagem
        // );

        //$this->_helper->email->sendEmailMailer($destinatarios[0], $mensagem['assunto'], $mensagem['mensagem'], $nome);
        //$this->_helper->campanha->enviarEmail($mensagem, array('fernando@titaniumtech.com.br'), $arrImg);
        $this->_helper->campanha->enviarEmail($mensagem, $destinatarios, $arrImg);
    }

    protected function enviaSms($numero, $card, $msg,$msgid) {

        $card=urlencode($card);
        $numero=urlencode($numero);
        $msg=urlencode($msg);

        $smsserver = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('filedir')['smsserver'];

        $url=$smsserver . "cb/sms_http.php?msg={$msg}&number={$numero}&send_to_sim={$card}&msg_id={$msgid}";
        $myfile = fopen("/data/www/apps/hashws/hash-ws-php/upload_dir/log_robo_enviasms.txt", "a");
        fwrite($myfile,$url . "\n");
        fclose($myfile);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        $res = curl_exec($ch);
        curl_close($ch);

    }

    public function gerasenhaAction() {

        $sal = Random::random_str(16);
        $pwd = hash_pbkdf2('sha1', '123456', $sal, 10000, 40);

        echo 'SAL: ' . $sal . "\nPWD: " . $pwd;

        exit;
    }


    public function habilitarModulosAction(){
        $idPessoa = $this->getRequest()->getParam('codigo');
        $idTime = $this->getRequest()->getParam('time');

        $grupoBo = new Config_Model_Bo_Grupo();
        if (is_null($idTime)) {
            $idGrupoTime = $grupoBo->getTimeByCriador($idPessoa);
        } else {
            $idGrupoTime = current($grupoBo->getGrupoByRepresentacao($idTime))['id'];
        }

        $servicoBo = new Config_Model_Bo_Servico();
        $modulos = $servicoBo->getModulosObrigatorios();

        $rlGrupoServico = new Config_Model_Bo_RlGrupoServico();
        $rlGrupoServico
            ->ativaServicosDoModulo($idPessoa, $idGrupoTime, $modulos);


    }


    public function importarCandidatosAction()
    {
        set_time_limit(0);

        $metatib = $this->getRequest()->getParam('metatib');
        $cargo = $this->getRequest()->getParam('cargo');
        $estados = $this->getRequest()->getParam('estados');
        $limit = $this->getRequest()->getParam('limit');
        $offset = $this->getRequest()->getParam('offset');
        $tibBo = new Content_Model_Bo_TpItemBiblioteca();
        $tibIdCandidato = current($tibBo->getTipoByMetanome($metatib)->toArray())['id'];

        $tibsParaGerarDadosInscricao = [ // -- melhorar essa atribuição, trazer todas e pegar soh as que precisa
            current($tibBo->getTipoByIdPaiByMetanome($tibIdCandidato, 'nomeGuerra')->toArray())['id'] => 'nomeGuerra',
            current($tibBo->getTipoByIdPaiByMetanome($tibIdCandidato, 'numero')->toArray())['id'] => 'numero',
            current($tibBo->getTipoByIdPaiByMetanome($tibIdCandidato, 'cpf')->toArray())['id'] => 'cpf'
        ];

        if (empty($tibIdCandidato)) {
            throw new App_Validate_Exception('imp_cand_tib_nao_encontrado');
        }

        $ibBo = new Content_Model_Bo_ItemBiblioteca();
        $pessoaBo = new Legacy_Model_Bo_Pessoa();
        $grupoBo = new Config_Model_Bo_Grupo();
        $rlGrupoItemBo = new Content_Model_Bo_RlGrupoItem();
        $tibIdCracha = current($tibBo->getTipoByMetanome(Content_Model_Bo_TpItemBiblioteca::META_CRACHA)->toArray())['id'];

//        $listaCandidatos = $ibBo->getAllIbByTib($tibIdCandidato)->toArray();

        $listaCandidatos = $ibBo->getAllIbByTSE($metatib, $cargo, $estados, $limit, $offset);
        foreach ($listaCandidatos as $ibCandidato) {


            $ibsCandidato = $ibBo->getFilhosByIdPai($ibCandidato['id']);
            $dadosInscricao = $this->formataDadosInscricao($ibsCandidato, $tibsParaGerarDadosInscricao);

            if (!$this->candidatoValido($dadosInscricao, $ibsCandidato)) {
                continue;
            }

            $buscarPar = false;
            foreach ($ibsCandidato as $item) {
                if($item['valor'] == self::CARGO_PREFEITO) {
                    $buscarPar  = true;
                    $cargo = self::CARGO_VICEPREFEITO;
                } elseif ($item['valor'] == self::CARGO_VICEPREFEITO){
                   $buscarPar  = true;
                   $cargo = self::CARGO_PREFEITO;
                } else {
                    continue;
                }
            }

            $imagemCandidato = NULL;
            if ( $buscarPar ) {
                $par = (new Content_Model_Bo_Precampanha)->getParCandidatoSemColigacao($ibCandidato['id'], $cargo);
                if (count($par) > 0) {
                    $parCandidato = current($par)['nomeguerra'];
                }
            }

            $arrayCrachaFormatado = $this->formataIbsProCracha($ibsCandidato, $tibIdCandidato, $tibIdCracha, $parCandidato, $imagemCandidato);

            try {
                Zend_Registry::set('TRANSACAO_INGESTAO', true);
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();

                // -- Inscricao
                list($senha, $idPessoa, $idTime) = $pessoaBo->criaPessoaETime(
                    $dadosInscricao['usuario'],
                    $dadosInscricao['email'],
                    $dadosInscricao['time'],
                    $dadosInscricao['alias'],
                    $this->getRequest()->getParam('produtos'),
                    $dadosInscricao['cpf'],
                    $imagemCandidato
                );

                // -- Criacao do cracha
                $idIbPai = $ibBo->insere(
                    $tibIdCracha,
                    $idPessoa,
                    $arrayCrachaFormatado
                );

                // -- Validacao
                $pessoaBo->salvaDeAcordoEInstalaModulos($idPessoa, NULL, false, $imagemCandidato);

                // -- encontra o grupo cracha
                $idGrupoCracha = current($grupoBo->getGrupoByTimeEMetanome($idTime, Config_Model_Bo_Grupo::META_CRACHA))['id'];

                // -- associa a nova ib ao grupo cracha
                $rlGrupoItemBo->relacionaItem($idGrupoCracha, $idIbPai);


                $BoGrupo = new Legacy_Model_Bo_Grupo();
                $rowset  = $BoGrupo->getSiteByCriador($idPessoa);

                foreach ($rowset as $row) {
                    $idMetadata = (new Config_Model_Bo_GrupoMetadata)->listMetaByMetanome($row['id'], Config_Model_Bo_GrupoMetadata::META_CRACHA)->toArray();
                    if ($idMetadata) {
                        (new Config_Model_Bo_GrupoMetadata)->updateMeta($row['id'], Config_Model_Bo_GrupoMetadata::META_CRACHA, $idIbPai);
                    } else {
                        (new Config_Model_Bo_GrupoMetadata)->insere($row['id'], Config_Model_Bo_GrupoMetadata::META_CRACHA, $idIbPai);
                    }
                }
                // -- cms_cracha - metanome/metadata - validacaocontroller
                // -- verificar com o fernando se deve usar o legacy grupo, como no validação

               // -- @todo Transformar em um help
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $opcoes = $config->getOption('filedir');
        $url = $opcoes['site'];

        $urlValidacao = $url . $this->view->url([
                'module' => 'config',
                'controller' => 'validacao',
                'action' => 'index',
                'tipoinstacacao' => 'ativacao',
                'codigo' => $idPessoa
            ]);

        $conteudoEmail = <<<CONTEUDO
E-mail de ativação

Ative sua conta na url: {$urlValidacao}

Dados de acesso
O seu usuário é: {$dadosInscricao['usuario']}
A sua senha é: {$senha}

Dúvidas: suporte@titaniumtech.com.br
CONTEUDO;

//                $this->_helper->campanha(
//                    [
//                        'assunto' => $this->_translate->translate('email_de_confirmacao'),
//                        'mensagem' => $conteudoEmail
//                    ],
//                    ['diretoria@titaniumtech.com.br', 'felipe@titaniumtech.com.br']
//                );
                Zend_Db_Table::getDefaultAdapter()->commit();
            } catch (Exception $ex) {
                Zend_Db_Table::getDefaultAdapter()->rollBack();

                // -- @todo: transformar em um helper
                $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');
                file_put_contents(
                    "{$filedir->path}log_ingestao_candidados.txt",
                    $ex->getMessage() . " - ib: " . current($ibsCandidato)['id_ib_pai'] . "\n",
                    FILE_APPEND
                );

//                x([$ex->getMessage(), $ex->getTraceAsString()]);
            }

//            x([$senha, $idPessoa, $dadosInscricao], false);
//
//            die('dieeeeee');

        }
        exit;
    }

    public function importainfo2Action() {
        set_time_limit(0);
        $db = Zend_Db_Table::getDefaultAdapter();
        $saida = false;
        while (!$saida) {
            $select = $db->select()->from(array('inf2' => 'tb_informacao_2'),array('id','valor','id_criador','id_tinfo','id_pai','id_pessoa','dt_criacao'))->where('importado is null')->limit(100,0);
            $qry = $db->query($select);
            $x = $qry->fetchAll();
            if(count(x)==0) {
                $saida = true;
            } else {
                foreach($x as $item){
                    $slcvrfy = $db->select()->from(array('inf' => 'tb_informacao'),array('id'))->where('id = ?',$item['id']);
                    $qry2 = $db->query($slcvrfy);
                    $vrfy = $qry2->fetchAll();
                    if(count($vrfy)==0){
                        $data = array(
                            'id' => $item['id'],
                            'valor' => $item['valor'],
                            'id_criador' => $item['id_criador'],
                            'id_tinfo' => $item['id_tinfo'],
                            'id_pai' => $item['id_pai'],
                            'id_pessoa' => $item['id_pessoa'],
                            'dt_criacao' => $item['dt_criacao']
                        );

                        $db->insert('tb_informacao', $data);
                    }
                    $upd = array('importado' => 's');
                    $db->update('tb_informacao_2',$upd,"id = '{$item['id']}'");
                }
            }
        }

        exit;
    }

    protected function candidatoValido($dadosInscricao, $ibsCandidato)
    {
        if (empty($dadosInscricao['usuario']) || empty($dadosInscricao['cpf'])) {
            // -- @todo: transformar em um helper
            $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');
            file_put_contents(
                "{$filedir->path}log_ingestao_candidados.txt",
                "Candidado com dados inválidos - ib: " . current($ibsCandidato)['id_ib_pai'] . "\n",
                FILE_APPEND
            );

            return false;
        } else {
            $pessoa = (new Legacy_Model_Bo_Pessoa())->find("nome = '{$dadosInscricao['usuario']}'");
            if (count($pessoa) > 0) {
                return false;
            }
        }
        return true;
    }

    protected function formataDadosInscricao($ibsCandidato, $tibsParaGerarDadosInscricao)
    {
        $dados = [];

        foreach ($ibsCandidato as $ibInfoCandidato) {
            if (!array_key_exists($ibInfoCandidato['id_tib'], $tibsParaGerarDadosInscricao)) {
                continue;
            }

            $dados[$tibsParaGerarDadosInscricao[$ibInfoCandidato['id_tib']]] = $ibInfoCandidato['valor'];

            if (count($dados) == count($tibsParaGerarDadosInscricao)) {
                break;
            }
        }

        $dados['nomeGuerra'] = $this->limparString($dados['nomeGuerra']);

        $dadosInscricao = [
            'cpf' => $dados['cpf'],
            'usuario' => strtolower("{$dados['nomeGuerra']}{$dados['numero']}")
        ];

        return array_merge($dadosInscricao, [
            'email' => "{$dadosInscricao['usuario']}@hash.ws",
            'time' => $dadosInscricao['usuario'],
            'alias' => $dadosInscricao['usuario']
        ]);
    }

    protected function formataIbsProCracha($ibsCandidato, $tibIdCandidato, $tibIdCracha, $parCandidato, &$imagemCandidato)
    {

        if (!isset($this->tibsCandidato)) {
            $tibsCandidato = (new Content_Model_Bo_TpItemBiblioteca())
                ->getTipoByIdPai($tibIdCandidato)
                ->toArray();

            $this->tibsCandidato = [];
            foreach ($tibsCandidato as $tibCand) {
                $this->tibsCandidato[$tibCand['id']] = $tibCand['metanome'];
            }
        }

        if (!isset($this->tibsCracha)) {
            $tibsCracha = (new Content_Model_Bo_TpItemBiblioteca())
                ->getTipoByIdPai($tibIdCracha)
                ->toArray();

            $this->tibsCracha = [];
            foreach ($tibsCracha as $tibCracha) {
                $this->tibsCracha[$tibCracha['metanome']] = $tibCracha['id'];
            }
        }

        $dadosCandidato = [];
        foreach ($ibsCandidato as $ibCand) {
            $dadosCandidato[$this->tibsCandidato[$ibCand['id_tib']]] = $ibCand['valor'];
        }
        $imagemCandidato = $dadosCandidato['imagem'];
        $dadosCracha = [
            $this->tibsCracha['NOME'] => $dadosCandidato['nomeGuerra'],
            $this->tibsCracha['CARGO'] => $dadosCandidato['cargo'],
            $this->tibsCracha['PARTIDO'] => $dadosCandidato['partido'],
            $this->tibsCracha['ESTADO'] => $dadosCandidato['uf'],
            $this->tibsCracha['CIDADE'] => $dadosCandidato['cidade'],
            $this->tibsCracha['NUMERO'] => $dadosCandidato['numero'],
            $this->tibsCracha['IMGTOPO'] => 'IMGTOPO',
            $this->tibsCracha['IMGLOGO'] => 'IMGLOGO',
            $this->tibsCracha['FOTO'] => $dadosCandidato['imagem'],
            $this->tibsCracha['SIGLA'] => $dadosCandidato['partidoSigla'],
            $this->tibsCracha['NOMECOMPLETO'] => $dadosCandidato['nome'],
            $this->tibsCracha['CPF'] => $dadosCandidato['cpf'],
            $this->tibsCracha['SITUACAO'] => $dadosCandidato['situacao'],
            $this->tibsCracha['PARTCOLIG'] => $dadosCandidato['coligacaoPartidos'],
            $this->tibsCracha['NOMECOLIG'] => $dadosCandidato['coligacaoNome'],
            $this->tibsCracha['EMAIL'] => $dadosCandidato['email'],
            $this->tibsCracha['PARTIDONUM'] => $dadosCandidato['partidoNumero'],
            $this->tibsCracha['PARCANDIDATO'] => $parCandidato
        ];

        unset($dadosCracha[null]);
        return $dadosCracha;
    }

    /*
     * Gera os PARCEIROSS dos cadidatos eleitorais municipais ( prefeito, vice)
     */
    public function geraparcandidatoAction() {
        $saida = false;
        $cnt=0;
        while(!$saida) {
            $cand = current($this->_bo->getProximoCandidatoSemPar());
            if(!$cand){
                $saida = true;
            } else {
                if($cnt == $_GET['cnt']) {
                    $saida = true;
                } else {
                    $cnt++;
                    $vice = current($this->_bo->getCandidadoPorColig($cand['cidade'],$cand['uf'],$cand['colig'],'VICE-PREFEITO'));
                    // x($cand,false);
                    // x($vice);
                    echo $cand['id'] . ' - ' . $vice['id'] . '</br>';
                    // x($this->_bo->getTpItemBiblioteca(null,'PARCANDIDATO')->toArray()['id']);
                    $this->_bo->persiste(null,$this->_bo->getTpItemBiblioteca(null,'parCandidato',$this->_bo->getTpItemBiblioteca(null,'TPINGCANDTSE')->toArray()['id'])->toArray()['id'],null,$cand['id'],$vice['nome']);
                    $this->_bo->persiste(null,$this->_bo->getTpItemBiblioteca(null,'parCandidato',$this->_bo->getTpItemBiblioteca(null,'TPINGCANDTSE')->toArray()['id'])->toArray()['id'],null,$vice['id'],$cand['nome']);
                }
            }
        }

        exit;
    }

    public function enviasmsloteAction() {
        $total = 40;

        for($cnt=1;$cnt<$total;$cnt++){
            echo 'enviando ' . $cnt . ' de ' . $total . '<br>';
            $this->enviaSms('61983830169',null,"TESTE DE ENVIO EM LOTE {$cnt} DE {$total}",null);
        }
        echo 'feito';

        exit;
    }

    public function preparatmpcampanhaAction () {

        set_time_limit(0);
        $saida = false;
        $myfile = fopen(getcwd() . "/transacional/001/ativacao.html", "r");
        $txtorig = fread($myfile, filesize(getcwd() . "/transacional/001/ativacao.html"));
        fclose($myfile);
        $lstimg = array_diff(scandir(getcwd() . "/transacional/001/imagens"), array('.', '..'));
        $arrImg = array();
        foreach($lstimg as $img) {
            $arrImg[explode('.',$img)[0]] = getcwd() . "/transacional/001/imagens/" . $img;
        }

        while(!$saida) {

            $tmpret = $this->_bo->geratmpemailinfocand();

            if(count($tmpret) > 0) {
                $ret = current($tmpret);
                $tmpemail = $txtorig;
                $ret['nometime'] = $this->limparString($ret['nometime']);
                $tmpemail = str_replace("%%HASH_URL%%",'http://www.hash.ws/config/ingestao/index/codigo/' . $ret['id'],$tmpemail);
                $tmpemail = str_replace("%%TIME_HASH%%",$ret['nometime'],$tmpemail);
                //$tmpemail = str_replace("%%linkcancelamento%%",'http://www.hash.ws/config/optinout/cancelcand?c=' . $ret['id'],$tmpemail);
                $tmpemail = str_replace("%%linkcancelamento%%",'http://www.hash.ws/content/robo/cancelcand/?c=' . $ret['id'],$tmpemail);
                echo $ret['id'].' inserido !<Br>';
                $idret = $this->_bo->inseretmpemailcand($ret['id'],$ret['nome'],$ret['email'],$ret['nometime'],$tmpemail,'N');
                //$this->_helper->email->sendEmailMailer('fernando@titaniumtech.com.br', 'Teste do email com imagem', $tmpemail, 'TitaniumTech', $arrImg);
                $msg = array('assunto' => '[Lançamento gratuito] Ganhe a eleição com ajuda do poderoso Sistema HASH', 'mensagem' => $tmpemail);


                $email = strtolower($ret['email']);

                 if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    $this->_helper->campanha->enviarEmail($msg, array($email), $arrImg);
                }

                //$this->_helper->campanha->enviarEmail($msg, array('frederico@titaniumtech.com.br'), $arrImg);

            } else {
                $saida = true;
            }
        }
        exit;
    }

    //07ddc950-f542-4a1a-d40a-45032bca8f17 id de teste, faze ro descancelamento
    public function cancelcandAction()
    {
        $pes = $_GET['c'];
        $ibBo = new Content_Model_Bo_ItemBiblioteca();
        $ibBo->atualizastatuscand($pes,'C');

        echo '<center><h3>Sua inscrição foi cancelada!!!</h3><center>'; exit;
    }


    public function carregacandsAction()
    {
        set_time_limit(0);

        $uf = $_GET['uf'];
        $pesBo = new Legacy_Model_Bo_Pessoa();
        $tibBo = new Config_Model_Bo_Tib();
        $clsBo = new Config_Model_Bo_Classificacao();
        $vncBo = new Config_Model_Bo_RlVinculoPessoa();
        $grpBo = new Config_Model_Bo_Grupo();
        $usrBo = new Auth_Model_Bo_Usuario();
        $grp = current($grpBo->getGrupoByMetanome('HASH'))['id'];
        $tp = current($tibBo->getByMetanome('TPINGCANDTSE'))['id'];
        $tppes = current($tibBo->getByIdPaiByMetanome($tp,'idpessoa'))['id'];
        $idcls = current($clsBo->getByMetanome('CANDIDATO_2016')->toArray())['id'];
        $saida = false;
        while(!$saida){
            $tmpret = $this->_bo->geratmppessoainfocand($uf);
            $idIbPai = $tmpret[0]['id'];
            if(count($tmpret)>0) {
                $arrItm = array();
                foreach($tmpret as $rec) {
                    $arrItm[$rec['metanome']] = $rec['valor'];
                }

                $nomeguerra = strtolower($this->limparString($arrItm['nomeGuerra']));
                $usrname = $nomeguerra . $arrItm['numero'];
                echo date('Y-m-d H:i:s') . " - Carregando " . $usrname;
                $usr = $usrBo->getUserByNomeUsuario($usrname);
                $usr = $usr->toArray();

                if(!((is_array($usr)) && (count($usr)>0))) {
                    echo " - CRIANDO ";

                    $idPessoa = $pesBo->criar_usuario($arrItm['nome'], null, $usrname, $arrItm['cpf'], '{EMAIL=' . $arrItm['email'] . ',CARGO=' . $arrItm['cargo'] . ',CPF=' . $arrItm['cpf'] . ',AVATAR=' . $arrItm['imagem'] . ',CIDADE=' . utf8_encode($arrItm['cidade']) . ',UF=' . $arrItm['uf'] . '}');
                    $vncBo->persiste(null,$idcls,$idPessoa[0][0]['criar_usuario'],null,$grp,date('Y-m-d H:i:s'),null);
                    $this->_bo->persiste(null,$tppes,$idPessoa[0][0]['criar_usuario'],$idIbPai,$idPessoa[0][0]['criar_usuario']);
                } else {
                    echo " - EXISTENTE ";

                    $vnc = $vncBo->getVinculoByClsPesGrp($idcls,$usr[0]['id'],$grp);
                    if((!((is_array($vnc)) && (count($vnc)>0)))){
                        echo " - CRIEI VINCULO!!!";
                        $vncBo->persiste(null,$idcls,$usr[0]['id'],null,$grp,date('Y-m-d H:i:s'),null);
                    } else {
                        echo " - NAO CRIEI VINCULO!!!";
                    }
                    $this->_bo->persiste(null,$tppes,$usr[0]['id'],$idIbPai,$usr[0]['id']);
                }

                echo "<br>";
                flush();
            } else {
                echo 'Finalizado';
                $saida = true;
            }
        }

        exit;
    }


    /**
     *
     * @param type $texto
     * @return type
     * @todo Transformar em helper
     */
    protected function limparString($texto)
    {

        $texto = str_replace(
            ['á', 'à', 'ã', 'â', 'é', 'ẽ', 'ê', 'í', 'ó', 'õ', 'ô', 'ú', 'ç', ' ', '-', "\'"],
            ['a', 'a', 'a', 'a', 'e', 'e', 'e', 'i', 'o', 'o', 'o', 'u', 'c', '', '', ''],
            $texto
        );

        return str_replace(
            ['Á', 'À', 'Ã', 'Â', 'É', 'Ẽ', 'Ê', 'Í', 'Ó', 'Õ', 'Ô', 'Ú', 'Ç', ' ', '-', "\'"],
            ['A', 'A', 'A', 'A', 'E', 'E', 'E', 'I', 'O', 'O', 'O', 'U', 'C', '', '',''],
            $texto
        );
    }

    public function ingestaoConfirmacao2Action()
    {
        $idPessoa = $this->getParam('pessoa');
        $idIbPai = $this->getParam('ib');
        $nome = $this->getParam('nome');
        $nome2 = $this->getParam('nome2');
        $time = $this->getParam('time');
        $alias = $this->getParam('alias');
        $usuario = $this->getParam('usuario');

        $email = $this->getParam('email');

        $pessoaBo = new Legacy_Model_Bo_Pessoa();
        $grupoBo = new Config_Model_Bo_Grupo();
        $idGrupoPessoal = current(
            (new Config_Model_Bo_RlGrupoPessoa())
                ->getGrupoPessoalNaInstalacao($idPessoa)
        )['id_grupo'];
        $idGrupoHash = current($grupoBo->getGrupoByMetanome(
            Config_Model_Bo_Grupo::META_GRUPOELEGIE)
        )['id'];
        $idGrupoModelo = current($grupoBo->getGrupoByMetanome('comitemodelo'))['id'];

        // -- Cria entidade
        $idEntidade = $pessoaBo->criar_entidade(
            $idPessoa,
            $nome,
            $nome2,
            $idGrupoPessoal
        );
        $idEntidade = current($idEntidade)['criar_entidade'];

        // -- Informações do cracha
        $ibBo = new Content_Model_Bo_ItemBiblioteca();
        $ibsCandidato = $ibBo->getFilhosByIdPai($idIbPai);

        $tibBo = new Content_Model_Bo_TpItemBiblioteca();
        $tibIdCandidato = current($tibBo->getTipoByMetanome('TPINGCANDTSE')->toArray())['id'];

        $infoCracha = $this->formataIbsProCracha2(
            $ibsCandidato,
            $tibIdCandidato
        );

        // -- Cria time
        $grupoBo->criar_time(
            $time,
            $idEntidade,
            $idGrupoHash,
            $idPessoa,
            $idCanal = null,
            $publico = 'f',
            $metanomeTime = $usuario,
            $descricao = null,
            $alias,
            $idGrupoModelo,
            $infoCracha,
            $grupoMetadados = ''
        );

        // -- Enviar o e-mail de configuração
        $siteUrl = $this->_helper->configuracao('filedir', 'site');
        $baseUrl = <<<HTML
<base href="{$siteUrl}/transacional/001/">
HTML;

        $conteudoEmail = str_replace(
            ['%%HASH_PERFIL_CONFIG%%'],
            ["{$siteUrl}/config/ingestao/perfil/codigo/{$idPessoa}"],
            $this->_helper->conteudo('transacional/001/configurado.html')
        );

        $this->_helper->email->sendEmailMailer(
            $email,
            $this->session->email,
            'Seu HASH foi configurado!',
            $conteudoEmail,
            'HASH Team'
        );
        exit;
    }

    /**
     * Faz a correspondência entre os metadados do crachá com os metadados da inscrição.
     *
     * IMPORTANTE: $imagemCandidato é um parâmetro de saída, pois o caminho da image
     * do candidato é utilizado no restante do processo de instalação além de ser
     * utilizado na criação do crachá.
     *
     * @param array $ibsCandidato Dados de ingestão do candidato
     * @param string $tibIdCandidato UUID do tib de ingestão do candidato
     * @return string Metadados do crachá já preparados para persistência
     */
    protected function formataIbsProCracha2($ibsCandidato, $tibIdCandidato)
    {
        // -- Nomeando as tibs de ingestão
        $dadosTibCandidado = (new Content_Model_Bo_TpItemBiblioteca())
            ->getTipoByIdPai($tibIdCandidato)
            ->toArray();

        $tibsCandidato = [];
        foreach ($dadosTibCandidado as $tibCand) {
            $tibsCandidato[$tibCand['id']] = $tibCand['metanome'];
        }

        // -- Associando as informações do crachá, com as informações de ingestão
        $dadosCracha = [
            "IMGTOPO=IMGTOPO",
            "IMGLOGO=IMGLOGO"
        ];
        foreach ($ibsCandidato as $ibCand) {

            // -- Pula metadado sem valor
            if (empty($ibCand['valor'])) {
                continue;
            }

            switch ($tibsCandidato[$ibCand['id_tib']]) {
                case 'nomeGuerra':
                    $dadosCracha[] = "NOME={$ibCand['valor']}";
                    break;
                case 'cargo':
                    $dadosCracha[] = "CARGO={$ibCand['valor']}";
                    break;
                case 'partido':
                    $dadosCracha[] = "PARTIDO={$ibCand['valor']}";
                    break;
                case 'uf':
                    $dadosCracha[] = "ESTADO={$ibCand['valor']}";
                    break;
                case 'cidade':
                    $dadosCracha[] = "CIDADE={$ibCand['valor']}";
                    break;
                case 'numero':
                    $dadosCracha[] = "NUMERO={$ibCand['valor']}";
                    break;
                case 'imagem':
                    $dadosCracha[] = "FOTO={$ibCand['valor']}";
                    break;
                case 'partidoSigla':
                    $dadosCracha[] = "SIGLA={$ibCand['valor']}";
                    break;
                case 'nome':
                    $dadosCracha[] = "NOMECOMPLETO={$ibCand['valor']}";
                    break;
                case 'cpf':
                    $dadosCracha[] = "CPF={$ibCand['valor']}";
                    break;
                case 'situacao':
                    $dadosCracha[] = "SITUACAO={$ibCand['valor']}";
                    break;
                case 'coligacaoPartidos':
                    $dadosCracha[] = "PARTCOLIG={$ibCand['valor']}";
                    break;
                case 'partido':
                    $dadosCracha[] = "NOMECOLIG={$ibCand['valor']}";
                    break;
                case 'coligacaoNome':
                    $dadosCracha[] = "EMAIL={$ibCand['valor']}";
                    break;
                case 'partidoNumero':
                    $dadosCracha[] = "PARTIDONUM={$ibCand['valor']}";
                    break;
                case 'parCandidato':
                    $dadosCracha[] = "PARCANDIDATO={$ibCand['valor']}";
                    break;
            }
        }

        $infoCracha = 'CMSGRUPOCRACHA={TPCRACHACOMITE={'
            . implode(',', $dadosCracha)
            . '}}';

        return $infoCracha;
    }

    public function processaArquivosAction()
    {
        $this->identity  = Zend_Auth::getInstance()->getIdentity();
        $servico = $this->identity->servicos[$this->getParam('servico')];

        $boItemBiblioteca = new Content_Model_Bo_ItemBiblioteca();

        while(1==1) {
            $boItemBiblioteca->processaArquivos($servico);
            exit('rodou 1 vez');
            sleep(1);
        }

    }
}
