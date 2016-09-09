<?php
//desabilitando o limite de memória de processamento do servidor
ini_set( "memory_limit", -1 );
//desabilitando o limite do tempo de execução para geração de PDF`s grandes
ini_set( "max_execution_time", 0 );


/**
 * @desc Classe para gerar PDF`s utilizando a biblioteca TCPDF.
 * @author Ellyson de Jesus Silva
 */
class App_Util_Pdf{

    private $_pdf;
    const CRIACAO = "AGBR";
    const AUTOR   = "AGBR";
    const DIR_PUBLIC_IMAGES = "../../../../public/images/";
    const DIR_PUBLIC_UPLOAD = "../../../../public/uploads/logos/";
    const TITULO_CABECALHO  = "AGBR";

    private $_tituloCabecalho;
    private $_endereco;
    private $_telefone;
    private $_email;

    /**
     * @param $orientacao ( P=paisagem, L=landscape )
     * @param $unidade Unidade de medida
     * @param $layout = true remove o cabeçalho
     * @param $formato Tipo de Folha Utilizada para o PDF
     * @param $codificacao Tipo de codificação dos caracteres
     */
    public function __construct( $titulo = null, $corpo = null, $layout = null, $url_imagem = null, $url_imagem_direita = null, $orientacao = "P", $unidade = "mm", $formato = "A4", $codificacao = "UTF-8", $complementoCabecalho = null )
    {
    	$this->_pdf = new TCPDF ( $orientacao, $unidade, $formato, true, $codificacao );
        //retira a opção de renderização de fontes para a geração do PDF ser mais ágil
        $this->_pdf->setFontSubsetting ( false );
        //seta as configs de fonte
        $this->_pdf->SetDefaultMonospacedFont ( PDF_FONT_MONOSPACED );
        $this->modificarFonte();

        //se $layout for igual a true ele ira remover o cabeçalho
        if ($layout == true) {

        	//seta o espaçamento padrão do documento
        	//$this->_pdf->SetMargins ( 10, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
        	$this->_pdf->SetMargins(7, 4, 7, 4, true);

        	//seta a quebra de página
        	$this->_pdf->SetAutoPageBreak ( TRUE, 0 );

        	//propriedades de imagem
        	$this->_pdf->setImageScale ( PDF_IMAGE_SCALE_RATIO );

        } else {

	        //seta o espaçamento padrão do documento
	        //$this->_pdf->SetMargins ( 10, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
	        $this->_pdf->SetMargins(7, 30, 7, true);

	        //seta a quebra de página
	        $this->_pdf->SetAutoPageBreak ( TRUE, 10 );

	        //propriedades de imagem
	        $this->_pdf->setImageScale ( PDF_IMAGE_SCALE_RATIO );

	        //palavras-chave
	        $this->modificarPalavrasChave();

	        //adicionar o título padrão
	        $this->modificarTitulo();

	        $this->setCabecalho();
	        //modifica o cabeçalho do PDF com titulo e imagens
	        $this->modificarCabecalho($titulo, $corpo, $url_imagem, $url_imagem_direita, $complementoCabecalho);

	        //seta o rodapé do documento
	        $this->modificarRodape();
        }

        $this->_pdf->AddPage();
    }

    public function debugPdf()
    {
        App_Util_Functions::debug($this->_pdf);
    }

    public static function getBase64Imagem( $pathImagem, $dataUrl = true )
    {
    	$prefix = '';
    	if ($dataUrl) {
    		$prefix = 'data:image/png;base64,';
    	}
    	return $prefix . base64_encode( file_get_contents( getcwd().$pathImagem ) );
    }

    public function modificarCriador( $nome = self::CRIACAO ){
    	$this->_pdf->SetCreator($nome);
    }

    //TODO: MODIFICAR os parametros $url_imagem e $url_imagem_direita para que o PDF consiga abrir imagens dentro do projeto
    //Atualmente a TCPDF só está abrindo imagens que estão no diretório raiz de imagens dela.
    //que fica em tcpdf/images
    public function modificarCabecalho( $titulo_cabecalho, $corpo_cabecalho = null, $url_imagem = null , $url_imagem_direita = null, $complementoCabecalho = null ){

        if ( empty( $titulo_cabecalho ) )
            $titulo_cabecalho = $this->_tituloCabecalho;

        if ( empty( $corpo_cabecalho ) )
            $corpo_cabecalho = $this->_endereco."\n".$this->_telefone." - ".$this->_email;

        if($url_imagem){
            $url_imagem = App_Util_Pdf::DIR_PUBLIC_IMAGES.$url_imagem;
        }else{
            $url_imagem = App_Util_Pdf::DIR_PUBLIC_IMAGES."logo4-01.jpg";
            $proprietarioSession = new Zend_Session_Namespace('proprietario');

            if($proprietarioSession->proprietario && $proprietarioSession->proprietario->logo_report){
                $url_imagem = App_Util_Pdf::DIR_PUBLIC_UPLOAD.$proprietarioSession->proprietario->logo_report;
            }
        }
        $this->_pdf->SetHeaderData ( $url_imagem, 32,
    		                         $titulo_cabecalho , $corpo_cabecalho.$complementoCabecalho );
    	$this->_pdf->setHeaderFont ( Array (PDF_FONT_NAME_MAIN, '', 11 ) );
    	$this->_pdf->SetHeaderMargin ( 7 );
    }

    private function modificarRodape(){
    	$this->_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    	$this->_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    }

    /**
     * @desc Realiza a abertura de uma arquivo
     *
     * @param $nome_arquivo (string)
     * @param $formaAbertura (string)
     *    As formas de abertura são:
     *         - I: Realiza a abertura diretamente no browser.
     *         - D: Envia para o browser e força o download do arquivo com o nome que está no parametro $nome_arquivo
     *         - F: Salva no próprio servidor.
     *         - S: Retorna o Arquivo como uma string.
     *         - FI: equivalente a opção F + I.
     *         - FD: equivalente a opção F + D.
     *         - E: Retorna o documento como um base64 para envio como anexo em emails.
     */
    public function abrirArquivo( $nome_arquivo = "teste.pdf", $formaAbertura = "I" ){
    	$this->_pdf->lastPage();
    	return $this->_pdf->Output ( $nome_arquivo, $formaAbertura );
    }


    /**
     * @desc Modifica a fonte atual do PDF
     * @param $tipo Fonte do PDF
     *
     */
    public function modificarFonte( $tipo = 'helvetica', $tamanho = 12 ){
    	$this->_pdf->SetFont ( $tipo, '', $tamanho, '', true );
    }

    /**
     * @desc Modifica o título do PDF
     * @param $titulo string
     */
    public function modificarTitulo( $titulo = ""){
        if( empty( $titulo ) ){
            $titulo = "Relatório - " . date( "d/m/Y H:i:s" );
        }
        $this->_pdf->SetTitle( $titulo );
    }

    /**
     * @desc Modifica o assunto do PDF
     * @param $assunto string
     */
    public function modificarAssunto( $assunto = ""){
        if( !empty($assunto) )
            $this->_pdf->SetSubject ( $assunto );
    }

    /**
     * @desc Inclui palavras chave no PDF
     * As palavras devem ser separadas por ,
     *
     * @example
     *  $this->_pdf->palavrasChave('TCPDF, PDF, example, test, guide');
     *
     * @param $palavras_chave string separada por vírgula
     */
    public function modificarPalavrasChave( $palavras_chave = "Relatorio" ){
        if( !empty( $palavras_chave ) )
            $this->_pdf->SetKeywords ( $palavras_chave );
    }

    /**
     * @desc Adiciona um bloco HTML na página do PDF
     *
     * @example
     *  $this->_pdf->adicionarHtml('<h1>teste</h1>');
     *
     * @param $html string
     */
    public function adicionarHtml( $html = ""){

    	if( !empty( $html ) ){
            $this->_pdf->writeHTML( $html, true, false, true, false, '' );
    	}
    }

    /**
     * @desc Inclui uma tabela no PDF.
     * @TODO: CONTINUAR AQUI!!!!!
     */
    public function incluirTabela( $cabecalho = null, $conteudo_tabela, $tamanhos = null ){

    	//verifica se o conteúdo está vazio
    	if( empty( $conteudo_tabela ) ){
    		echo "Aviso = É necessário que a tabela tenha conteúdo.";
    		exit;
    	}

    	//verifica se o cabeçalho e o conteúdo tem a mesma quantidade de colunas
    	if( !empty($cabecalho) ){
    		if( count( $cabecalho ) != count( $conteudo_tabela[0] ) ){
    			echo "Aviso = A quantidade de colunas no cabeçalho deve ser o mesmo do conteúdo da tabela.";
    			exit;
    		}
    	}

    	//Column titles
    	$header = array('Country', 'Capital', 'Area (sq km)', 'Pop. (thousands)');
    	$border = 'border-right-color:#C1CDCD;border-color:#C1CDCD;border-left-color:#C1CDCD;border-bottom-color:#C1CDCD;';
    	$html = '

    	<style>

    	table tr th{
    	font-weight: bold;
    	}

    	</style>

    	<table  cellpadding="1" cellspacing="2" style="font-size:32px; text-align:left; border-width: 1px;border-collapse:collapse;'. $border .'">';

    	$html .= '  <tr style="background-color:#E4E9F2; color:#144d91; ">';

    	//Colocando o cabeçalho através do array passado pelo parametro da função
    	foreach ( $cabecalho as $indice => $nome ){
    	    //Verifica se o array tamanhos veio preenchido e o coloca no width
    	    if ( isset( $tamanhos[$indice] ) )
    	        $width =  'width = "' . $tamanhos[$indice] . '"';
    	    else
    	        $width = '';

    	    $html .= '<th '. $width .'>' . $nome .  '</th>';
    	}
    	$html .= '</tr>';

        //Conteúdo da tabela vindo de uma matrix
    	foreach ( $conteudo_tabela as $chave => $nome ){

    	        $zebra = "#eeeeee";
    	    if( $chave  % 2 == 0){
        	    $zebra = "#ffffff";
    	    }

    	    $html .= '<tr  style="background-color:' . $zebra . '" >';
    	    foreach ( $nome as $indice => $conteudo ){
    	        //Verifica se o array tamanhos veio preenchido e o coloca no width do td correspondente
    	        if ( isset( $tamanhos[$indice] ) )
    	            $width =  'width = "' . $tamanhos[$indice] . '"';
    	        else
    	            $width = '';

    	        $html .= '<td '. $width .'>' . $conteudo . '</td>';
    	    }
    	    $html .= '</tr>';
    	}

    	$html .= "</table>";

    	$this->adicionarHtml($html);

    }

    /**

     * @desc Inclui uma listagem no PDF
     * Formada por um título e vários UL > LI

     */

    public function incluirListagem( $titulo_listagem = null, $lista ){

    	if( empty( $lista ) ){
    		echo "Aviso = A lista está vazia. No mínimo um item é obrigatório";
    		exit;
    	}
        if ( !empty( $titulo_listagem ) )
    	    $html = '<h5>'. $titulo_listagem .' </h5><p><ul style="font-size:32px;">';
        else
            $html = '<ul style="font-size:32px;">';

    	foreach ( $lista as $conteudo ){
    	    $html .= '<li>'. $conteudo .'</li>';
    	}
    	$html .= '</ul> ';

    	$this->adicionarHtml($html);
    }


    /**

    * @desc Inclui uma listagem no PDF
    * Formada por um título e vários UL > LI

    */

    public function incluirTitulo( $titulo, $br = false  ){

        if( empty( $titulo ) ){
            echo "Aviso = O título está vazio.";
            exit;
        }
        $style = "";
        if ( $br )
            $style = "<br />";


        $html = "<h4 style='text-decoration: underline;'>{$titulo}</h4>{$style}";

        $this->adicionarHtml($html);
    }
    /**

    * @desc Inclui item no PDF
    */

    public function incluirItem( $tituloItem = null, $item, $br = false, $sem_dois_pontos = false){

        if( is_null( $item ) ){
            echo "Aviso =  O(s) $tipo_item estão vazio. No mínimo um $tipo_item é obrigatório. <br /> Local: $tituloItem";
            exit;
        }

        $divisao_dois_pontos = " :";
        if( $sem_dois_pontos )
        	$divisao_dois_pontos = "";

        $html = '<strong style="font-size:32px;">'. $tituloItem . $divisao_dois_pontos .'  </strong>';

        $html .= '<span style="font-size:32px;">'. $item .'</span>';

        if ( $br )
            $html .= "<br />";
        $this->adicionarHtml($html);
    }

    /**

    * @desc Inclui uma listagemSimples no PDF
    * Formada por um título e vários UL > LI

    */

    public function incluirListagemSimples( $titulo_listagem = null, $lista ){

        if( empty( $lista ) ){
            echo "Aviso = A lista está vazia. No mínimo um item é obrigatório";
            exit;
        }
          if ( !empty( $titulo_listagem ) )
            $html = '<h5 style="text-decoration: underline;" >'. $titulo_listagem .'</h5><p><ul style="font-size:32px;">';
          else
            $html = '<ul style="font-size:32px;">';


        foreach ( $lista as $conteudo ){
            $html .=  $conteudo ;
        }
        $html .= '</ul><br />';

        $this->adicionarHtml($html);
    }

    public function getTitulo()
    {
        return $this->_tituloCabecalho;
    }

    public function setTitulo($titulo)
    {
        $this->_tituloCabecalho = $titulo;
        return $this;
    }

    public function getEndereco()
    {
        return $this->_endereco;
    }

    public function setEndereco($endereco)
    {
        $this->_endereco = $endereco;
        return $this;
    }


    public function getTelefone()
    {
        return $this->_telefone;
    }

    public function setTelefone($telefone)
    {
        $this->_telefone = $telefone;
        return $this;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function setEmail($email)
    {
        $this->_email = $email;
        return $this;
    }

    public function setCabecalho()
    {
        $config				= Zend_Registry::get('config');
        $proprietarioSession = new Zend_Session_Namespace('proprietario');

        if(isset($proprietarioSession->proprietario['nome_empresa']) && !empty($proprietarioSession->proprietario['nome_empresa'])){
        	$this->_tituloCabecalho			= $proprietarioSession->proprietario['nome_empresa'];
        }else{
        	$this->_tituloCabecalho         = $config->company ? $config->company->nomeEmpresa : null;
        }

        if(isset($proprietarioSession->proprietario['end_proprietario']) && !empty($proprietarioSession->proprietario['end_proprietario']) ){
        	$this->_endereco 				= $proprietarioSession->proprietario['end_proprietario'];
        }else{
        	$this->_endereco                = $config->company ? $config->company->enderecoEmpresa: null;
        }

        if(isset($proprietarioSession->proprietario['telefone']) && !empty($proprietarioSession->proprietario['telefone']) ){
        	$this->_telefone				= $proprietarioSession->proprietario['telefone'];
        }else{
        	$this->_telefone                = $config->company ? $config->company->telEmpresa: null;
        }

        if(isset($proprietarioSession->proprietario['email']) && !empty($proprietarioSession->proprietario['email']) ){
        	$this->_email					= $proprietarioSession->proprietario['email'];
        }else{
        	$this->_email                   = $config->company ? $config->company->emailEmpresa : null;
        }

        return $this;
    }
    
    public function pagBreak(){
    	$this->_pdf->SetAutoPageBreak(TRUE, 0);
    	$this->_pdf->addPage(false);
    	return $this;
    }
}