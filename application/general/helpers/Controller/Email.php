<?php

class Controller_Email extends Zend_Controller_Action_Helper_Abstract {

    private $remetente = 'suporte@titaniumtech.com.br';

    public function __construct() {
        $this->pluginLoader = new Zend_Loader_PluginLoader ();
    }


    public function sendEmail($email, $subject, $content)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { return false; }

        $headers = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= 'From: <'.$this->remetente.'>\n';
        //$headers .= 'From: '.$this->remetente."\r\n";

        //return mail($email, $subject, addslashes($content), $headers, '-'.$this->remetente);
        return mail($email, $subject, addslashes($content), $headers);
    }

    public function sendEmailMailer($emailDestinatario, $assunto, $conteudo, $nome = 'TTech', $imgs = [])
    {
        if (!filter_var($emailDestinatario, FILTER_VALIDATE_EMAIL)) { return false; }

        $mailer = new PHPMailer();
        $mailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mailer->IsSMTP();  // Define que a mensagem ser� SMTP
        $mailer->Port       = 587; //Indica a porta de conex�o para a sa�da de e-mails
        $mailer->SMTPSecure = 'tls';
        $mailer->Subject    = $assunto;
        $mailer->Host       = "mail.hash.ws"; // Endere�o do servidor SMTP
        $mailer->SMTPAuth   = true; //define se haver� ou n�o autentica��o no SMTP
        $mailer->Username   = $this->remetente; // Usu�rio do servidor SMTP
        $mailer->Password   = 'titanium2015#$@'; // Senha do servidor SMTP
        $mailer->FromName   = $nome; //Nome que ser� exibido para o destinat�rio
        $mailer->From       = $this->remetente; //Obrigat�rio ser a mesma caixa postal indicada em "username"
//        $mailer->SMTPDebug  = 2;

        $mailer->SetLanguage("br", "phpMailer/language/");
        $mailer->IsHTML(true); // Define que o e-mail ser� enviado como HTML
        $mailer->CharSet = 'UTF-8'; // Charset da mensagem (opcional)

        $body = '<html><head><title></title></head><body>'. $conteudo .'</body></html>';

        $mailer->Body = $body;

        foreach($imgs as $cid => $img) {
            $mailer->AddEmbeddedImage($img, $cid);
        }

        $mailer->AddAddress($emailDestinatario, $assunto);

        if($mailer->Send()){ return true; }else{ return false; }
    }
}
