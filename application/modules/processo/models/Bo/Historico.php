<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  06/07/2013
 */
class Processo_Model_Bo_Historico extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_Historico
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_Historico();
        parent::__construct();
    }

    public function sendEmailDay()
    {
        $mail = new App_Util_Mail();
        $mail->setFrom('no-reply@agtic.com.br' , utf8_decode('ERP | Sistemas Integrados de Gestão Empresarial') )
        ->setSubject( utf8_decode(  "Histórico de processos" ) );

        $idProcessoList = $this->_dao->getIdAlteradoNow();
        $historicoList = array();
        $historicoArray = array();
        foreach ($idProcessoList as $idProcesso){
            $historicoArray[$idProcesso]    = $this->find(array('pro_id = ?' => $idProcesso), 'dt_criacao DESC');
            $historicoList[$idProcesso]     = $this->find(array('pro_id = ?' => $idProcesso), 'dt_criacao DESC');
        }
        $mailView = new Zend_View();
        $criteria = array('dt_criacao LIKE "2013-07-24%"');
        $mailView->historicoList      = $historicoList;
        $mailView->historicoArray     = $historicoArray;
        $mailView->idProcessoList     = $idProcessoList;
        $mailView->setScriptPath(APPLICATION_PATH.'/modules/processo/views/scripts/');
        $mailView->addHelperPath('App/Views/Helpers/');

        $html = $mailView->render( 'historico/send-email-day.phtml' );
        $mail->setBodyHtml( utf8_decode( $html ) );

        $userList = array('daviddesousam2@yahoo.com.br', 'ellysonbsb@gmail.com','ceo@grupoagbr.com.br');
        foreach( $userList as $email ){
            $mail->setBodyHtml( $html, 'UTF-8' );
            $mail->clearRecipients();
            $mail->addTo($email );
            $mail->send();
        }
    }

}