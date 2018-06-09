<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
$ru = $_SERVER['REQUEST_URI'];
$arr_url = parse_url($ru);
if (preg_match('/[A-Z]/', $arr_url['path'])) {
    $ru = strtolower($arr_url['path']);
    if(isset($arr_url['query']))$ru = $ru.'?'.$arr_url['query'];
    header("Location: $ru",TRUE,301);
    exit();
}
chdir(dirname(__DIR__));
// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}
// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
