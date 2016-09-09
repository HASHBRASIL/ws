<?php
error_reporting(E_ALL & ~E_NOTICE);

 header('Content-Type: text/html; charset=utf-8',true);
//exit("Sistema em manutenção");

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once realpath(APPLICATION_PATH . '/../vendor/autoload.php');

require_once "../includes/functions.php";
spl_autoload_register('hash_autoloader');

/** Zend_Application */
require_once 'Zend/Application.php';

/* Bliblioteca TCPDF*/
require_once'tcpdf/config/lang/eng.php';
//require_once'tcpdf/tcpdf.php';

include_once('class/tcpdf/tcpdf.php');
require_once 'class/PHPJasperXML.inc.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();
