<?php

abstract class App_Controller_Action_Abstract extends Zend_Controller_Action
{
    protected $_translate;


    /**
     * Add error message and redirect
     * @param $msg
     * @param redirect String
     * @return void
     */
    protected function _addMessageError($msg, $redirect = null)
    {
        $this->_addMessage(array('error' => $msg), $redirect);
    }


    /**
     * Add success message and redirect
     * @param $msg
     * @param redirect String
     * @return void
     */
    protected function _addMessageSuccess($msg, $redirect = null)
    {
        $this->_addMessage(array('success' => $msg), $redirect);
    }

    /**
     * Add message and redirect
     * @param array $msg
     * @param redirect String
     * @return void
     */
    protected function _addMessage($msg, $redirect = null)
    {
        $this->_helper->_flashMessenger->addMessage(
            array(key($msg) => $this->_translate->translate(current($msg)))
        );

        if($redirect) {
            $this->_redirect($redirect);
        }
    }

    /**
     * @param  string $url
     * @param  array $options Options to be used when redirecting
     * @return void
     */
    protected function _redirect($url, array $options = array())
    {
        $messages = $this->_helper->_flashMessenger->getMessages();
        $this->_helper->_flashMessenger->clearMessages();
        foreach ($messages as $message) {
            $this->_helper->_flashMessenger->addMessage($message);
        }

        parent::redirect($url, $options);
    }

    /**
     * @return string[]
     */
    protected function _getUserRoles()
    {
        return array_flip((array) $this->getInvokeArg('bootstrap')->getContainer()->setting->role->toArray());
    }

    protected function _msg($sucesso,$msg) {
        $svcBo = new Config_Model_Bo_Servico();

        $target = "";
        if (isset($this->servico['ws_target']) && $this->servico['ws_target']) {
            $target = current($svcBo->getServicoByMetanome($this->servico['ws_target']))['id'];
        } else {
            $target = $this->servico['id_pai'];
        }

        $response = array(
            'success' => $sucesso,
            'msg' => $this->_translate->translate($msg),
            'data' => array('target' => array('servico' => $target))
        );
        $this->_helper->json($response);
    }
}