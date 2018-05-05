<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE);

define('INIT_MICROTIME', microtime(true));

defined('DOCUMENT_ROOT')
|| define('DOCUMENT_ROOT', realpath($_SERVER['DOCUMENT_ROOT']));

defined('APP_PATH')
|| define('APP_PATH', DOCUMENT_ROOT . '/../app/');

try {
    include_once(DOCUMENT_ROOT . '/../app/bootstrap.php');
}
catch(\Exception $e) {
    ob_clean();
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

    $errorStack = "Exception: " . $e->getMessage() . "\n" .
        $e->getFile() . ":" . $e->getLine() . "\n" .
        "\n" .
        $e->getTraceAsString();

    echo $errorStack;
    exit();
}
