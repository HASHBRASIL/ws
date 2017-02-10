<?php
/**
 * HashWS
 */

/**
 * Campanha é um helper de notificação geral, basicamente, recebe uma mensagem
 * e um conjunto de remetentes e envia a eles a mensagem.
 *
 * Comportamentos disponíveis:
 * * Envio de e-mail.
 *
 * @author Maykel S. Braz
 */
class Controller_Campanha extends Zend_Controller_Action_Helper_Abstract
{
    public $pluginLoader;

    public function __construct(){
        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }

    /**
     * @var string[] Configurações do sendgrid.
     */
    protected $opcoesSg;

    /**
     *
     * @param type $option
     * @return type
     */
    protected function getOpcaoSg($opcao)
    {
        if (is_null($this->opcoesSg)){
            $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
            $this->opcoesSg = $config->getOption('mail');
        }

        return $this->opcoesSg['sendgrid'][$opcao];
    }

    protected function criaEmail(array $mensagem, array $destinatarios, $imagem = [])
    {
        $remetente = new SendGrid\Email(null, $this->getOpcaoSg('from'));
        $assunto = $mensagem['assunto'];
        $conteudo = new SendGrid\Content("text/html", $mensagem['mensagem']);

        $mail = new SendGrid\Mail(
            $remetente,
            $assunto,
            new SendGrid\Email(null, array_shift($destinatarios)),
            $conteudo
        );

        if ($imagem) {
            $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');
            if(!is_array($imagem)) {
                $imagem = array($imagem);
            }
            // $arrayImagem = explode("/",$imagem);
            foreach($imagem as $img) {
                //$path = getcwd() . "/transacional/001/imagens/" . $img;
                $filename = pathinfo($img, PATHINFO_FILENAME);
                $type = pathinfo($img, PATHINFO_EXTENSION);
                $data = file_get_contents($img);
                $base64 = base64_encode($data);
                $attachment = new SendGrid\Attachment();
                $attachment->setFilename($filename . '.' . $type);
                $attachment->setDisposition('inline');
                $attachment->setContent($base64);
                $attachment->setContentID($filename);
                $attachment->setType('image/' . $type );
                $mail->addAttachment($attachment);
            }
        }

        foreach ($destinatarios as $destinatario) {
            $para = new SendGrid\Email(null, $destinatario);
            $mail->personalization[0]->addTo($para);
        }

        //x($mail->jsonSerialize());

        return $mail;
    }

    public function enviarEmail(array $mensagem, $destinatarios, $imagem = [])
    {

        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $emailSuporte = $config->getOption('company')['emailSuporte'];
        if (!in_array($emailSuporte, $destinatarios)) {
            //$destinatarios[] = $emailSuporte;
        }

        $sg = new SendGrid($config->getOption('mail')['sendgrid']['apikey']);
        
        $response = $sg->client->mail()->send()->post($this->criaEmail(
            $mensagem,
            $destinatarios,
            $imagem
        ));

        
        //x($response);
        if (0 !== strpos($response->statusCode(), '2')) {
            echo $response->statusCode();
            echo $response->headers();
            echo $response->body();
            throw new Exception('impossivel_enviar_email');
        }
    }

    public function enviarSms($numero, $card, $msg, $msgid)
    {
        $card = urlencode($card);
        $numero = urlencode($numero);
        $msg = urlencode($msg);

        $smsserver = Zend_Controller_Action_HelperBroker::getStaticHelper('configuracao')
            ->getValorConfiguracao('filedir', 'smsserver');

        $url = "{$smsserver}cb/sms_http.php?msg={$msg}&number={$numero}"
            . "&send_to_sim={$card}&msg_id={$msgid}";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($ch);
        curl_close($ch);
    }

    /**
     *
     * @param type $mensagem
     * @param array $destinatarios
     * @todo Criar tipos complexos para mensagem, de forma a identificar o tipo da mensagem e acionar o comportamento associado à aquele tipo
     */
    public function direct(array $mensagem, array $destinatarios, $imagem = NULL)
    {
        $this->enviarEmail($mensagem, $destinatarios, $imagem);
    }
}

