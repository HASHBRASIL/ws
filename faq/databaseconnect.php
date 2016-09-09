<?php

class DatabaseConnection {

    private static $instance = null;

    private function __construct()
    {
        try {
            $dbh = new PDO( 'pgsql:host=hml.emandato.com;port=5432;dbname=hashws;user=hash;password=1fro1910+' );
            //echo "PDO connection object created";
        }
        catch( PDOException $e )
        {
            //echo $e->getMessage();
        }

        $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

        $this->_connection = $dbh;

    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __clone() { }

    public function getConnection() {
        return $this->_connection;
    }
}

$dbh = DatabaseConnection::getInstance()->getConnection();
