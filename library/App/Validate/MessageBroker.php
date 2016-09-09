<?php

class App_Validate_MessageBroker
{
    private $_storage;

    private static $_instance = null;

    /**
     *
     * @return App_Validate_MessageBroker
     */
    public static function getInstance(){
        if( self::$_instance == null ){
            self::$_instance = new App_Validate_MessageBroker();
        }
        return self::$_instance;
    }

    private function __construct(){
        $this->_storage = new Zend_Session_Namespace( 'systemMessages' );
        if( !isset( $this->_storage->messageList ) ){
            $this->_storage->messageList = array();
        }
    }

    public function addMessage( $message, $type = 'success' )
    {
        if( !isset( $this->_storage->messageList ) ){
            $this->_storage->messageList = array();
        }
        if( is_array( $message ) ){
            foreach( $message as $value ){
                $object = new stdClass();
                $object->type = $type;
                $object->text = $value;
                $this->_storage->messageList[] = $object;
            }
        }else{
            $object = new stdClass();
            $object->type = $type;
            $object->text = $message;
            $this->_storage->messageList[] = $object;
        }
        return $this;
    }

    public function getMessageList()
    {
        $messageList = $this->_storage->messageList;
        $this->_storage->messageList = array();
        return $messageList;
    }

    public function hasMessages(){
        return ( count( $this->_storage->messageList ) > 0 );
    }

    public static function addSuccessMessage( $message )
    {
        self::getInstance()->addMessage( $message );
    }

    public static function addValidationMessage( $message )
    {
        self::getInstance()->addMessage( $message, 'warning' );
    }

    public static function addErrorMessage( $message )
    {
        self::getInstance()->addMessage( $message, 'alert' );
    }

    public function hasMessagesByType( $type )
    {
        foreach( $this->_storage->messageList as $message ){
            if( $message->type == $type ){
                return true;
            }
        }
        return false;
    }

    public static function hasErrorMessages()
    {
        return self::getInstance()->hasMessagesByType( 'alert' );
    }

    public static function hasValidationMessages()
    {
        return self::getInstance()->hasMessagesByType( 'warning' );
    }
}