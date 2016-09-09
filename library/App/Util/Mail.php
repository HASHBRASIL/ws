<?php
class App_Util_Mail extends Zend_Mail
{

    /**
     * Application env válida para o envio de emails.
     * @var string
     */
    protected $_validApplicationEnv = 'production';

    /**
     * @param  Zend_Mail_Transport_Abstract $transport
     * @return App_Util_Mail                    Provides fluent interface
     */
    public function send($transport = null)
    {
        $config = Zend_Registry::get('config');
        //verifica se existe um transporte definido para o objeto
        //se não existir utiliza o tranporte da configuração
        if(empty($transport)) {
            //atualmente o sistema aceita somente o smtp como transporte
            $this->setTransportSmtp($config);
        }
        //Caso o application env não seja de um tipo
        //válido para envio os destinatários são resetados.
        if ($this->_validApplicationEnv != APPLICATION_ENV) {
            //Reseta destinatários se existir.
            if ($this->getRecipients()) {
                $this->clearRecipients();
            }
            //Resgata o config com os dados
            $config = Zend_Registry::get('config');
            //Verifica se foi informado os dados para email default.
            if ($this->hasValidDataDefault($config)) {
                //Seta o destinatário default.
                $this->addToByConfig($config);
            }
        }
        return parent::send($transport);
    }

    /**
     * Seta o default transport SMTP com as configurações do application.ini
     * @param Zend_Config $config
     * @return NULL
     */
    public function setTransportSmtp(Zend_Config $config)
    {
        if (empty($config->mail)
              || empty($config->mail->transport)
              || empty($config->mail->transport->type)
              || $config->mail->transport->type != "smtp")
        {
            return null;
        }
        //Dados de configurações
        $configTransport = array(
            "username" => $config->mail->transport->username,
            "password" => $config->mail->transport->password,
            "auth"     => $config->mail->transport->auth,
            "port"     => $config->mail->transport->port,
            "register" => $config->mail->transport->register,
            "ssl"      => $config->mail->transport->ssl
        );
        //Cria o objeto
        $transport = new Zend_Mail_Transport_Smtp($config->mail->transport->host,
                                                  $configTransport);
        self::setDefaultTransport($transport);
    }

    /**
     * @param Zend_Config $config
     * @return boolean
     */
    public function hasValidDataDefault($config)
    {
        return !empty($config->mail)
        && !empty($config->mail->default)
        && !empty($config->mail->default->email);
    }

    /**
     * @param Zend_Config $config
     * @return Core_Mail provides fluent interface
     */
    public function addToByConfig($config)
    {
        $recipient = $config->mail->default->email;
        if (!empty($config->mail->default->name)) {
            $recipient = array($config->mail->default->name => $config->mail->default->email);
        }
        $this->addTo($recipient);
        return $this;
    }

}