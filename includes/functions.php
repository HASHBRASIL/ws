<?php
    /*
     * Descrição: - É necessário recuperar o MASTER deputado                          | tp_itembiblioteca
     *            - Buscar todos os itens de biblioteca usando a master               | tb_itembiblioteca
     *            - Buscar todos os itens de biblioteca que pertencem a essa !classe! | tb_itembiblioteca
     *            - Buscar template do tipo da master                                 | tp_itembiblioteca
     *            - Buscar cada area especifica para descobrir o campo ($sql)         | tp_itembiblioteca
     * Função:    "Retorna dados do banco para gerar campos dinamicos em html e zaz"
     * Tabelas:   "tb_itembiblioteca | tp_itembiblioteca"
     *
     *
     */

    function createFormElement($tipo, $valor, $placeholder, $array)
    {
        $field = '';
        if($tipo == "text" || $tipo == 'date' ){
            $field .= "<input type='text' placeholder='".$placeholder."' value='".$valor."'";
            foreach($array as $key => $value){
                $field .= $key .'="'.$value.'"';
                if($key == 'id')
                    $id = $value;
            }
            $field .=  "/>";

        }else if($tipo == 'textarea'){
            $field = '<textarea ';
            foreach($array as $key => $value){
                $field .= $key .'="'.$value.'"';
                if($key == 'id')
                    $id = $value;
            }
            $field .= '>'.$valor.'</textarea>';

        }else if($tipo == 'imagem'){
            $field = '<input type="file" ';
            foreach($array as $key => $value){
                $field .= $key .'="'.$value.'"';
                if($key == 'id')
                    $id = $value;
            }
            $field .=  "/>";
        }
        return $field;
    }

    /********************************************************************************************
                            FUNÇÕES DE SQL
    *********************************************************************************************/

    /**
     * Recupera dados do grupo HASH via metadata
     * @param  PDO::Connection  $connection     Variável de conexão do tipo PDO
     * @param  string           $metanomeHash   Metanome do grupo HASH
     * @param  PDO::fetch_style $param          Tipo de retorno do PDO, default PDO::FETCH_ASSOC ( PDO::FETCH_ASSOC, PDO::FETCH_BOTH, PDO::FETCH_BOUND, PDO::FETCH_CLASS, PDO::FETCH_INTO, PDO::FETCH_LAZY, PDO::FETCH_NAMED, PDO::FETCH_NUM, PDO::FETCH_OBJ )
     * @return array
     */
    function getHash( $connection, $metanomeHash, $param = PDO::FETCH_ASSOC )
    {
        $query = $connection->prepare( "SELECT * FROM tb_grupo WHERE metanome = :metanomeHash" );
        $query->bindParam( ':metanomeHash', $metanomeHash, PDO::PARAM_STR );
        $query->execute();
        $hash = $query->fetch( $param );
        return $hash;
    }

    /**
     * Retorna servico por id
     * @param string        $metanome    Metanome do serviço a ser retornado
     * @param string        $connection  Variável de conexão do tipo PDO
     * @param fetch_style   $param       Tipo de retorno do PDO, default PDO::FETCH_ASSOC  ( PDO::FETCH_ASSOC, PDO::FETCH_BOTH, PDO::FETCH_BOUND, PDO::FETCH_CLASS, PDO::FETCH_INTO, PDO::FETCH_LAZY, PDO::FETCH_NAMED, PDO::FETCH_NUM, PDO::FETCH_OBJ )
     * @return array
     */
    function getIdServico( $metanome, $connection, $param = PDO::FETCH_ASSOC )
    {
        $buscaIDServico = $connection->prepare( "SELECT id FROM tb_servico WHERE metanome = :metanome" );
        $buscaIDServico->bindParam( ':metanome', $metanome );
        $buscaIDServico->execute();
        $IDServico     = $buscaIDServico->fetchAll( $param );
        return $IDServico;
    }

    /**
     * Retorna lista de grupos
     * @param $connection Variável de conexão com o banco de dados
     * @param $limite     Limite da query, default NULL
     * @param $param      Tipo de retorno do PDO, default PDO::FETCH_ASSOC
     */
    function getGrupos( $connection, $limite = null, $param = PDO::FETCH_ASSOC )
    {
        if( !is_null( $limite ) ){
            $buscaGrupos = $connection->prepare( "SELECT * FROM tb_grupo LIMIT = :limite" );
            $buscaGrupos->bindParam( ':limite', $limite );
            $buscaGrupos->execute();

            $grupos = $buscaGrupos->fetchAll( $param );
        }else{
            $buscaGrupos = $connection->query( "SELECT * FROM tb_grupo" );
            $buscaGrupos->execute();

            $grupos = $buscaGrupos->fetchAll( $param );
        }
        return $grupos;
    }

    /**
     * Retorna lista de grupos
     * @param $connection Variável de conexão com o banco de dados
     * @param $limite     Limite da query, default NULL
     * @param $param      Tipo de retorno do PDO, default PDO::FETCH_ASSOC
     */
    function getGrupoByID( $connection, $id, $param = PDO::FETCH_ASSOC )
    {
        $buscaGrupo = $connection->prepare( "SELECT * FROM tb_grupo WHERE id = :id" );
        $buscaGrupo->bindParam( ':id', $id );
        $buscaGrupo->execute();

        $grupo = $buscaGrupo->fetchAll( $param );
        return $grupo;
    }

    /**
     * Retorna lista de entidades por usuário
     * @param $connection Variável de conexão com o banco de dados
     * @param $user_id    Id do usuário que
     * @param $param      Tipo de retorno do PDO, default PDO::FETCH_ASSOC
     */
    function getEntidadesByUser( $connection, $user_id, $param = PDO::FETCH_ASSOC )
    {
        $buscaEntidades = $connection->prepare(
            "WITH RECURSIVE getGEM AS (
                SELECT * FROM tb_grupo WHERE id IN ( SELECT id_grupo FROM rl_grupo_pessoa WHERE id_pessoa = :idUsuario )
            UNION
                SELECT g.* FROM tb_grupo g JOIN getGEM gg ON ( gg.id_pai = g.id )
            ) SELECT * FROM getGEM WHERE id_representacao IS NOT NULL ORDER BY nome"
        );
        $buscaEntidades->bindParam( ':idUsuario', $user_id );
        $buscaEntidades->execute();
        $entidades     = $buscaEntidades->fetchAll( $param );
        return $entidades;
    }

    /**
     * Retorna lista de servicos
     * @param $connection Variável de conexão com o banco de dados
     * @param $limite     Limite da query, default NULL
     * @param $param      Tipo de retorno do PDO, default PDO::FETCH_ASSOC
     */
    function getServicos( $connection, $limite = null, $param = PDO::FETCH_ASSOC )
    {
        if( !is_null( $limite ) )
        {
            $BuscaServico = $connection->prepare( "SELECT * FROM tb_servico LIMIT = :limite" );
            $BuscaServico->bindParam( ':limite', $limite );
            $BuscaServico->execute();

            $servicos = $BuscaServico->fetchAll( $param );
        }else
        {
            $BuscaServico = $connection->query( "SELECT * FROM tb_servico" );
            $BuscaServico->execute();

            $servicos = $BuscaServico->fetchAll( $param );
        }
        return $servicos;
    }

    /**
     * Função para retorno da url do sistema
     * @return type
     */
    function base_url()
    {
        $system_url = "http://".$_SERVER['HTTP_HOST']."/";
        return $system_url;
    }

    /**
     * Retorna servico por id
     * @param $metanome   Metanome do serviço a ser retornado
     * @param $connection Variável de conexão com o banco de dados
     * @param $param      Tipo de retorno do PDO, default PDO::FETCH_ASSOC
     */
    function getGrupoByMetanome( $metanome, $connection, $param = PDO::FETCH_ASSOC )
    {
        $query = $connection->prepare( "SELECT * FROM tb_grupo WHERE metanome = :metanome" );
        $query->bindParam( ':metanome', $metanome );
        $query->execute();
        $grupo = $query->fetchAll( $param );
        return $grupo;
    }

    /**
     * Description
     * @param  type                 $idGrupo
     * @param  PDO::Connection      $connection
     * @param  PDO::fetch_style     $param
     * @return array
     */
    function getDadosGrupoByID( $idGrupo, $connection, $param = PDO::FETCH_ASSOC )
    {
        $query = $connection->prepare(
            "SELECT
                grupo.*, grupoMD.valor AS arquivo, rgp.nomehash AS alias
            FROM
                tb_grupo grupo
            FULL OUTER JOIN
                tb_grupo_metadata grupoMD ON ( grupo.id = grupoMD.id_grupo )
            FULL OUTER JOIN
                rl_grupo_pessoa rgp       ON ( grupo.id = rgp.id_grupo     )
            WHERE
                grupo.id = :idGrupo
            "
        );
        $query->bindParam( ':idGrupo', $idGrupo );
        $query->execute();
        $dadosGrupo = $query->fetchAll( $param );
        return $dadosGrupo;
    }

    /**
     * Description
     * @param type                  $idUserLogado
     * @param PDO::Connection       $connection
     * @param PDO::fetch_style      $param
     * @return array
     */
    function getNomeUsuarioByID ( $idUserLogado, $connection, $param = PDO::FETCH_ASSOC )
    {
        $queryNomeUsuario = $connection->prepare( "SELECT nome FROM tb_pessoa WHERE id = :idUserLogado " );
        $queryNomeUsuario->bindParam( ':idUserLogado', $idUserLogado );
        $queryNomeUsuario->execute();
        $nomeUsuario = $queryNomeUsuario->fetch( $param );
        return $nomeUsuario;
    }

    /**
     * Description
     * @param PDO::Connection       $connection
     * @param PDO::fetch_style      $param
     * @return array
     */
    function getTimesByUser( $connection, $param = PDO::FETCH_ASSOC )
    {
        $usuariosHash = $connection->query(
            "SELECT
                tb_pessoa.id,
                tb_pessoa.nome,
                rl_grupo_pessoa.nomehash
            FROM
                tb_pessoa
                INNER JOIN
                rl_grupo_pessoa ON ( tb_pessoa.id = rl_grupo_pessoa.id_pessoa )
            WHERE
                nomehash IS NOT NULL" );
        $usuariosHash->execute();
        $pessoas = $usuariosHash->fetchAll( $param );
        return $pessoas;
    }

    /**
     * Description
     * @param type      $metanome
     * @param type      $valor
     * @param type      $connection
     * @param type      $param
     * @return type
     */
    function getIdItembibliotecaByMetanome( $metanome, $valor, $connection, $param = PDO::FETCH_ASSOC )
    {
        $query = $connection->prepare("SELECT id_ib FROM tb_itembiblioteca_metadata WHERE metanome = :metanome AND valor = :valor");
        $query->bindParam(':metanome', $metanome);
        $query->bindParam(':valor',    $valor);
        $query->execute();
        $dados = $query->fetch($param);
        if (!isset($dados['id_ib'])) {
            $dados['id_ib'] = -1;
        }
        return $dados['id_ib'];
    }

    /**
     * Description
     * @param type      $connection
     * @param type      $param
     * @return type
     */
    function getTimes( $connection, $param = PDO::FETCH_ASSOC )
    {
        $query = $connection->query( "SELECT * from tb_grupo WHERE id_representacao IS NOT NULL" );
        $times = $query->fetchAll( $param );
        return $times;
    }

    /**
     * Função para comparar nome de uma pessoa e retorna quantidade se encontrado.
     * @param $connection
     * @param $createNomeTime
     * @param int $param
     * @return int
     */
    function getNomePessoaByCreateNomeTime ( $connection, $createNomeTime, $param = PDO::FETCH_ASSOC )
    {
        $query = $connection->prepare( "SELECT nome FROM tb_pessoa WHERE nome ILIKE  '%' || :createNomeTime || '%'" );
        $query->bindParam( ':createNomeTime', $createNomeTime, PDO::PARAM_STR );
        $query->execute();
        $nomesEncontrados = $query->fetchAll( $param );
        $nomes = array(
            "matches" => count( $nomesEncontrados ),
            "nomes"   => $nomesEncontrados,
        );
        return $nomes;
    }

    /**
     * Função para upload de img
     * @param  string   $nameInputForm         Nome que o $_FILES[''] recebe como parametro, name do input
     * @param  string   $pastaUpload           Diretório que vai ser feito o upload
     * @param  array    $extensoesPermitidas   Array com as extensões permitidas
     * @param  boolean  $renomeia              Renomeia o arquivo ao fazer upload, se true é necessário passar o proximo parametro da função
     * @param  string   $nomeFinalArquivo      Nome final que o arquivo vai ter
     * @return boolean
     */
    function uploadFile( $nameInputForm, $pastaUpload, $extensoesPermitidas, $renomeia = false, $nomeFinalArquivo = null )
    {
        if( $_FILES[$nameInputForm]['name'][0] != '' )
        {
            $upload['pasta']        = $pastaUpload;
            // $upload['pasta']  = 'var/resource/imagen/';
            $upload['tamanho']      = 1024 * 1024 * 2; // 2Mb
            $upload['extensoes']    = $extensoesPermitidas;
            $upload['renomeia']     = $renomeia;

            // Array com os tipos de erros de upload do PHP
            $upload['erros'][0] = 'Não houve erro';
            $upload['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
            $upload['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
            $upload['erros'][3] = 'O upload do arquivo foi feito parcialmente';
            $upload['erros'][4] = 'Não foi feito o upload do arquivo';

            // Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
            if ( $_FILES[$nameInputForm]['error'][0] != 0 )
            {
                // die("Não foi possível fazer o upload, erro:" . $upload['erros'][$_FILES[$nameInputForm]['error']]);
                return false;
                exit; // Para a execução do script
            }

            // Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar

            // Faz a verificação da extensão do arquivo
            $extensao = strtolower( end( explode('.', $_FILES[$nameInputForm]['name'][0] ) ) );
            if ( array_search( $extensao, $upload['extensoes'] ) === false )
            {
                // echo "Por favor, envie arquivos com as seguintes extensões: ".implode( ", ", $extensoesPermitidas );
                return false;
                exit;
            }

            // Faz a verificação do tamanho do arquivo
            if ( $upload['tamanho'] < $_FILES[$nameInputForm]['size'][0] )
            {
                // echo "O arquivo enviado é muito grande, envie arquivos de até 2Mb.";
                return false;
                exit;
            }

            // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta

            // Primeiro verifica se deve trocar o nome do arquivo
            if ( $upload['renomeia'] == true )
            {
                // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
                $nome_final = $nomeFinalArquivo.".".$extensao;
            } else
            {
                // Mantém o nome original do arquivo
                $nome_final = $_FILES[$nameInputForm]['name'][0];
            }

            // Verifica se o arquivo já existe.
            // if ( file_exists( $upload['pasta'] . $nome_final ) ) {
            //  // echo "O arquivo $nome_final já existe no local de destino.";
            //  exit;
            // }

            // Depois verifica se é possível mover o arquivo para a pasta escolhida
            if ( move_uploaded_file( $_FILES[$nameInputForm]['tmp_name'][0], $upload['pasta'] . $nome_final ) )
            {
                // Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
                // echo "Upload efetuado com sucesso!";
                // echo ' <a href="' . $upload['pasta'] . $nome_final . '">Clique aqui para acessar o arquivo</a> ';
            } else
            {
                // Não foi possível fazer o upload, provavelmente a pasta está incorreta
                // echo "Não foi possível enviar o arquivo, tente novamente";
            }
            $arrRetorno = array(
                'fezUpload' => true,
                'extensao'  => $extensao,
                'nomeFinal' => $nome_final,
                'url'       => $upload['pasta'] . $nome_final
            );
            return $arrRetorno;
        }else
        {
            // echo "Não foi possível enviar o arquivo, tente novamente";
            return false;
        }
        return false;
    }


    function hash_autoloader($class) {
        // Define an array of directories in the order of their priority to iterate through.
        $dirs = array(
            'includes/Model/', // Project specific classes - model (+Core Overrides)
            'includes/', // Project specific classes
            'vendor/', // vendor classes
            '../includes/Model/', // Project specific classes - model (+Core Overrides)
            '../includes/', // Project specific classes
            '../vendor/', // vendor classes
        );

        // Looping through each directory to load all the class files. It will only require a file once.
        // If it finds the same class in a directory later on, IT WILL IGNORE IT! Because of that require once!
        foreach( $dirs as $dir ) {
            if (file_exists($dir . $class . '.php')) {
                require_once($dir . $class . '.php');
                return;
            }

            if (file_exists($dir . $class . '/' . $class.'.php')) {
                require_once($dir . $class . '/' . $class.'.php');
                return;
            }

            if (is_file($file = $dir.str_replace(array('_', "\0"), array('/', ''), $class).'.php')) {
                require $file;
            }

        }

    }

    /**
     * para json não precisa enviar parametro nenhum. ele já retorna sucesso com msg salvo com sucesso.
     * @param  bool|false $success
     * @param string $msg
     * @param null $data
     */
    function parseJson($error = false, $msg = 'Salvo com sucesso!', $data = null)
    {
        echo json_encode(array('error' => $error, 'msg' => $msg, 'data' => $data));
        exit;
    }

    /**
     * quando for ajax, tem que retonar algum conteudo para a tela.
     * neste caso não precisa de msg nem dizer se tem erro ou não, simplesmente envia de voltar o html por padrão.
     * @param $html
     * @param string $title
     * @param array $data
     * @param null $msg
     * @param bool|true $success
     */
    function parseResponseModal($html, $title = 'modal', $data = array(), $msg = null, $error = false)
    {
        if (!$html) {
            parseJson(true, 'Modal sem conteúdo!');
        }

        $data['html']  = $html;
        $data['title'] = $title;

        parseJson($error, $msg, $data);
    }


    function parseResults($results,$pagina = false)
    {
	echo json_encode(array('results' => $results,'pagination' => array('more' => $pagina)));
        exit;
    }

    function parseJsonTarget($servico, $id = null)
    {
        parseJson(false, '', array('target' => array('servico' => $servico, 'id' => $id)));
    }

    require 'Dump.php';

    function x($d,$exit = true){
        $trace = debug_backtrace();

        echo '<center><Br>------------------------------<Br>';
        foreach($trace as $linha) {

            echo '| <strong>Linha</strong> : '.$linha['line'].' <strong>do Arquivo</strong> -> '.$linha['file'].' |<br>';

        }
        echo '------------------------------<Br></center>';
        echo '<Br><Br>';

        $x = new Global_Dump($d);
        if ($exit){
                     die('<h1>D.I.E.</h1>');
        }
    }
    /**
     * Debug X
     * @param $variavel
    */
    
    // function x( $mixExpression , $boolExit = TRUE , $boolFinish = NULL )
    // {

    //     static $arrMessages;
    //     if( ! $arrMessages )
    //     {
    //         $arrMessages = array();
    //     }

    //     if( $boolFinish )
    //     {
    //         return( implode( " <br/> " , $arrMessages ) );
    //     }

    //     $arrBacktrace = debug_backtrace();
    //     $strMessage = "";
    //     $strMessage .= "<div style=\"position:absolute; background-color:#fff;z-index:2;\" ><font color=\"#700000\" size=\"4\"><b>DEBUG</b></font><pre>" ;
    //     foreach( $arrBacktrace[0] as $strAttribute => $mixValue )
    //     {

    //         $strMessage .= "<b>" . $strAttribute . "</b> ". $mixValue ."\n";
    //     }
    //     $strMessage .= "<hr />";

    //     # Abre o buffer, impedindo que seja impresso na tela alguma coisa
    //     ob_start();
    //     var_dump( $mixExpression );
    //     # Pega todo o buffer
    //     $strMessage .= ob_get_clean();

    //     $strMessage .= "</pre></fieldset>";


    //     foreach( $arrMessages as $messages )
    //     {
    //         print $messages;
    //         ob_flush();
    //         flush();
    //     }
    //     print $strMessage.'</div>';
    //     print "<br /><font color=\"#700000\" size=\"4\"><b>D I E</b></font></div>";

    //     if( $boolExit )
    //     {
    //         exit();
    //     }
    // }

    /**
     * UpLoad para o S3 da Amazon
     *
     * @param array $file
     *
     */
    function amazonUpload( $file )
    {
        $access_key =   "744618135668:user/s3-user-hashws";//2º = "user/s3-user-hashws";//1º = "s3-user-hashws";
        $secret_key =   "WC}!mq%*sFHg";
        $base_url   =   "s3-sa-east-1.amazonaws.com";//"https://744618135668.signin.aws.amazon.com/console";
        $nome       =   UUID::v4();
        $bucket     =   "hashws";

        $ib     =   explode('_', key($file));
        $ext    =   explode('.', $file[ key($file) ]['name']);

        $nome   =   $nome . '.' . $ext[1];

        $clientS3 = Aws\S3\S3Client::factory(array(
                'key'    => $access_key,
                'secret' => $secret_key//,
                //      'base_url' => $base_url
        ));

        $response = $clientS3->putObject(array(
                'Bucket' => $bucket,
                'Key'    => $nome,
                'SourceFile' => $file[ key($file) ]['tmp_name'],
        ));
        x($response);
        return $bucket . '/' . $nome;
    }


    /**
     * UpLoad em disco
     *
     * @param array     $file
     * @param string    $grupo
     *
     */
    function localUpload( $file, $time, $grupo )
    {
        //$ext  =   explode('.', $file[ key($file) ]['name']);

        $ib     =   explode('_', key($file));
        $ext    =   explode('.', $file[ key($file) ]['name']);
        $nome   =   $ib[0] . '.' . $ext[1];
        $newFolder  =   'upload_dir/' . $time . '/';
        $retorno    =   $time . '/';
        if ( !file_exists($newFolder) ){
            mkdir( $newFolder, 0755 );
            }

        $newFolder  =   $newFolder . $grupo . '/';
        $retorno    =   $retorno . $grupo . '/';
        if ( !file_exists($newFolder) ){
            mkdir( $newFolder, 0755 );
        }
        move_uploaded_file($file[ key($file) ]['tmp_name'], $newFolder . $nome);

        return $retorno . $nome;
    }

    function getUrlContent($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($httpcode>=200 && $httpcode<300) ? $data : false;
    }

    function curlRequest($url, array $post = array())
    {
    	$ch = curl_init();

    	curl_setopt($ch,CURLOPT_URL, $url);

    	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.76 Safari/537.36');
    	curl_setopt($ch, CURLOPT_AUTOREFERER, true);

    	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie_file.txt");
    	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie_file.txt");

    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    	$a = curl_exec ($ch);
    	curl_close($ch);
    	return $a;
    }

class dBug {
    var $xmlDepth=array();
    var $xmlCData;
    var $xmlSData;
    var $xmlDData;
    var $xmlCount=0;
    var $xmlAttrib;
    var $xmlName;
    var $arrType=array("array","object","resource","boolean","NULL");
    var $bInitialized = false;
    var $bCollapsed = false;
    var $arrHistory = array();
    //constructor
    function dBug($var,$forceType="",$bCollapsed=false) {
        //include js and css scripts
        if(!defined('BDBUGINIT')) {
            define("BDBUGINIT", TRUE);
            $this->initJSandCSS();
        }
        $arrAccept=array("array","object","xml"); //array of variable types that can be "forced"
        $this->bCollapsed = $bCollapsed;
        if(in_array($forceType,$arrAccept))
            $this->{"varIs".ucfirst($forceType)}($var);
        else
            $this->checkType($var);
    }
    //get variable name
    function getVariableName() {
        $arrBacktrace = debug_backtrace();
        //possible 'included' functions
        $arrInclude = array("include","include_once","require","require_once");
        //check for any included/required files. if found, get array of the last included file (they contain the right line numbers)
        for($i=count($arrBacktrace)-1; $i>=0; $i--) {
            $arrCurrent = $arrBacktrace[$i];
            if(array_key_exists("function", $arrCurrent) &&
                (in_array($arrCurrent["function"], $arrInclude) || (0 != strcasecmp($arrCurrent["function"], "dbug"))))
                continue;
            $arrFile = $arrCurrent;
            break;
        }
        if(isset($arrFile)) {
            $arrLines = file($arrFile["file"]);
            $code = $arrLines[($arrFile["line"]-1)];
            //find call to dBug class
            preg_match('/\bnew dBug\s*\(\s*(.+)\s*\);/i', $code, $arrMatches);
            return $arrMatches[1];
        }
        return "";
    }
    //create the main table header
    function makeTableHeader($type,$header,$colspan=2) {
        if(!$this->bInitialized) {
            $header = $this->getVariableName() . " (" . $header . ")";
            $this->bInitialized = true;
        }
        $str_i = ($this->bCollapsed) ? "style=\"font-style:italic\" " : "";
        echo "<table cellspacing=2 cellpadding=3 class=\"dBug_".$type."\">
                <tr>
                    <td ".$str_i."class=\"dBug_".$type."Header\" colspan=".$colspan." onClick='dBug_toggleTable(this)'>".$header."</td>
                </tr>";
    }
    //create the table row header
    function makeTDHeader($type,$header) {
        $str_d = ($this->bCollapsed) ? " style=\"display:none\"" : "";
        echo "<tr".$str_d.">
                <td valign=\"top\" onClick='dBug_toggleRow(this)' class=\"dBug_".$type."Key\">".$header."</td>
                <td>";
    }
    //close table row
    function closeTDRow() {
        return "</td></tr>\n";
    }
    //error
    function  error($type) {
        $error="Error: Variable cannot be a";
        // this just checks if the type starts with a vowel or "x" and displays either "a" or "an"
        if(in_array(substr($type,0,1),array("a","e","i","o","u","x")))
            $error.="n";
        return ($error." ".$type." type");
    }
    //check variable type
    function checkType($var) {
        switch(gettype($var)) {
            case "resource":
                $this->varIsResource($var);
                break;
            case "object":
                $this->varIsObject($var);
                break;
            case "array":
                $this->varIsArray($var);
                break;
            case "NULL":
                $this->varIsNULL();
                break;
            case "boolean":
                $this->varIsBoolean($var);
                break;
            default:
                $var=($var=="") ? "[empty string]" : $var;
                echo "<table cellspacing=0><tr>\n<td>".$var."</td>\n</tr>\n</table>\n";
                break;
        }
    }
    //if variable is a NULL type
    function varIsNULL() {
        echo "NULL";
    }
    //if variable is a boolean type
    function varIsBoolean($var) {
        $var=($var==1) ? "TRUE" : "FALSE";
        echo $var;
    }
    //if variable is an array type
    function varIsArray($var) {
        $var_ser = serialize($var);
        array_push($this->arrHistory, $var_ser);
        $this->makeTableHeader("array","array");
        if(is_array($var)) {
            foreach($var as $key=>$value) {
                $this->makeTDHeader("array",$key);
                //check for recursion
                if(is_array($value)) {
                    $var_ser = serialize($value);
                    if(in_array($var_ser, $this->arrHistory, TRUE))
                        $value = "*RECURSION*";
                }
                if(in_array(gettype($value),$this->arrType))
                    $this->checkType($value);
                else {
                    $value=(trim($value)=="") ? "[empty string]" : $value;
                    echo $value;
                }
                echo $this->closeTDRow();
            }
        }
        else echo "<tr><td>".$this->error("array").$this->closeTDRow();
        array_pop($this->arrHistory);
        echo "</table>";
    }
    //if variable is an object type
    function varIsObject($var) {
        $var_ser = serialize($var);
        array_push($this->arrHistory, $var_ser);
        $this->makeTableHeader("object","object");
        if(is_object($var)) {
            $arrObjVars=get_object_vars($var);
            foreach($arrObjVars as $key=>$value) {
                $value=(!is_object($value) && !is_array($value) && trim($value)=="") ? "[empty string]" : $value;
                $this->makeTDHeader("object",$key);
                //check for recursion
                if(is_object($value)||is_array($value)) {
                    $var_ser = serialize($value);
                    if(in_array($var_ser, $this->arrHistory, TRUE)) {
                        $value = (is_object($value)) ? "*RECURSION* -> $".get_class($value) : "*RECURSION*";
                    }
                }
                if(in_array(gettype($value),$this->arrType))
                    $this->checkType($value);
                else echo $value;
                echo $this->closeTDRow();
            }
            $arrObjMethods=get_class_methods(get_class($var));
            foreach($arrObjMethods as $key=>$value) {
                $this->makeTDHeader("object",$value);
                echo "[function]".$this->closeTDRow();
            }
        }
        else echo "<tr><td>".$this->error("object").$this->closeTDRow();
        array_pop($this->arrHistory);
        echo "</table>";
    }
    //if variable is a resource type
    function varIsResource($var) {
        $this->makeTableHeader("resourceC","resource",1);
        echo "<tr>\n<td>\n";
        switch(get_resource_type($var)) {
            case "fbsql result":
            case "mssql result":
            case "msql query":
            case "pgsql result":
            case "sybase-db result":
            case "sybase-ct result":
            case "mysql result":
                $db=current(explode(" ",get_resource_type($var)));
                $this->varIsDBResource($var,$db);
                break;
            case "gd":
                $this->varIsGDResource($var);
                break;
            case "xml":
                $this->varIsXmlResource($var);
                break;
            default:
                echo get_resource_type($var).$this->closeTDRow();
                break;
        }
        echo $this->closeTDRow()."</table>\n";
    }
    //if variable is a database resource type
    function varIsDBResource($var,$db="mysql") {
        if($db == "pgsql")
            $db = "pg";
        if($db == "sybase-db" || $db == "sybase-ct")
            $db = "sybase";
        $arrFields = array("name","type","flags");
        $numrows=call_user_func($db."_num_rows",$var);
        $numfields=call_user_func($db."_num_fields",$var);
        $this->makeTableHeader("resource",$db." result",$numfields+1);
        echo "<tr><td class=\"dBug_resourceKey\">&nbsp;</td>";
        for($i=0;$i<$numfields;$i++) {
            $field_header = "";
            for($j=0; $j<count($arrFields); $j++) {
                $db_func = $db."_field_".$arrFields[$j];
                if(function_exists($db_func)) {
                    $fheader = call_user_func($db_func, $var, $i). " ";
                    if($j==0)
                        $field_name = $fheader;
                    else
                        $field_header .= $fheader;
                }
            }
            $field[$i]=call_user_func($db."_fetch_field",$var,$i);
            echo "<td class=\"dBug_resourceKey\" title=\"".$field_header."\">".$field_name."</td>";
        }
        echo "</tr>";
        for($i=0;$i<$numrows;$i++) {
            $row=call_user_func($db."_fetch_array",$var,constant(strtoupper($db)."_ASSOC"));
            echo "<tr>\n";
            echo "<td class=\"dBug_resourceKey\">".($i+1)."</td>";
            for($k=0;$k<$numfields;$k++) {
                $tempField=$field[$k]->name;
                $fieldrow=$row[($field[$k]->name)];
                $fieldrow=($fieldrow=="") ? "[empty string]" : $fieldrow;
                echo "<td>".$fieldrow."</td>\n";
            }
            echo "</tr>\n";
        }
        echo "</table>";
        if($numrows>0)
            call_user_func($db."_data_seek",$var,0);
    }
    //if variable is an image/gd resource type
    function varIsGDResource($var) {
        $this->makeTableHeader("resource","gd",2);
        $this->makeTDHeader("resource","Width");
        echo imagesx($var).$this->closeTDRow();
        $this->makeTDHeader("resource","Height");
        echo imagesy($var).$this->closeTDRow();
        $this->makeTDHeader("resource","Colors");
        echo imagecolorstotal($var).$this->closeTDRow();
        echo "</table>";
    }
    //if variable is an xml type
    function varIsXml($var) {
        $this->varIsXmlResource($var);
    }
    //if variable is an xml resource type
    function varIsXmlResource($var) {
        $xml_parser=xml_parser_create();
        xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
        xml_set_element_handler($xml_parser,array(&$this,"xmlStartElement"),array(&$this,"xmlEndElement"));
        xml_set_character_data_handler($xml_parser,array(&$this,"xmlCharacterData"));
        xml_set_default_handler($xml_parser,array(&$this,"xmlDefaultHandler"));
        $this->makeTableHeader("xml","xml document",2);
        $this->makeTDHeader("xml","xmlRoot");
        //attempt to open xml file
        $bFile=(!($fp=@fopen($var,"r"))) ? false : true;
        //read xml file
        if($bFile) {
            while($data=str_replace("\n","",fread($fp,4096)))
                $this->xmlParse($xml_parser,$data,feof($fp));
        }
        //if xml is not a file, attempt to read it as a string
        else {
            if(!is_string($var)) {
                echo $this->error("xml").$this->closeTDRow()."</table>\n";
                return;
            }
            $data=$var;
            $this->xmlParse($xml_parser,$data,1);
        }
        echo $this->closeTDRow()."</table>\n";
    }
    //parse xml
    function xmlParse($xml_parser,$data,$bFinal) {
        if (!xml_parse($xml_parser,$data,$bFinal)) {
            die(sprintf("XML error: %s at line %d\n",
                xml_error_string(xml_get_error_code($xml_parser)),
                xml_get_current_line_number($xml_parser)));
        }
    }
    //xml: inititiated when a start tag is encountered
    function xmlStartElement($parser,$name,$attribs) {
        $this->xmlAttrib[$this->xmlCount]=$attribs;
        $this->xmlName[$this->xmlCount]=$name;
        $this->xmlSData[$this->xmlCount]='$this->makeTableHeader("xml","xml element",2);';
        $this->xmlSData[$this->xmlCount].='$this->makeTDHeader("xml","xmlName");';
        $this->xmlSData[$this->xmlCount].='echo "<strong>'.$this->xmlName[$this->xmlCount].'</strong>".$this->closeTDRow();';
        $this->xmlSData[$this->xmlCount].='$this->makeTDHeader("xml","xmlAttributes");';
        if(count($attribs)>0)
            $this->xmlSData[$this->xmlCount].='$this->varIsArray($this->xmlAttrib['.$this->xmlCount.']);';
        else
            $this->xmlSData[$this->xmlCount].='echo "&nbsp;";';
        $this->xmlSData[$this->xmlCount].='echo $this->closeTDRow();';
        $this->xmlCount++;
    }
    //xml: initiated when an end tag is encountered
    function xmlEndElement($parser,$name) {
        for($i=0;$i<$this->xmlCount;$i++) {
            eval($this->xmlSData[$i]);
            $this->makeTDHeader("xml","xmlText");
            echo (!empty($this->xmlCData[$i])) ? $this->xmlCData[$i] : "&nbsp;";
            echo $this->closeTDRow();
            $this->makeTDHeader("xml","xmlComment");
            echo (!empty($this->xmlDData[$i])) ? $this->xmlDData[$i] : "&nbsp;";
            echo $this->closeTDRow();
            $this->makeTDHeader("xml","xmlChildren");
            unset($this->xmlCData[$i],$this->xmlDData[$i]);
        }
        echo $this->closeTDRow();
        echo "</table>";
        $this->xmlCount=0;
    }
    //xml: initiated when text between tags is encountered
    function xmlCharacterData($parser,$data) {
        $count=$this->xmlCount-1;
        if(!empty($this->xmlCData[$count]))
            $this->xmlCData[$count].=$data;
        else
            $this->xmlCData[$count]=$data;
    }
    //xml: initiated when a comment or other miscellaneous texts is encountered
    function xmlDefaultHandler($parser,$data) {
        //strip '<!--' and '-->' off comments
        $data=str_replace(array("&lt;!--","--&gt;"),"",htmlspecialchars($data));
        $count=$this->xmlCount-1;
        if(!empty($this->xmlDData[$count]))
            $this->xmlDData[$count].=$data;
        else
            $this->xmlDData[$count]=$data;
    }
    function initJSandCSS() {
        echo <<<SCRIPTS
            <script language="JavaScript">
            /* code modified from ColdFusion's cfdump code */
                function dBug_toggleRow(source) {
                    var target = (document.all) ? source.parentElement.cells[1] : source.parentNode.lastChild;
                    dBug_toggleTarget(target,dBug_toggleSource(source));
                }
                function dBug_toggleSource(source) {
                    if (source.style.fontStyle=='italic') {
                        source.style.fontStyle='normal';
                        source.title='click to collapse';
                        return 'open';
                    } else {
                        source.style.fontStyle='italic';
                        source.title='click to expand';
                        return 'closed';
                    }
                }
                function dBug_toggleTarget(target,switchToState) {
                    target.style.display = (switchToState=='open') ? '' : 'none';
                }
                function dBug_toggleTable(source) {
                    var switchToState=dBug_toggleSource(source);
                    if(document.all) {
                        var table=source.parentElement.parentElement;
                        for(var i=1;i<table.rows.length;i++) {
                            target=table.rows[i];
                            dBug_toggleTarget(target,switchToState);
                        }
                    }
                    else {
                        var table=source.parentNode.parentNode;
                        for (var i=1;i<table.childNodes.length;i++) {
                            target=table.childNodes[i];
                            if(target.style) {
                                dBug_toggleTarget(target,switchToState);
                            }
                        }
                    }
                }
            </script>
            <style type="text/css">
                table.dBug_array,table.dBug_object,table.dBug_resource,table.dBug_resourceC,table.dBug_xml
                    { font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; font-size:12px; border-spacing:2px; display:table; border-collapse:separate; }
                table.dBug_array td,
                table.dBug_object td,
                table.dBug_resource td,
                table.dBug_resourceC td,
                table.dBug_xml td
                    { line-height:1.3; padding:3px; vertical-align:top; }
                .dBug_arrayHeader,
                .dBug_objectHeader,
                .dBug_resourceHeader,
                .dBug_resourceCHeader,
                .dBug_xmlHeader
                    { font-weight:bold; color:#FFFFFF; cursor:pointer; }
                .dBug_arrayKey,
                .dBug_objectKey,
                .dBug_xmlKey
                    { cursor:pointer; }
                /* array */
                table.dBug_array { background-color:#006600; }
                table.dBug_array td { background-color:#FFFFFF; }
                table.dBug_array td.dBug_arrayHeader { background-color:#009900; }
                table.dBug_array td.dBug_arrayKey { background-color:#CCFFCC; }
                /* object */
                table.dBug_object { background-color:#0000CC; }
                table.dBug_object td { background-color:#FFFFFF; }
                table.dBug_object td.dBug_objectHeader { background-color:#4444CC; }
                table.dBug_object td.dBug_objectKey { background-color:#CCDDFF; }
                /* resource */
                table.dBug_resourceC { background-color:#884488; }
                table.dBug_resourceC td { background-color:#FFFFFF; }
                table.dBug_resourceC td.dBug_resourceCHeader { background-color:#AA66AA; }
                table.dBug_resourceC td.dBug_resourceCKey { background-color:#FFDDFF; }
                /* resource */
                table.dBug_resource { background-color:#884488; }
                table.dBug_resource td { background-color:#FFFFFF; }
                table.dBug_resource td.dBug_resourceHeader { background-color:#AA66AA; }
                table.dBug_resource td.dBug_resourceKey { background-color:#FFDDFF; }
                /* xml */
                table.dBug_xml { background-color:#888888; }
                table.dBug_xml td { background-color:#FFFFFF; }
                table.dBug_xml td.dBug_xmlHeader { background-color:#AAAAAA; }
                table.dBug_xml td.dBug_xmlKey { background-color:#DDDDDD; }
            </style>
SCRIPTS;
    }
}

